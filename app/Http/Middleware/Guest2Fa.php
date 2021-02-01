<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Guest2Fa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //redirect if 2fa
        if (!$request->session()->has('login.id'))
            return redirect()->route('login');

        return $next($request);
    }
}
