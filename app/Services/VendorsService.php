<?php


namespace App\Services;


use App\Models\Activity;
use App\Models\Service;
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
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . $startDate . ' - ' . $endDate);
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            $jobs = Activity::where([
                ['handleByVendor', Auth::user()->id],
                ['status', 1]
            ])
                ->whereDate('created_at','<=',$end)
                ->whereDate('created_at','>=',$start)
                ->get()->transform(function ($item) {
                    $service = Service::where('uniqueId', $item->serviceId)->first();
                    return [
                        'id' => $item->uniqueId,
                        'phone' => $item->phone,
                        'service' => $service->serviceName ?? "Deleted Service",
                        'status' => $item->status,
                        'date' => Carbon::parse($item->updated_at)->toDateTimeString()
                    ];
                });

            $transactions = DB::table('vendors_balance')->where('vendorID', Auth::user()->id)
                ->whereDate('created_at','<=',$end)
                ->whereDate('created_at','>=',$start)
                ->get()->transform(function ($item){
                    return [
                        'id' => $item->uniqueId,
                        'amount' => $item->amount,
                        'request' => $item->requestID,
                        'type' => $item->type,
                        'date' => Carbon::parse($item->created_at)->toDateTimeString()
                    ];
                });
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

    public function simsListView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $sims = Sims::where('userId', Auth::user()->id)->get()->toArray();
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