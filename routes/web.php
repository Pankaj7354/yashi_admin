<?php

use App\Http\Controllers\admin\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtpsendController;


Route::get('/', function () {
    return view('admin_access.index');
});

// Grouping routes with 'auth' prefix and middleware
Route::group([
    'prefix' => 'auth',                 // URL prefix: /auth/...
    'middleware' => ['web'],             // Common middleware
    'as' => 'auth.',                     // Route name prefix: auth.*
], function () {

    // Guest-only routes (users not logged in)
    Route::group(['middleware' => ['guest']], function () {
        // Show Register Form & Handle Registration
        Route::match(['get', 'post'], '/register', [AuthController::class, 'register'])->name('register');

        // Show Login Form & Handle Login
        Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('login');

        // Forgot Password Form
        Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('forgot.form');
        Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('forgot.send');

        // Reset Password (with token)
        Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('reset.form');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.perform');

        // OTP Verification Routes
        Route::post('/send-otp', [OtpsendController::class, 'sendOtp'])->name('sendOtp');
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verifyOtp');  // Verify OTP
    });

    // Authenticated-only routes (users already logged in)
    Route::group(['middleware' => ['auth']], function () {
        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
