<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    $user = $request->user();
    $object = new stdClass();
    $object->name = $user->name;
    $object->email = $user->email;
    if ($user->two_factor_secret !== null && $user->two_factor_secret !== '')
        $object->two_factor_authentication_enabled = true;
    // $object->two_factor_authentication_enabled = false;
    return ["roles" => $request->user()->roles()->get(), "user" => $object];
})->middleware('auth:sanctum');

// fortify
$limiter = config('fortify.limiters.login');
$enableViews = config('fortify.views', true);
$twoFactorLimiter = config('fortify.limiters.two-factor');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(array_filter([
        'guest_api',
        $limiter ? 'throttle:' . $limiter : null,
    ]));

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// Password Confirmation...
if ($enableViews) {
    Route::get('/user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show'])
        ->middleware(['auth:sanctum'])
        ->name('password.confirmation');
}

Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store'])
    ->middleware(['auth:sanctum']);


// Two Factor Authentication...
if (Features::enabled(Features::twoFactorAuthentication())) {
    Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            'guest_api',
            $twoFactorLimiter ? 'throttle:' . $twoFactorLimiter : null,
        ]));

    $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
        ? ['auth:sanctum', 'password.confirm']
        : ['auth:sanctum'];

    Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
        ->middleware($twoFactorMiddleware);

    Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
        ->middleware($twoFactorMiddleware);

    Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
        ->middleware($twoFactorMiddleware);

    Route::get('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
        ->middleware($twoFactorMiddleware);

    Route::post('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])
        ->middleware($twoFactorMiddleware);
}


Route::get('/fruits', function () {
    return [
        ['name' => 'ssdsdsdsd', 'age' => 22],
        ['name' => 'zzz 34334ssasas', 'age' => 54],
        ['name' => 'dfbcb dfd', 'age' => 47],
        ['name' => 'cxswee fgf', 'age' => 46],
        ['name' => 'mhjge vbvs', 'age' => 34],
    ];
})->middleware(['auth', 'admin']);

Route::get('/fruit', function () {
    return [
        ['name' => 'ssdsdsdsd', 'age' => 22],
        ['name' => 'zzz 34334ssasas', 'age' => 54],
    ];
});

Route::get('/tuypes', function () {
    return [
        ['name' => 'ssdsdsdsd', 'age' => 22],
        ['name' => 'zzz 34334ssasas', 'age' => 54],
    ];
});
