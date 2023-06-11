<?php


namespace App\Services;


use App\Models\Activity;
use App\Models\Balance;
use App\Models\Network;
use App\Models\Service;
use App\Models\Sims;
use App\Models\User;
use App\Repositories\BalanceRepositoryInterface;
use App\Repositories\SimsRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;
use DB;
use Auth;

class CustomerService
{
    protected $balanceRepo;
    protected $simsRepo;
    protected $apiService;
    public function __construct(BalanceRepositoryInterface $balanceRepo, SimsRepositoryInterface $simsRepo, ApiService $apiService)
    {
        $this->balanceRepo = $balanceRepo;
        $this->simsRepo = $simsRepo;
        $this->apiService = $apiService;
    }
    public function getBalance(int $userid)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $user = User::where('id', $userid)->first();
            if(empty($user))
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - User not found');
                return [
                    'status' => 0,
                    'error' => 'User not found'
                ];
            }
            if($user->admin)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - admin dau co balance dau ma coi');
                return [
                    'status' => 0,
                    'error' => 'admin dau co balance dau ma coi'
                ];
            }

            if ($user->balance < 0 ) // recorrect
            {
                $user->balance = 0;
                $user->save();
            }
            
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return [
                'status' => 1,
                'data' => $user->balance
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

    public function requestsView($startDate, $endDate)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $transactions = Activity::where([
                ['userid', Auth::user()->id]
            ])->orderBy('created_at', 'DESC')->whereDate('created_at','<=',$end)
                ->whereDate('created_at','>=',$start)
                ->get()->transform(function ($item) {
                    $service = Service::where('uniqueId', $item->serviceId)->first();
                    return [
                        'id' => $item->uniqueId,
                        'service' => $service->serviceName,
                        'price' => $service->price,
                        'phone' => $item->phone,
                        'status' => $item->status,
                        'code' => $item->code,
                        'date' => Carbon::parse($item->created_at)->toDateTimeString(),
                    ];
                })->toArray();
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => $transactions
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function dashboardView($startDate, $endDate)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $transactions = Balance::where([
                ['accountId', Auth::user()->id],
                ['status', "<=", 2]
            ])->orderBy('created_at', 'DESC')->whereDate('created_at','<=',$end)
                ->whereDate('created_at','>=',$start)
                ->get()->transform(function ($item) {
                    return [
                        'date' => Carbon::parse($item->created_at)->toDateTimeString(),
                        'type' => $item->type,
                        'amount' => $item->totalChange,
                        'status' => $item->status,
                        'request' => $item->activityId,
                        'reason' => $item->reason,
                    ];
                })->toArray();
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => $transactions
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function balanceFilter($startDate, $endDate)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $totalSpent = Balance::where([
                ['accountId', Auth::user()->id],
                ['status', 1]
            ])->whereDate('created_at','<=',$end)
                ->whereDate('created_at','>=',$start)->sum('totalChange');
            $totalTopup = Balance::where([
                ['accountId', Auth::user()->id],
                ['status', 3]
            ])->whereDate('created_at','<=',$end)
                ->whereDate('created_at','>=',$start)->sum('totalChange');

            $transactions = Balance::where([
                ['accountId', Auth::user()->id],
                ['status', ">=", 3]
            ])->orderBy('created_at', 'DESC')->whereDate('created_at','<=',$end)
                ->whereDate('created_at','>=',$start)
                ->get()->transform(function ($item) {
                    return [
                        'date' => Carbon::parse($item->created_at)->toDateTimeString(),
                        'type' => $item->type,
                        'amount' => $item->totalChange,
                        'status' => $item->status,
                        'request' => $item->activityId,
                        'reason' => $item->reason,
                    ];
                })->toArray();
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'transactions' => $transactions,
                    'spent' => $totalSpent,
                    'topup' => $totalTopup
                ]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function dashboardFilter($startDate, $endDate)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $users = User::all();

            $transactions = Balance::leftJoin('activitieslog','balanceslog.activityId','=','activitieslog.uniqueId')
                ->whereDate('balanceslog.created_at','<=',$end)
                ->whereDate('balanceslog.created_at','>=',$start)
                ->leftJoin('services', 'services.uniqueId', '=', 'activitieslog.serviceId')
                ->select(
                    'balanceslog.uniqueId',
                    'balanceslog.accountId',
                    'balanceslog.activityId',
                    'balanceslog.oldBalance',
                    'balanceslog.newBalance',
                    'balanceslog.totalChange',
                    'balanceslog.status',
                    'balanceslog.type',
                    'balanceslog.created_at',
                    'activitieslog.serviceId',
                    'services.serviceName'
                )
                ->orderBy('balanceslog.created_at', 'desc')
                ->get()->transform(function ($item) {
                    return [
                        'id' => $item['uniqueId'],
                        'userid' => $item['accountId'],
                        'serviceName' => $item['serviceName'] ?? null,
                        'old' => $item['oldBalance'],
                        'new' => $item['newBalance'],
                        'amount' => $item['totalChange'],
                        'status' => $item['status'],
                        'date' => $item['created_at'],
                        'type' => ($item['status'] == 3) ? 'topup' : ($item['type'] == '+' ? 'plus' : 'minus'),
                        'typeText' => $item['type']
                    ];
                });
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'users' => $users,
                    'transactions' => $transactions
                ]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function adminDashboardView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $sims = Sims::where('status', '>', 0)->count();
            $simsDied = Sims::where('status', 0)->count();
            $totalSims = Sims::count();

            $users = User::where('tier', '<', 100)->count();
            $usersBalances = User::where('tier', '<', 100)->sum('balance');

            $activities = Activity::where('status', '>', 0)->count();
            $activitiesFailed = Activity::where('status', 0)->count();

            $transactions = Balance::where('status', 1)->count();

            $apiDoc = $this->apiService->getDoc();

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'count' => [
                        'sims' => [
                            'total' => $totalSims,
                            'alive' => $sims,
                            'died' => $simsDied
                        ],
                        'users' => [
                            'normal' => $users,
                            'balances' => $usersBalances,
                        ],
                        'transactions' => [
                            'normal' => $transactions,
                        ],
                        'requests' => [
                            'normal' => $activities,
                            'failed' => $activitiesFailed
                        ]
                    ],
                    'apiDoc' => $apiDoc['data']
                ]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getMessage());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function adminUsersView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $users = User::where([
                ['tier', '<', 10],
            ])->paginate(20);
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['users' => $users ?? []]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function adminVendorsView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $users = User::where([
                ['tier', '>=', 10],
            ])->get();
            foreach ($users as $user)
            {
                $sims = Sims::where('userId', $user->id)->count();

                $totalProfit = DB::table('vendors_balance')->where([
                    ['vendorId', $user->id],
                    ['type', '+']
                ])->sum('amount');

                $totalTurn = Activity::where([
                    ['handleByVendor', $user->id],
                    ['status', 1]
                ])->count();

                $user->simCount = $sims;
                $user->totalProfit = $totalProfit;
                $user->rentTotal = $totalTurn;
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['users' => $users ?? []]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function adminSimsView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $sims = Sims::orderBy('status', 'DESC')->paginate(20);
            $networks = Network::all();
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'sims' => $sims ?? [],
                    'networks' => $networks ?? []
                ]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function adminServicesView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $services = Service::paginate(20);
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'services' => $services ?? []
                ]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function adminNetworksView()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $network = Network::paginate(20);
            $networks = Network::all();
            $phoneList = [];
            foreach ($networks as $net)
            {
                $phoneCount = Sims::where('networkId', $net['uniqueId'])->count();
                $phoneList[$net['id']] = $phoneCount;
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => [
                    'networks' => $network ?? [],
                    'simList' => $phoneList
                ]
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function bulkEdit(string $action, array $data)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            if ($action == 'lock')
            {
                Sims::whereIn('uniqueId', $data)->update(['status' => 0]);
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['edit' => 'Success']
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function editBalance(string $userid, string $type, int $balance, string $reason)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $user = User::where('id', $userid)->first();
            if(empty($user))
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - User not found');
                return [
                    'status' => 0,
                    'error' => 'User not found'
                ];
            }
            if ($type == 'plus')
            {
                Balance::insert([
                    'uniqueId' => substr(sha1(date("Y-m-d H:i:s")),0,10),
                    'accountId' => $userid,
                    'oldBalance' => $user->balance,
                    'newBalance' => $user->balance + $balance,
                    'totalChange' => $balance,
                    'status' => 3, // 2: hold ; 1: success
                    'reason' => $reason,
                    'activityId' => 'adminAction'.substr(sha1(date("Y-m-d H:i:s")),0,10).'321',
                    'type' => '+',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $user->balance = $user->balance + $balance;
                $user->save();
            }else{
                $finalAmount = $user->balance - $balance;
                if ($finalAmount < 0) $finalAmount = 0;
                Balance::insert([
                    'uniqueId' => substr(sha1(date("Y-m-d H:i:s")),0,10),
                    'accountId' => $userid,
                    'oldBalance' => $user->balance,
                    'newBalance' => $finalAmount,
                    'totalChange' => $balance,
                    'status' => 4, // 2: hold ; 1: success
                    'reason' => $reason,
                    'activityId' => 'adminAction'.substr(sha1(date("Y-m-d H:i:s")),0,10).'321',
                    'type' => '-',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $user->balance = $finalAmount;
                $user->save();
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['edit' => 'Success']
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function updateUser(string $userid, string $name, string $email)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $user = User::where('id', $userid)->first();
            $user->name = $name;
            $user->email = $email;
            $user->save();
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['edit' => 'Success']
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function removeLockedService($sim, $service)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . $sim . ' - ' . $service);
            $sim = Sims::where('uniqueId', $sim)->first();
            if (!$sim || empty($sim))
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Sim not found');
                return array(
                    'status' => 0,
                    'error' => 'Sim not found'
                );
            }

            $lockedServices = json_decode($sim['locked_services'], true);

            if (empty($lockedServices[$service]))
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - Locked service not found');
                return array(
                    'status' => 0,
                    'error' => 'Locked service not found'
                );
            }

            DB::table('sim_activities')->where([
                ['phoneNumber', $sim['phone']],
                ['serviceId', $service]
            ])->update(['deleted_at' => Carbon::now()]);

            unset($lockedServices[$service]);
            $sim->locked_services = json_encode($lockedServices);
            $sim->save();

            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                "status"=>1,
                "data"=>"removed successfully :)"
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function updateSim(string $uniqueId, string $status = null, string $network = null, bool $delete = false)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $sim = Sims::where('uniqueId', $uniqueId)->first();
            if ($delete)
            {
                $sim->delete();
            }else {
                $sim->status = $status;
                $sim->networkId = $network;
                $sim->save();
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['edit' => 'Success']
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function updateService(string $uniqueId, string $status = null, int $price = null, int $limit, int $success, int $fail, int $cooldown, string $structure, string $valid, bool $delete = false)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $service = Service::where('id', $uniqueId)->first();
            if ($delete)
            {
                $service->delete();
            }else {
                $service->status = $status;
                $service->price = $price;
                $service->limit = $limit;
                $service->success = $success;
                $service->fail = $fail;
                $service->cooldown = $cooldown;
                $service->structure = $structure;
                $service->valid = $valid;
                $service->save();
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['edit' => 'Success']
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

    public function updateNetwork(string $uniqueId, string $status = null, bool $delete = false)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $network = Network::where('id', $uniqueId)->first();
            if ($delete)
            {
                $network->delete();
            }else {
                $network->status = $status;
                $network->save();
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return [
                'status' => 1,
                'data' => ['edit' => 'Success']
            ];
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return array(
                'status' => 0,
                'error' => $e->getMessage()
            );
        }
    }

//    public function balanceView()
//    {
//        $totalSpent = Balance::where([
//            ['accountId', Auth::user()->id],
//            ['status', 1]
//        ])->sum('totalChange');
//
//        $totalTopup = Balance::where([
//            ['accountId', Auth::user()->id],
//            ['status', 3]
//        ])->sum('totalChange');
//
//        $accountChanges = Balance::where([
//            ['accountId', Auth::user()->id],
//            ['status', ">=", 3]
//        ])->get();
//
//        return [
//            'spent' => $totalSpent,
//            'topup' => $totalTopup,
//            'transactions' => $accountChanges
//        ];
//    }
}