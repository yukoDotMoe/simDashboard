<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ApiService;
use App\Services\CustomerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;

class UsersController extends Controller
{
    protected $customerService;
    protected $apiService;
    public function __construct(CustomerService $customerService, ApiService $apiService)
    {
        $this->customerService = $customerService;
        $this->apiService = $apiService;
    }

    public function dashboardView()
    {
        if (Auth::user()->tier >= 10)
        {
            return view('vendors.dashboard');
        }else{
            $result = $this->customerService->dashboardView();
            if ($result['status'] == 0 ) abort(502);
            return view('dashboard', ['data' => $result['data']]);
        }
    }

    public function checkToken($token)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $result = $this->apiService->checkApi($token);
            if($result['status'] == 0)
            {
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
            return response()->json(
                ApiService::returnResult(
                    $result['data']
                )
            );
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

    public function accountInfo(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            if ($request->has('token')) {
                $user = User::where('api_token', $request->query('token'))->first();
            } else {
                $user = User::where('id', Auth::user()->id)->first();
            }
            $result = $this->customerService->getBalance($user->id);
            if($result['status'] == 0)
            {
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
            return response()->json(
                ApiService::returnResult(
                    ['balance' => $result['data']]
                )
            );
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

    public function apiDoc()
    {
        $result = $this->apiService->getDoc();
        if ($result['status'] == 0 ) abort(502);
        return view('api', ['data' => $result['data']]);
    }

    public function resetToken()
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            $result = $this->apiService->updateToken();
            if($result['status'] == 0)
            {
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
            return response()->json(
                ApiService::returnResult(
                    ['token' => $result['token']]
                )
            );
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
}
