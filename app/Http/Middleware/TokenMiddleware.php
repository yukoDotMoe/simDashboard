<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

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
        if (!$request->has('token') && empty($request->query('token')))
        {
            return response([
                'status' => 401,
                'success' => false,
                'message' => 'Unauthorized Access',
            ], 401);
        }else{
            $token = $request->query('token');
            if ($token == config('simConfig.adminToken')) return $next($request);
            $user = User::where('api_token', $token)->count();
            if ($user < 1) return response([
                'status' => 401,
                'success' => false,
                'message' => 'Unauthorized Access',
            ], 401);
        }
        return $next($request);
    }
}
