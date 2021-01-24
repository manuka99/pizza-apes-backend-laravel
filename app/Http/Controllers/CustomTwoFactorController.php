<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse;
use Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;


class CustomTwoFactorController extends Controller
{
    public function enable(Request $req)
    {
        $user = $req->user();
        $code = $req->code;

        if (!$user->two_factor_secret) {
            return response()->json(['message' => 'Two factor authentication secret was missing or incorrect.'])->setStatusCode('422');
        } else if ($code && app(TwoFactorAuthenticationProvider::class)->verify(
            decrypt($user->two_factor_secret),
            $code
        )) {
            $user->forceFill([
                'is_two_factor_enabled' => true,
            ])->save();
            return response()->json(['status' => 'two-factor-authentication-enabled'])->setStatusCode('200');
        } else {
            return response()->json(['code' => 'The provided two factor authentication code was invalid'])->setStatusCode('422');
        }
    }
}
