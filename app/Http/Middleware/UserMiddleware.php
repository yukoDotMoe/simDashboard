<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserMiddleware
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
        $user = $request->user();
        if ($user['tier'] >= 10) {
            if ($request->route()->getName() == 'dashboard') return redirect()->route('vendor.dashboard');
            return response([
                'status' => 401,
                'success' => false,
                'message' => 'You cannot use this function as an vendor',
            ], 401);
        }
        return $next($request);
    }
}
