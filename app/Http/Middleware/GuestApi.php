<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            // authenticated
            if (Auth::guard($guard)->check()) {
                return back()->setStatusCode(401);
            }
        }

        //redirect if 2fa
        if ($request->session()->has('login.id'))
            return back()->setStatusCode(401);

        return $next($request);
    }
}
