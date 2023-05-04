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
                'prefix' => 'nullable|min:2|max:2|integer',
                'Eprefix' => 'nullable|min:2|max:2|integer',
                'number' => 'nullable|min:10|max:1|integer',
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

            if ($request->has('network')) {
                $result = $this->simsService->networkRent($user->api_token, $serviceId, $request->query('network'));
            }

            if ($request->has('prefix')) {
                $result = $this->simsService->rentStartWith($user->api_token, $serviceId, $request->query('prefix'));
            }

            if ($request->has('Eprefix')) {
                $result = $this->simsService->rentStartWith($user->api_token, $serviceId, $request->query('Eprefix'), false);
            }

            if ($request->has('number')) {
                $result = $this->simsService->basicRent($user->api_token, $serviceId, $request->query('number'));
            }

            if (!isset($result)) {
                $result = $this->simsService->basicRent($user->api_token, $serviceId);
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
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $validator = Validator::make($request->all(), [
                'phone' => 'required|integer|min:10',
                'network' => 'required|string',
                'content' => 'nullable|string',
                'code' => 'nullable|string',
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
            $content = ($request->has('content')) ? $request->input('content') : null;
            $code = ($request->has('code')) ? $request->input('code') : null;
            $result = $this->simsService->handleClientRequest(
                $request->query('token'),
                $request->query('phone'),
                $request->query('network'),
                $content,
                $code
            );
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            if($result['status'] == 0)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return [
                    'status' => 0,
                    'error' => $result['error']
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - ');
            return response()->json(ApiService::returnResult(['result' => $result['data']]));
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
