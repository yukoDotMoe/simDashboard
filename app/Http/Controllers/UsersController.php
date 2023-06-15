<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ApiService;
use App\Services\CustomerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;
use Session;

class UsersController extends Controller
{
    protected $customerService;
    protected $apiService;
    public function __construct(CustomerService $customerService, ApiService $apiService)
    {
        $this->customerService = $customerService;
        $this->apiService = $apiService;
    }

    public function accountView()
    {
        return view('account');
    }

    public function balanceView()
    {
        return view('balance');
    }

    public function dashboardView()
    {
        if (Auth::user()->tier >= 10)
        {
            return view('vendors.dashboard');
        }else{
            return view('dashboard');
        }
    }

    public function balanceFilter(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            $start = $request->startDate;
            $end = $request->endDate;
            $result = $this->customerService->balanceFilter($start, $end);
            if ($result['status'] == 0 )
            {
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

    public function requestsFilter(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            $start = $request->startDate;
            $end = $request->endDate;
            $result = $this->customerService->requestsView($start, $end);
            if ($result['status'] == 0 )
            {
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

    public function dashboardFilter(Request $request)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start - ' . json_encode($request->all()));
            $start = $request->startDate;
            $end = $request->endDate;
            $result = $this->customerService->dashboardView($start, $end);
            if ($result['status'] == 0 )
            {
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

    public function checkToken($token)
    {
        try {
            Log::info(__CLASS__ . ' - ' . __FUNCTION__ . ' - Start');
            // if (!empty(request()->bearerToken())) {
            //         $user = User::where([
                        // ['api_token', request()->bearerToken()],
                        // ['ban', 0],
                        // ['lock_api', 0]
            //         ])->first();
            // } else {
            //     return response([
            //         'status' => 401,
            //         'success' => false,
            //         'message' => 'Unauthorized Access.',
            //     ], 401);
            // }
            // if (empty($user)) return response([
            //         'status' => 401,
            //         'success' => false,
            //         'message' => 'Unauthorized Access..',
            //     ], 401);
            // if ($user->admin != 1 && $user->tier < 10) return response([
            //         'status' => 401,
            //         'success' => false,
            //         'message' => 'Unauthorized Access...',
            //     ], 401);
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
                $user = User::where('api_token', $request->token)->first();
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

    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect('login')->with('success', 'Password changed successfully. Please login again');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        #Match The Old Password
        if(!Hash::check($request->current_password, auth()->user()->password)){
            return back()->with("error", "Old Password Doesn't match!");
        }

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return $this->logout();
//        return redirect()->route('accounts')->with('success', 'Password change successfully. Please login again');
    }
}
