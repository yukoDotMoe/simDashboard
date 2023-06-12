<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorMiddleware
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
        if ($user['ban'] == 1)
        {
            Auth::logout();
            return redirect('/login')->with('error', 'Your account has been ban.');
        }
        if ($user['tier'] < 10) return response([
            'status' => 401,
            'success' => false,
            'message' => 'You cannot use this function as an user',
        ], 401);
        return $next($request);
    }
}
