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
                'network' => 'nullable|string',
                'number' => 'nullable|min:10|max:15|integer',
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

            if ($request->has('token')) {
                $user = User::where('api_token', $request->query('token'))->first();
            } else {
                $user = User::where('id', Auth::user()->id)->first();
            }
            $serviceId = $request->query('service');
            $network = $request->query('network');
            $phone = $request->query('phone');

            $result = $this->simsService->basicRent($user->api_token, $serviceId, $network, $phone);

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
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            if ($request->has('token')) {
                $user = User::where('api_token', $request->query('token'))->first();
            }else{
                return response()->json(
                    ApiService::returnResult(
                        [],
                        415,
                        'Missing token, please add it to the url as query string.'
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
                'request' => 'required|string',
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
            if ($request->has('token')) {
                $user = User::where('api_token', $request->query('token'))->first();
            } else {
                $user = User::where('id', Auth::user()->id)->first();
            }
            $requestId = $request->query('request');

            $result = $this->simsService->fetchRequest($requestId);

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
