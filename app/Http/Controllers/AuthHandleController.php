<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthHandleController extends Controller
{
    public function getSesssionUser(Request $request)
    {
        $user = $request->user();
        //check for 2fa if not authenticated
        if ($user === null && $request->session()->has('login.id')) {
            $user = User::find($request->session()->get('login.id'));
            $user->prompt2fa = true;
        }
        if ($user) {
            $user->two_factor_secret = null;
            $user->two_factor_recovery_codes = null;
            return ["roles" => $user->roles()->get(), "user" => $user];
        }
        return ["roles" => null, "user" => null];
    }

    public function forgetTwoFactorLogin(Request $request)
    {
        $request->session()->forget('login.id');
    }

    
}
