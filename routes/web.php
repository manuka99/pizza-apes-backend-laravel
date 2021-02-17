<?php

use App\Http\Controllers\AuthHandleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OptionsController;
use App\Http\Controllers\ProductVariationController;
use App\Http\Controllers\SocialAuthController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Artisan;
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
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/product/variants/{pid}', [ProductVariationController::class, 'getProductVariants']);
Route::post('/product/variants/allPosible/{pid}', [ProductVariationController::class, 'createAllPosibleVariations']);
Route::get('/product/variants/otherPosible/{pid}', [ProductVariationController::class, 'createOtherVariationsPosible']);
Route::post('/product/variants/custom/{pid}', [ProductVariationController::class, 'createCustomVariation']);
Route::post('/product/variants/update/{pid}', [ProductVariationController::class, 'updateProductVariants']);
Route::post('/product/variants/destroy-all/{pid}', [ProductVariationController::class, 'destroyAllVariants']);
Route::delete('/product/variants/destroy/{pvid}', [ProductVariationController::class, 'destroyVariant']);
Route::get('/options/{pid}', [OptionsController::class, 'getProductOptions']);


Route::get('/', function () {
    return view('welcome');
});

//Clear all:
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    // Artisan::call('optimize');
    // Artisan::call('route:cache');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    return "Cleared";
});

//Clear Cache facade value:
Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function () {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function () {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function () {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function () {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});


Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

Route::get("/session-id", function (Request $request) {
    return $request->session()->getId();
});

Route::get("/geoip", function (Request $request) {
    return geoip()->getLocation('113.59.217.14')->toArray();
})->middleware('auth');

//guest auth section
Route::group(['middleware' => ['guest']], function () {

    // login
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    //login post
    $limiter = config('fortify.limiters.login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            'guest',
            $limiter ? 'throttle:' . $limiter : null,
        ]));

    //google auth
    Route::get("/auth/google/redirect", [SocialAuthController::class, 'googleAuth']);
    Route::get('/auth/google/callback', [SocialAuthController::class, 'googleCallback']);
    //facebook auth
    Route::get("/auth/facebook/redirect", [SocialAuthController::class, 'facebookAuth']);
    Route::get('/auth/facebook/callback', [SocialAuthController::class, 'facebookCallback']);

    // Password Reset...
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');

    // Registration...
    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::group(['middleware' => 'guest_2fa'], function () {

    // forget 2fa login id
    Route::post('/forget/two-factor-login', [AuthHandleController::class, 'forgetTwoFactorLogin']);

    // Two Factor Authentication...
    $twoFactorLimiter = config('fortify.limiters.two-factor');

    Route::get('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'create'])
        ->name('two-factor.login');

    Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            $twoFactorLimiter ? 'throttle:' . $twoFactorLimiter : null,
        ]));
});

//auth section
Route::group(['middleware' => ['auth'],], function () {

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Email Verification...
    Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');

    // Profile Information...
    Route::put('/user/profile-information', [ProfileInformationController::class, 'update'])
        ->name('user-profile-information.update');

    // Passwords...
    Route::put('/user/password', [PasswordController::class, 'update'])
        ->name('user-password.update');

    // Password Confirmation...
    Route::get('/user/confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::get('/user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show'])
        ->name('password.confirmation');

    Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Two Factor Authentication...
    $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
        ? ['password.confirm']
        : [''];

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
});
