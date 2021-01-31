<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;


class SocialAuthController extends Controller
{
    public function googleAuth(Request $request, $name = null)
    {
        $request->session()->put('login.request.api', $name);
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback(Request $request)
    {
        $error = "";
        $socialLoginUser = Socialite::driver('google')->user();
        $socialLoginUserEmail = $socialLoginUser->email;
        if ($socialLoginUserEmail !== null && $socialLoginUserEmail !== "") {
            $dbUser = User::where("email", $socialLoginUserEmail)->first();
            if ($dbUser != null) {
                if ($dbUser->is_two_factor_enabled) {
                    $request->session()->put('login.id', $dbUser->getKey());
                }else{
                    Auth::login($dbUser->getKey());
                }
            } else {
                // create user
                User::create([
                    "fname" => $socialLoginUser->user->given_name,
                    "lname" => $socialLoginUser->user->family_name,
                    "email" => $socialLoginUser->user->email,
                ]);
                Auth::login($dbUser->getKey());
            }
        } else {
            $error = "No email associated with the logged in account.";
        }

        if ($error !== "") {
            return back();
        } else {
            return back();
        }
    }
}
