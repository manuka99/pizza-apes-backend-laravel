<?php

namespace App\Http\Middleware;

use App\Models\SessionData;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Closure;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;

class EnsureSessionIsValid
{

    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasSession() || !$request->user()) {
            return $next($request);
        } else if (!SessionData::find($request->session()->getId())->isValid) {
            $controller = new AuthenticatedSessionController($this->guard);
            $controller->destroy($request);
        }

        return $next($request);
    }
}
