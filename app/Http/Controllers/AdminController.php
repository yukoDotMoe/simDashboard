<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Balance;
use App\Models\Network;
use App\Models\Service;
use App\Models\Sims;
use App\Models\User;
use App\Services\ApiService;
use App\Services\CustomerService;
use App\Services\NetworkService;
use App\Services\ServiceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;

class AdminController extends Controller
{
    protected $customerService;
    protected $serviceService;
    protected $networkService;
    protected $apiService;
    public function __construct(CustomerService $customerService, ServiceService $serviceService, NetworkService $networkService, ApiService $apiService)
    {
        $this->customerService = $customerService;
        $this->networkService = $networkService;
        $this->serviceService = $serviceService;
        $this->apiService = $apiService;
    }
    public function adminDashboardView()
    {
        $result = $this->customerService->adminDashboardView();
        if ($result['status'] == 0 ) abort(502);
        return view('admin.dashboard', ['data' => $result['data']]);
    }
    public function adminUsersView()
    {
        $result = $this->customerService->adminUsersView();
        if ($result['status'] == 0 ) abort(502);
        return view('admin.users', ['data' => $result['data']]);
    }

    public function adminVendorsView()
    {
        $result = $this->customerService->adminVendorsView();
        if ($result['status'] == 0 ) abort(502);
        return view('admin.vendors', ['data' => $result['data']]);
    }

    public function adminSimsView()
    {
        $result = $this->customerService->adminSimsView();
        if ($result['status'] == 0 ) abort(502);
        return view('admin.sims', ['data' => $result['data']]);
    }

    public function adminServicesView()
    {
        $result = $this->customerService->adminServicesView();
        if ($result['status'] == 0 ) abort(502);
        return view('admin.services', ['data' => $result['data']]);
    }

    public function adminNetworksView()
    {
        $result = $this->customerService->adminNetworksView();
        if ($result['status'] == 0 ) abort(502);
        return view('admin.networks', ['data' => $result['data']]);
    }

    public function getUser($id)
    {
        if(preg_match("/[a-z]/i", $id)) {
            $result = User::where('username', $id)->first();
        }else
        {
            $result = User::where('id', $id)->first();
        }
        return response()->json($result);
    }

