<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\Service;
use App\Models\Sims;
use App\Models\User;
use App\Services\ApiService;
use App\Services\CustomerService;
use App\Services\NetworkService;
use App\Services\ServiceService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            $result = User::where('email', $id)->first();
        }else
        {
            $result = User::where('id', $id)->first();
        }
        return response()->json($result);
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
                $result = $this->customerService->updateService($uniqueId, null, null, null, null, null, null,true);
            }else{
                $status = $request->query('status');
                $price = $request->query('price');
                $limit = $request->query('limit');
                $cooldown = $request->query('cooldown');
                $structure = $request->query('structure');
                $valid = $request->query('valid');
                $result = $this->customerService->updateService($uniqueId, $status, $price, $limit, $cooldown, $structure, $valid);
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
            $cooldown = $request->query('cooldown');
            $structure = $request->query('structure');
            $valid = $request->query('valid');
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . $name . ' - ' . $price);
            $result = $this->serviceService->create($name, $price, $limit, $cooldown, $structure, $valid);
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
}
