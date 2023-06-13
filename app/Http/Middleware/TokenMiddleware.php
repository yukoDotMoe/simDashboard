<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!$request->has('token') && empty($request->token))
            {
                return response([
                    'status' => 401,
                    'success' => false,
                    'message' => 'Unauthorized Access',
                ], 401);
            }else{
                $token = $request->token;
                if ($token == config('simConfig.adminToken')) return $next($request);
                $user = User::where('api_token', $token)->first();
                if ($user['ban'] == 1 || $user['lock_api'] == 1)
                {
                    Auth::logout();
                    return redirect('/login')->with('error', 'Your account has been ban.');
                }
                if ($user['tier'] >= 10) return response([
                    'status' => 401,
                    'success' => false,
                    'message' => 'You cannot use this function as an vendor',
                ], 401);
            }
            return $next($request);
        } catch (Exception $e) {
            Log::info($e);
            return response([
                'status' => 501,
                'success' => false,
                'message' => 'The site is experiencing technical difficulties. Please try again',
            ], 401);
        }
    }
}