    public function bulkEdit(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $action = $request->type;
            $data = $request->data;
            $result = $this->customerService->bulkEdit($action, $data);
            if ($result['status'] == 0) {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        $result['error']
                    )
                );
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult($result['data']));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function getService($id)
    {
        $id = str_replace(' ', '', $id);
        if(preg_match("/[a-z]/i", $id)) {
            $result = Service::where('serviceName', $id)->first();
        }else
        {
            $result = Service::where('id', $id)->first();
        }
        return response()->json($result);
    }

    public function getNetwork($id)
    {
        $id = str_replace(' ', '', $id);
        if(preg_match("/[a-z]/i", $id)) {
            $result = Network::where('networkName', $id)->first();
        }else
        {
            $result = Network::where('id', $id)->first();
        }
        return response()->json($result);
    }

    public function getSim($id)
    {
        $id = str_replace(' ', '', $id);
        $result = Sims::where('uniqueId', $id)->first();
        $locked = DB::table('sim_lock')->where([
            ['phone', $result->phone],
        ])->get()->transform(function ($item) {
            $service = Service::where('uniqueId', $item->services)->first();
            return [
                'id' => $item->id,
                'name' => $service->serviceName ?? 'Deleted Service',
                'cooldown' => $item->cooldown
            ];
        });
        
        $result->locked = $locked;
        return response()->json($result);
    }

    public function getSimByPhone($id)
    {
        $id = str_replace(' ', '', $id);
        if(preg_match("/[a-z]/i", $id)) {
            $result = Sims::where('uniqueId', $id)->first();
        }else{
            $result = Sims::where('phone', $id)->first();
        }
        return response()->json($result);
    }

    public function serviceCreate()
    {
        return view('admin.serviceCreate');
    }

    public function networkCreate()
    {
        return view('admin.networkCreate');
    }

    public function editBal(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $userid = $request->query('userid');
            $type = $request->query('type');
            $amount = $request->query('amount');
            $reason = $request->query('reason');
            $result = $this->customerService->editBalance($userid, $type, $amount, $reason);
            if ($result['status'] == 0) {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        $result['error']
                    )
                );
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult($result['data']));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function updateUser(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $userid = $request->query('userid');
            $username = $request->query('username');
            $email = $request->query('email');
            $result = $this->customerService->updateUser($userid, $username, $email);
            if ($result['status'] == 0) {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        $result['error']
                    )
                );
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult($result['data']));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function veryBadUserUpdate(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            $uid = $request->userid;
            $user = User::findOrFail($uid);

            if (empty($user))
            {
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        'Cannot find user'
                    )
                );
            }

            $input = $request->input('data');
            if (isset($input['password'])) $input['password'] = Hash::make($input['password']);
            $user->fill($input)->save();
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult(['edit' => 'success']));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function ban(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            $type = $request->objType;
            $id = $request->objId;

            switch ($type)
            {
                case (0):
                    $selector = User::findOrFail($id);
                    break;
                case (1):
                    $selector = ($request->has('unban')) ? Sims::withTrashed()->findOrFail($id) : Sims::findOrFail($id);
                    break;
                case (2):
                    $selector = ($request->has('unban')) ? Service::withTrashed()->findOrFail($id) : Service::findOrFail($id);
                    break;
                case (3):
                    $selector = ($request->has('unban')) ? Network::withTrashed()->findOrFail($id) : Network::findOrFail($id);
                    break;
                default:
                    $selector = User::findOrFail($id);
            }
            if ($type == 0)
            {
                if (!$request->has('unban'))
                {
                    $selector->ban = true;
                    $selector->lock_api = true;
                }else{
                    $selector->ban = false;
                    $selector->lock_api = false;
                }

                $selector->save();
            }else{
                if (!$request->has('unban')){
                    $selector->delete();
                }else{
                    $selector->restore();
                }
            }
            if (empty($selector))
            {
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        'Cannot find objects'
                    )
                );
            }

