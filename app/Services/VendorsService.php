<?php


namespace App\Services;


use App\Models\Activity;
use App\Models\Service;
use App\Models\Sims;
use App\Models\User;
use App\Models\Balance;
use App\Models\Network;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Log;
use Exception;

class VendorsService
{
    public function transactionsView($startDate, $endDate)
    {
        try {
            // Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . $startDate . ' - ' . $endDate);
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            $transactions = Balance::where([
                ['accountId', Auth::user()->id],
                ['status', '>', 2]
                ])
                ->whereDate('created_at','<=',$end)
                ->whereDate('created_at','>=',$start)
                ->get()->transform(function ($item){
                    return [
                        'id' => $item->uniqueId,
                        'amount' => $item->totalChange,
                        'type' => $item->type,
                        'status' => $item->status,
                        'reason' => $item->reason,
                        'old' => $item->oldBalance,
                        'new' => $item->newBalance,
                        'date' => Carbon::parse($item->created_at)->toDateTimeString()
                    ];
                });

            // Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => $transactions->toArray() ?? []
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
    public function dashboardView($startDate, $endDate)
    {
        try {
            // Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . $startDate . ' - ' . $endDate);
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            
            $transactions = DB::table('vendors_balance')->leftJoin('activitieslog','activitieslog.uniqueId','=','vendors_balance.requestID')
                ->whereDate('activitieslog.created_at','<=',$end)
                ->whereDate('activitieslog.created_at','>=',$start)
                ->leftJoin('services', 'services.uniqueId', '=', 'activitieslog.serviceId')
                ->select(
                    'activitieslog.uniqueId',
                    'activitieslog.userid',
                    'activitieslog.phone',
                    'services.serviceName',
                    'activitieslog.status',
                    'activitieslog.created_at',
                    'activitieslog.code',
                    'vendors_balance.amount',
                    'vendors_balance.withdraw'
                )
                ->orderBy('activitieslog.created_at', 'desc')
                ->get()->transform(function ($item) {
                    $user = User::where('id', $item->userid)->first();
                    return [
                        'id' => $item->uniqueId,
                        'user' => $user->username ?? 'Deleted User',    
                        'phone' => $item->phone,
                        'code' => $item->code,
                        'service' => ucfirst($item->serviceName),
                        'status' => $item->status,
                        'amount' => $item->amount,
                        'withdraw' => $item->withdraw,
                        'date' => Carbon::parse($item->created_at)->toDateTimeString()
                    ];
                });
            // Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => $transactions->toArray() ?? []
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

    public function fetchSimActivities($sim)
    {
        try {
            // Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $sims = Sims::where('uniqueId', $sim)->first();
            $working = DB::table('success_records')->where('simId', $sim)->orderBy('created_at', 'DESC')->get()->transform(function ($item){
                $activitys = Activity::where('uniqueId', $item->requestId)->first();
                $user = User::where('id', $activitys->userid)->first();
                $service = Service::where('uniqueId', $item->serviceId)->first();
                return [
                    'id' => $item->uniqueId,
                    'service' => ucfirst($service->serviceName) ?? 'Deleted Service',
                    'request' => $item->requestId ?? 'Deleted Request',
                    'user' => $user->username,
                    'price' => number_format($service->price, 0, '', ','),
                    'date' => Carbon::parse($item->created_at)->toDateTimeString()
                ];
            });
            $failed = DB::table('failed_records')->where('simId', $sim)->orderBy('created_at', 'DESC')->get()->transform(function ($item){
                $service = Service::where('uniqueId', $item->serviceId)->first();
                $activitys = Activity::where('uniqueId', $item->requestId)->first();
                $user = User::where('id', $activitys->userid)->first();
                return [
                    'id' => $item->uniqueId,
                    'service' => ucfirst($service->serviceName) ?? 'Deleted Service',
                    'request' => $item->requestId ?? 'Deleted Request',
                    'user' => $user->username,
                    'reason' => $item->reason,
                    'date' => Carbon::parse($item->created_at)->toDateTimeString()
                ];
            });
            
            $activitys = Activity::where([
                ['simId', $sim],
                ['status', 2]
            ])->get()->transform(function ($item){
               $service = Service::where('uniqueId', $item->serviceId)->first();
               $userRent = User::where('id', $item->userid)->first();
               return [
                    'id' => $item->uniqueId,
                    'user' => $userRent->username,
                    'service' => ucfirst($service->serviceName) ?? 'Deleted Service',
                    'request' => $item->uniqueId ?? 'Deleted Request',
                    'reason' => $item->reason,
                    'date' => Carbon::parse($item->created_at)->toDateTimeString()
                ];
            });

            // Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return ['status' => 1, 'data' => ['phone' => $sims->phone,'success' => $working ?? [], 'failed' => $failed ?? [], 'working' => $activitys ?? []]];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function simsListView($showOffline = false)
    {
        try {
            // Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . $showOffline);
            $sims = Sims::where([
                ['userId', Auth::user()->id],
                ['status', '>', ($showOffline) ? -1 : 0]
                ])->get()->transform(function ($item){
                
                $network = Network::where('uniqueId', $item->networkId)->first();
                
                return [
                    'id' => $item->uniqueId,
                    'phone' => $item->phone,
                    'network' => $network->networkName ?? 'Deleted Network',
                    'status' => $item->status,
                    'success' => $item->success,
                    'failed' => $item->failed,
                    'date' => Carbon::parse($item->created_at)->toDateTimeString()
                ];
            })->toArray();
            
            
            foreach ($sims as $key => $sim)
            {
                
                $acts = Activity::where([
                        ['status', 1],
                        ['simId', $sim['id']]
                    ])->get()->transform(function ($item){
                    return $item->uniqueId;
                });
                
                $totalProfit = Balance::whereIn('activityId', $acts)->where('status', 1)->whereDate('created_at','<=', Carbon::now()->endOfDay())
                ->whereDate('created_at','>=', Carbon::now()->startOfDay() )->sum('totalChange');
                
                $sims[$key]['totalProfit'] = $totalProfit;   
            }
        
            
            $working = Sims::where([
                ['userId', Auth::user()->id],
                ['status', '>=', 1]
            ])->count();
            $nonWorking = Sims::where([
                ['userId', Auth::user()->id],
                ['status', 0]
            ])->count();
            // Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => [
                    'sims' => $sims ?? [],
                    'online' => $working ?? 0,
                    'offline' => $nonWorking ?? 0
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
}