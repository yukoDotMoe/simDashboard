<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ApiService;
use App\Services\SimsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


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
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            if ($request->has('token')) {
                $user = User::where('api_token', $request->query('token'))->first();
            } else {
                $user = User::where('id', Auth::user()->id)->first();
            }
            $serviceId = $request->query('service');

            $result = $this->simsService->basicRent($user->api_token, $serviceId);

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

            if($result['status'] == 0)
            {
                Log::error(__CLASS__ . ' - ' . __FUNCTION__ . ' - End - Error - ' . $result['error']);
                return [
                    'status' => 0,
                    'error' => $result['error']
                ];
            }
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - End');
            return response()->json(ApiService::returnResult($result['data']));
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

    public function updateSimClient(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
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
            return [
                'status' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
}
