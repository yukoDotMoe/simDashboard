<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Sims;
use App\Services\BalanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use DB;

class CheckSimAlive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sim:isDead';

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
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $data = Sims::where([
                ['updated_at', '<', Carbon::now()->subMinutes(3)],
                ['status', 1]
            ])->update(['status' => 0]);
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Total: ' . $data . ' sims status has been changed to Maintained state');
            return 1;
        } catch (Exception $e)
        {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return 0;
        }
    }
}
