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
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function update(Request $request)
    {
        $token = Str::random(80);

        $request->user()->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();

        return ['token' => $token];
    }

    public function dashboardView()
    {
        $result = $this->customerService->dashboardView();
        if ($result['status'] == 0 ) abort(502);
        return view('dashboard', ['data' => $result['data']]);
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
                return [
                    'status' => 0,
                    'error' => $result['error']
                ];
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
}
