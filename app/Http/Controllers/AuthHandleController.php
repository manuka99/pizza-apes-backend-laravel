<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthHandleController extends Controller
{
    public function getSesssionUser(Request $request)
    {
        $user = new User();
        if ($request->user())
            $user = User::find($request->user()->getKey());
        //check for 2fa if not authenticated
        if ($user->id === null && $request->session()->has('login.id')) {
            $user = User::find($request->session()->get('login.id'));
            $user->prompt2fa = true;
        }
        if ($user) {
            return ["roles" => $user->roles()->get(), "user" => $user];
        }
        return ["roles" => null, "user" => null];
    }

    public function forgetTwoFactorLogin(Request $request)
    {
        $request->session()->forget('login.id');
        if (!$request->is('api/*'))
            return redirect('login');
    }

    public function loginErrorInSession(Request $request)
    {
        return $request->session()->pull('login.error');
    }

    public function getAuthUser(Request $request)
    {
        return ["roles" => $request->user()->roles()->get(), "user" => $request->user()];
    }
}
