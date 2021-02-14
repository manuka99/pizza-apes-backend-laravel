<?php

use App\Http\Controllers\AuthHandleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomTwoFactorController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSessionsController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
//user details for react redux
Route::get('/redux/user', [AuthHandleController::class, 'getSesssionUser']);
//get login error
Route::get('/login-error', [AuthHandleController::class, 'loginErrorInSession']);

Route::group(['middleware' => ['guest_api']], function () {
    // login
    $limiter = config('fortify.limiters.login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            $limiter ? 'throttle:' . $limiter : null,
        ]));

    //google auth
    Route::get("/auth/google/redirect", [SocialAuthController::class, 'googleAuth']);
    Route::get('/auth/google/callback', [SocialAuthController::class, 'googleCallback']);
    //facebook auth
    Route::get("/auth/facebook/redirect", [SocialAuthController::class, 'facebookAuth']);
    Route::get('/auth/facebook/callback', [SocialAuthController::class, 'facebookCallback']);

    //forgot password
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
});


Route::group(['middleware' => 'guest_2fa_api'], function () {
    // two factor authentication challenge
    $twoFactorLimiter = config('fortify.limiters.two-factor');
    Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            $twoFactorLimiter ? 'throttle:' . $twoFactorLimiter : null,
        ]));

    // forget 2fa login id
    Route::post('/forget/two-factor-login', [AuthHandleController::class, 'forgetTwoFactorLogin']);
});

Route::middleware(['auth:sanctum'])->group(function () {

    //user details
    Route::get('/user', [AuthHandleController::class, 'getAuthUser']);

    // logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    // Passwords...
    Route::put('/user/password', [PasswordController::class, 'update']);

    // Password Confirmation...
    Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store']);

    //send email verification
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');

    // Two Factor Authentication...
    $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
        ? ['password.confirm'] : [];

    Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
        ->middleware($twoFactorMiddleware);

    Route::post('/user/two-factor-authentication-enable', [CustomTwoFactorController::class, 'enable'])->middleware(['auth_api']);

    Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
        ->middleware($twoFactorMiddleware);

    Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
        ->middleware($twoFactorMiddleware);

    Route::get('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
        ->middleware($twoFactorMiddleware);

    Route::post('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])
        ->middleware($twoFactorMiddleware);

    //update profile
    Route::put('/user/profile-information', [ProfileInformationController::class, 'update']);

    // get active user sessions
    Route::get('/user/active-sessions', [UserSessionsController::class, 'index']);

    Route::get('/user/active-sessions/{id}', [UserSessionsController::class, 'show']);

    Route::post('/user/revoke-session/{id}', [UserSessionsController::class, 'destroy']);
});

Route::middleware(['auth:sanctum'])->group(
    function () {
        Route::post('/products/create', [ProductController::class, 'create']);
        Route::get('/products/{id}', [ProductController::class, 'product']);
        Route::post('/products/{id}', [ProductController::class, 'store']);
        Route::get('/products/simple_bundle/{id}', [ProductController::class, 'getSimpleAndBundleData']);
        Route::post('/products/simple_bundle/{id}', [ProductController::class, 'storeSimpleAndBundleData']);
        Route::post('/products/add-category/{id}', [ProductController::class, 'storeCategories']);
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories/new', [CategoryController::class, 'store']);
        Route::post('/files', [FileController::class, 'store']);
        Route::post('/gallery/{id}', [GalleryController::class, 'storeProduct']);
        Route::post('/tags/add/{id}', [TagController::class, 'store']);
        Route::post('/tags/destroy/{id}', [TagController::class, 'destroy']);
        Route::post('/tags/destroy-all/{pid}', [TagController::class, 'destroyAllProductTags']);
        Route::get('/products/suggested/{id}', [ProductController::class, 'getSuggestedProducts']);
        Route::post('/products/suggested/{id}', [ProductController::class, 'storeSuggestedProducts']);
        Route::post('/products/suggested/destroy/{id}', [ProductController::class, 'destroySuggestedProducts']);
        Route::post('/products/suggested/destroy-all/{id}', [ProductController::class, 'destroyAllSuggestedProducts']);

        Route::post('/search_products', [ProductController::class, 'searchProducts']);
    }
);

Route::get('/fruits', function () {
    return [
        ['name' => 'ssdsdsdsd', 'age' => 22],
        ['name' => 'zzz 34334ssasas', 'age' => 54],
        ['name' => 'dfbcb dfd', 'age' => 47],
        ['name' => 'cxswee fgf', 'age' => 46],
        ['name' => 'mhjge vbvs', 'age' => 34],
    ];
})->middleware(['auth_api', 'admin']);

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
