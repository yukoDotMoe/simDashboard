<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Service;
use App\Models\Sims;
use App\Services\BalanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;

class CheckSimLock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sim:isLock {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if sim database died';

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
        $typeOtp = $this->argument('type');
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        try {
            $start = microtime(true);
            // Check for used
            $out->writeln('[✨] Starting cronjob for checking services validiton. Type: ' . $typeOtp);
            switch ($typeOtp) {
                case 1:
                    $usedServices = Service::where([
                        ['status', 1],
                        ['used', '>', 0]    
                    ])->get();
                    break;
                    
                case 2:
                    $usedServices = Service::where([
                        ['status', 1],
                        ['success', '>', 0]    
                    ])->get();
                    break;
                    
                case 3:
                    case 2:
                    $usedServices = Service::where([
                        ['status', 1],
                        ['fail', '>', 0]    
                    ])->get();
                    break;
                
                default:
                    $usedServices = Service::where([
                        ['status', 1],
                        ['used', '>', 0]    
                    ])->get();
                    break;
            }
            if(count($usedServices) == 0)
            {
                $out->writeln('[🧨] Aborted job, cause no services is need to execute.');
                return 0;
            }
            
            $out->writeln('[🎉] Found ' . count($usedServices) . ' services that have limit with type ' . $typeOtp);
            
            foreach ($usedServices as $used)
            {
                $out->writeln('-----------------------');
                $out->writeln('[🔎] Searching for numbers that associated with service: ' . $used->uniqueId);
                $out->writeln('-----------------------');
                switch ($typeOtp) {
                    case 1:
                        $useCount = DB::table('sim_activities')->where([
                            ['serviceId', $used->uniqueId],
                            ['deleted_at', NULL]
                        ])->select('phoneNumber', DB::raw('COUNT(*) as `count`'))
                        ->groupBy('phoneNumber')
                        ->having('count', '>', 1)
                        ->get();
                        break;
                        
                    case 2:
                        $useCount = DB::table('success_records')->where([
                            ['serviceId', $used->uniqueId],
                            ['deleted_at', NULL]
                        ])->select('phone', DB::raw('COUNT(*) as `count`'))
                        ->groupBy('phone')
                        ->having('count', '>', 1)
                        ->get();
                        break;
                        
                    case 3:
                        $useCount = DB::table('failed_records')->where([
                            ['serviceId', $used->uniqueId],
                            ['deleted_at', NULL]
                        ])->select('phone', DB::raw('COUNT(*) as `count`'))
                        ->groupBy('phone')
                        ->having('count', '>', 1)
                        ->get();
                        break;
                    
                    default:
                        $useCount = DB::table('sim_activities')->where([
                            ['serviceId', $used->uniqueId],
                            ['deleted_at', NULL]
                        ])->select('phoneNumber', DB::raw('COUNT(*) as `count`'))
                        ->groupBy('phoneNumber')
                        ->having('count', '>', 1)
                        ->get();
                        break;
                }
                if(count($useCount) == 0)
                {
                    $out->writeln('[🧨] Aborted job, cause no numbers is need to execute.');
                    return 0;
                }
                $out->writeln('[🎫] Found ' . count($useCount) . ' phone numbers that associated with type ' . $typeOtp);
                foreach ($useCount as $filtered)
                {
                    if($filtered->count >= $used->limit)
                    {
                        switch ($typeOtp) {
                            case 1:
                                $sim = Sims::where('phone', $filtered->phoneNumber)->first();
                                $phone = $filtered->phoneNumber;
                                break;
                                
                            case 2:
                                $sim = Sims::where('phone', $filtered->phone)->first();
                                $phone = $filtered->phone;
                                break;
                                
                            case 3:
                                $sim = Sims::where('phone', $filtered->phone)->first();
                                $phone = $filtered->phone;
                                break;
                            
                            default:
                                $sim = Sims::where('phone', $filtered->phoneNumber)->first();
                                $phone = $filtered->phone;
                                break;
                        }
                        if(!empty($sim))
                        {
                            $startSim = microtime(true);
                            $out->writeln('--------');
                            $out->writeln('[🔎] Currently proccessing number: ' . $sim->phone);
                            $locked = json_decode($sim->locked_services, true);
                            
                            if(array_key_exists($used->uniqueId, $locked ?? []))
                            {
                                $out->writeln('[🔫] Found service ' . $used->uniqueId . ' exist in the locked array, checking date is valid..');
                                if (Carbon::parse($locked[$used->uniqueId]['cooldown'])->lte(Carbon::now()))
                                {
                                    $out->writeln('[🎇] Remove lock out of locked array.');
                                    $this->deleteAllInTable($used->uniqueId, $phone, $typeOtp);
                                    unset($locked[$used->uniqueId]);
                                }else{
                                    $out->writeln('[🎐] Aborted due to still cooldown, ' . gmdate('H:i:s', Carbon::now()->diffInSeconds(Carbon::parse($locked[$used->uniqueId]['cooldown']))) . ' left.');
                                }
                            }else{
                                $out->writeln('[❎]  Not found service in the locked array, create a new one.' );
                                $locked[$used->uniqueId] = [
                                    'name' => $used->serviceName,
                                    'type' => $typeOtp,
                                    'cooldown' => Carbon::now()->addHours($used->cooldown)
                                ];
                            }
                            
                            $sim->locked_services = json_encode($locked);
                            $sim->save();
                            $endSim = microtime(true);
                            
                            $out->writeln('[🎊] Number processed in ' . number_format(($endSim - $startSim), 2) . ' seconds');
                        }
                        
                    }
                }
            }
            
            $end = microtime(true);
            $out->writeln('-----------------------');
            $out->writeln("[🎇] Job finished in " . number_format(($end - $start), 2) . ' seconds');
            return 1;
            
            
        } catch (Exception $e)
        {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            $out->writeln("[!] Error occurred at line '" . $e->getLine() . "'. Please fix.");
            return 0;
        }
    }
    
    
    protected function deleteAllInTable($service, $phone, $type)
    {
        switch ($type) {
            case 'use':
                DB::table('sim_activities')->where([
                    ['phoneNumber', $phone],
                    ['serviceId', $service],
                    ['deleted_at', NULL]
                ])->update(['deleted_at' => Carbon::now()]);
                break;
                
            case 'success':
                DB::table('success_records')->where([
                    ['phone', $phone],
                    ['serviceId', $service],
                    ['deleted_at', NULL]
                ])->update(['deleted_at' => Carbon::now()]);
                break;
                
            case 'fail':
                DB::table('failed_records')->where([
                    ['phone', $phone],
                    ['serviceId', $service],
                    ['deleted_at', NULL]
                ])->update(['deleted_at' => Carbon::now()]);
                break;
            
            default:
                DB::table('sim_activities')->where([
                    ['phoneNumber', $phone],
                    ['serviceId', $service],
                    ['deleted_at', NULL]
                ])->update(['deleted_at' => Carbon::now()]);
                break;
        }
    }
}
