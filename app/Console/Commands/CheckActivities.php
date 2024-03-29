<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Balance;
use App\Models\Sims;
use App\Models\User;
use App\Services\SimsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;
use Pusher\Pusher;

class CheckActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'request:isValid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        try {
            $activities = Activity::where([
                ['updated_at', '<', Carbon::now()->subMinutes(env('DEFAULT_SMS_WAIT') ?? 5)],
                ['status', 2]
            ])->get();
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . count($activities));
            $out->writeln("[*] Starting checking all working jobs - Found " . count($activities) . " jobs");

            foreach ($activities as $activity) {
                $transaction = Balance::where('activityId', $activity['uniqueId'])->first();

                if(!$transaction)
                {
                    $out->writeln("[!] Transaction not found. Aborted. " . $activity['uniqueId'] . ' - ' . $activity['updated_at']);
                    continue;
                }

                $user = User::where('id', $transaction['accountId'])->first();
                
                $phone = Sims::where([
                    ['phone', $activity['phone']],
                    ['status', 2]
                ])->first();
                if(empty($phone))
                {
                    Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Sim not valid');
                    $out->writeln("[!] Number '" . $activity['phone'] . "' not valid. Aborted...");
                }else{
                    if ($phone['status'] == 2)
                    {
                        SimsService::addSimResult($phone['phone'], $activity['serviceId'], $activity['uniqueId'], 0, $phone['userid'], 'Timeout');
                        $updatePhone = Sims::where('uniqueId', $phone['uniqueId'])->update(['status' => 1, 'failed' => $phone['failed']+1]); // Make phone available
                        SimsService::checkLock($phone['uniqueId'], $activity['serviceId']);
                        $out->writeln("[%] Number '" . $activity['phone'] . "' changed to available");
                    }
                }

                DB::beginTransaction();
                $activityUpdate = Activity::where('uniqueId', $transaction['activityId'])->update(['status' => 0, 'reason' => 'Failed due to timeout']);
                Balance::insert([
                    'uniqueId' => substr(uniqid().date("sihdmY"),6,10),
                    'accountId' => $transaction['accountId'],
                    'oldBalance' => $user->balance,
                    'newBalance' => $user->balance + $transaction['totalChange'],
                    'totalChange' => $transaction['totalChange'],
                    'status' => 0, // 2: hold ; 1: success
                    'reason' => 'Refunded',
                    'activityId' => $activity['uniqueId'],
                    'type' => '+',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $user->balance = $user->balance + $transaction['totalChange'];
                $user->save();

                DB::commit();
                if(!$activityUpdate)
                {
                    DB::rollBack();
                    Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Failed to update request');
                    $out->writeln("[!] Sim '" . $activity['phone'] . "' Failed to update job. Aborted...");
                    return 0;
                }

                $transaction->status = 1;
                $transaction->save();
                $data = [
                    'uniqueId' => $activity['uniqueId'],
                    'status' => 0
                ];

                $options = array(
                    'cluster' => 'ap1',
                    'encrypted' => true
                );

                $pusher = new Pusher(
                    env('PUSHER_APP_KEY'),
                    env('PUSHER_APP_SECRET'),
                    env('PUSHER_APP_ID'),
                    $options
                );

                Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
                $metadataRequest = json_decode($activity['metadata'], true);
                if (isset($metadataRequest['isApi'])) {
                    if ( !$metadataRequest['isApi']) $pusher->trigger('user-flow.' . $transaction['accountId'], 'simFailed', $data);
                }
                
                Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Finish Closing request - ' . $activity['uniqueId']);
                $out->writeln("[%] Finished update job ID '" . $activity['uniqueId'] . "'.");
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return 1;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            $out->writeln("[!] Error occurred at line '" . $e->getLine() . "'. Please fix.");
            return 0;
        }
    }
}
