<?php

namespace App\Http\Controllers;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Config;

class SocialAuthController extends Controller
{
    public function googleAuth(Request $request, $name = null)
    {
        $request->session()->put('login.request.api', $name);
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback(Request $request)
    {
        $socialLoginUser = Socialite::driver('google')->user();
        return $this->handleSocialLoginAuth($socialLoginUser, "Google", $request);
    }

    public function facebookAuth(Request $request, $name = null)
    {
        $request->session()->put('login.request.api', $name);
        return Socialite::driver('facebook')->redirect();
    }

    public function facebookCallback(Request $request)
    {
        $socialLoginUser = Socialite::driver('facebook')->user();
        return $this->handleSocialLoginAuth($socialLoginUser, "Facebook", $request);
    }

    public function handleSocialLoginAuth($socialLoginUser, $provider, Request $request)
    {
        $socialLoginUserEmail = $socialLoginUser->email;
        $redirectToApi = $request->session()->get('login.request.api') === "api";

        if ($socialLoginUserEmail !== null && $socialLoginUserEmail !== "") {
            $dbUser = User::where("email", $socialLoginUserEmail)->first();
            if ($dbUser != null) {
                if ($dbUser->is_two_factor_enabled) {
                    $request->session()->put('login.id', $dbUser->getKey());
                } else {
                    //login account matched email
                    Auth::loginUsingId($dbUser->getKey());
                }
            } else {
                $social_user_profile = $socialLoginUser->user;
                // create user
                $dbUser = new User;
                $dbUser->fname = $social_user_profile['name'];
                $dbUser->email = $social_user_profile['email'];
                $dbUser->password = Str::random(25);
                $dbUser->save();
                Auth::loginUsingId($dbUser->getKey());
            }
        } else {
            $request->session()->put("login.error", "All User profiles at Pizza Apes must have an email address. There is no email address associated with the logged in " . $provider . " account.");
        }
        
        if ($redirectToApi)
            return redirect(Config('EnvValues.REACT_API_URL'). "/login");
        else
            return redirect()->route('login');
    }
}