//            $input = $request->input('data');
//            $user->fill($input)->save();
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult(['edit' => 'success']));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function updateSimInfo(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $uniqueId = $request->query('id');
            if ($request->has('delete'))
            {
                $result = $this->customerService->updateSim($uniqueId, null, null, true);
            }else{
                $status = $request->query('status');
                $network = $request->query('network');
                $result = $this->customerService->updateSim($uniqueId, $status, $network);
            }

            if ($result['status'] == 0) {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        $result['error']
                    )
                );
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult($result['data']));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function updateServicesInfo(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            $uniqueId = $request->query('id');
            if ($request->has('delete'))
            {
                $result = $this->customerService->updateService($uniqueId, null, null, null, null, null, null, null, null,null, true);
            }else{
                $name = $request->query('name');
                $status = $request->query('status');
                $price = $request->query('price');
                $limit = $request->query('limit');
                $success = $request->query('success');
                $fail = $request->query('fail');
                $cooldown = $request->query('cooldown');
                $structure = $request->query('structure');
                $valid = $request->query('valid');
                $result = $this->customerService->updateService($uniqueId, $name, $status, $price, $limit, $success, $fail, $cooldown, $structure, $valid);
            }

            if ($result['status'] == 0) {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        $result['error']
                    )
                );
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult($result['data']));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function createService(Request $request)
    {
        try {
            $name = $request->query('name');
            $price = $request->query('price');
            $limit = $request->query('limit');
            $success = $request->query('success');
            $fail = $request->query('fail');
            $cooldown = $request->query('cooldown');
            $structure = $request->query('structure');
            $valid = $request->query('valid');
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            $result = $this->serviceService->create($name, $price, $limit, $success, $fail, $cooldown, $structure, $valid);
            if ($result['status'] == 0) {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        $result['error']
                    )
                );
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult(['result' => $result['data']]));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function updateNetworksInfo(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ');
            $uniqueId = $request->query('id');
            if ($request->has('delete'))
            {
                $result = $this->customerService->updateNetwork($uniqueId, null, true);
            }else{
                $status = $request->query('status');
                $result = $this->customerService->updateNetwork($uniqueId, $status);
            }

            if ($result['status'] == 0) {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        $result['error']
                    )
                );
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult($result['data']));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . ' - ' . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function createNetwork(Request $request)
    {
        try {
            $name = $request->query('name');
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . $name);
            $result = $this->networkService->create($name);
            if ($result['status'] == 0) {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        $result['error']
                    )
                );
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult(['result' => $result['data']]));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function handleApiChange(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            $content = $request->apiContent;
            $result = $this->apiService->updateApiDoc($content);
            if ($result['status'] == 0) {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        $result['error']
                    )
                );
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult(['result' => $result['data']]));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function dashboardFilter(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            $start = $request->startDate;
            $end = $request->endDate;
            $result = $this->customerService->dashboardFilter($start, $end);
            if ($result['status'] == 0) {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        $result['error']
                    )
                );
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult($result['data']));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function removeLockedService(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            $simId = $request->simId;
            $lockedServiceId = $request->serviceId;
            $result = $this->customerService->removeLockedService($simId, $lockedServiceId);
            if ($result['status'] == 0) {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return response()->json(
                    ApiService::returnResult(
                        [],
                        502,
                        $result['error']
                    )
                );
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult(['result' => $result['data']]));
        } catch (Exception $e) {
            Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $e->getFile() . " - " . $e->getLine());
            return response()->json(
                ApiService::returnResult(
                    [],
                    502,
                    $e->getMessage()
                )
            );
        }
    }

    public function vendorCreate()
    {
        return view('admin.vendorsCreate');
    }

    public function vendorCreatePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|alpha_dash|unique:users,name',
            'password' => 'required|min:10',
            'profit' => 'required|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            Log::info($validator->fails());
            return redirect()->route('admin.vendors.create')->with('error', 'Thông tin không hợp lệ: ' . $validator->errors()->first());
        }

        $username = $request->username;
        $password = $request->password;
        $profit = $request->profit;

        $user = User::create([
            'name' => $username,
            'username' => $username,
            'password' => Hash::make($password),
            'profit' => $profit,
            'api_token' => Str::random(80),
            'tier' => 10,
            'balance' => 0,
            'lock_api' => false
        ])->id;

        if (empty($user))
        {
            return redirect()->route('admin.vendors.create')->with('error', 'Không thể tạo người dùng mới');
        }

        return redirect()->route('admin.vendors')->with('success', 'Tạo thành công đại lí với id ' . $user);
    }

    public function getHistory($id)
    {
        $user = User::where('id', $id)->first();
        return view('admin.history', ['user' => $user]);
    }

    public function getHistoryPost(Request $request)
    {

        $start = Carbon::parse($request->startDate);
        $end = Carbon::parse($request->endDate);
        $id = User::all();

        $transactions = Balance::where('accountId', $request->id)->leftJoin('activitieslog','balanceslog.activityId','=','activitieslog.uniqueId')
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
            ->orderBy('activitieslog.created_at', 'desc')
            ->get()->transform(function ($item) {
                return [
                    'id' => $item['uniqueId'],
                    'request' => $item['activityId'],
                    'serviceName' => $item['serviceName'] ?? null,
                    'old' => $item['oldBalance'],
                    'new' => $item['newBalance'],
                    'amount' => $item['totalChange'],
                    'status' => $item['status'],
                    'date' => $item['created_at'],
                ];
            });

        return response()->json(ApiService::returnResult([
            'transactions' => $transactions
        ]));
    }
}
