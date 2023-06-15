<?php

namespace App\Http\Controllers;

use App\Events\SmsDelivered;
use App\Models\User;
use App\Services\ApiService;
use App\Services\SimsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;

class SimController extends Controller
{
    protected $simsService;
    public function __construct(SimsService $simsService)
    {
        $this->simsService = $simsService;
    }

    public function userRent(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            $validator = Validator::make($request->all(), [
                'service' => 'required|bail',
                'network' => 'required|string',
                'number' => 'nullable|min:9|max:15',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    ApiService::returnResult(
                        $validator->errors()->toArray(),
                        415,
                        'Invalid information'
                    )
                );
            }
            $isApi = false;
            if (!empty(request()->bearerToken())) {
                $user = User::where([
                    ['api_token', request()->bearerToken()],
                    ['ban', 0],
                    ['lock_api', 0]
                ])->first();
                $isApi = true;
            } else {
                $user = User::where('id', Auth::user()->id)->first();
            }
            $serviceId = $request->service;
            $network = $request->network;
            $phone = $request->number;

            $result = $this->simsService->basicRent($user->api_token, $serviceId, $network, $isApi, $phone);

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
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
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

    public function updateSimClient(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Data - ' . json_encode($request->all()));
            if (!empty(request()->bearerToken())) {
                $user = User::where([
                    ['api_token', request()->bearerToken()],
                    ['ban', 0],
                    ['lock_api', 0]
                ])->first();
                if (empty($user)) return response()->json(
                    ApiService::returnResult(
                        [],
                        404,
                        'Unauthorized Access'
                    )
                );
            }else{
                return response()->json(
                    ApiService::returnResult(
                        [],
                        404,
                        'Unauthorized Access'
                    )
                );
            }
            if (!$user['admin'] && $user['tier'] < 10)
            {
                return response()->json(
                    ApiService::returnResult(
                        [],
                        401,
                        'Invalid access'
                    )
                );
            }
            $result = $this->simsService->handleClientRequest($request->getContent(), $user['tier'] >= 10, $user['id']);
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult(['result' => $result]));
        } catch (Exception $e)
        {
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

    public function rentView()
    {
        $result = $this->simsService->basicRentView();
        if ($result['status'] == 0 ) abort(502);
        return view('basicRent', [ 'data' => $result['data']]);
    }

    public function customRentView()
    {
        $result = $this->simsService->customRentView();
        if ($result['status'] == 0 ) abort(502);
        return view('customRent', [ 'data' => $result['data']]);
    }

    public function rentHistoryView()
    {
        $result = $this->simsService->rentHistoryView();
        if ($result['status'] == 0 ) abort(502);
        return view('rentHistory', ['data' => $result['data']]);
    }

    public function fetchRequest(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            $validator = Validator::make($request->all(), [
                'requestId' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    ApiService::returnResult(
                        $validator->errors()->toArray(),
                        415,
                        'Invalid information'
                    )
                );
            }

            $requestId = $request->requestId;

            $result = $this->simsService->fetchRequest((string)$requestId);

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
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
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
}
