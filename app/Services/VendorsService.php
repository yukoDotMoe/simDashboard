<?php


namespace App\Services;


use App\Models\Activity;
use App\Models\Sims;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Log;
use Exception;

class VendorsService
{
    public function dashboardView($startDate, $endDate)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            // TODO: change this to handleByVendor #vendors.dashboard
            // and style the view for the dashboard too (´• ω •`) ♡
            $jobs = Activity::where([
                ['userID', Auth::user()->id],
                ['status', 1]
            ])
                ->whereDate('created_at','<=',$end)
                ->whereDate('created_at','>=',$start)
                ->get();

            $transactions = DB::table('vendors_balance')->where('vendorID', Auth::user()->id)
                ->whereDate('created_at','<=',$end)
                ->whereDate('created_at','>=',$start)
                ->get();
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => [
                    'requests' => $jobs ?? [],
                    'transactions' => $transactions ?? []
                ]
            ];
        } catch (Exception $e)
        {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return [
                'status' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    public function simsListView($startDate, $endDate)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            $sims = Sims::where('userId', Auth::user()->id)
                ->whereDate('created_at','<=',$end)
                ->whereDate('created_at','>=',$start)
                ->get();

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');

            return [
                'status' => 1,
                'data' => $sims ?? []
            ];
        } catch (Exception $e)
        {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return [
                'status' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
}