<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Middleware\EnsureUserType;
use App\Http\Middleware\TrackDeviceActivity;
use Illuminate\Support\Facades\Route;

// Student sign-up: 1. register  2. login/send-code  3. login/verify
Route::post('v1/student/register', [AuthController::class, 'register'])->middleware('throttle:5,1');

// /api/v1/{admin|student|instructor}/...
Route::prefix('v1/{type}')->where(['type' => 'admin|student|instructor'])->group(function () {

    // Login: 1. password  2. send code  3. verify code
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('login/send-code', [AuthController::class, 'sendCode'])->middleware('throttle:otp');
    Route::post('login/verify', [AuthController::class, 'verify'])->middleware('throttle:otp');

    Route::middleware(['auth:sanctum', EnsureUserType::class, TrackDeviceActivity::class])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::get('devices', [DeviceController::class, 'index']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});