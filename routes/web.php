<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Middleware\EnsureAdminIsActive;
use Illuminate\Support\Facades\Route;

// This Laravel app is the admin panel (admin.shifatflow.com). The API is in routes/api.php.

Route::middleware('guest')->group(function () {
    // Admin login: 1. password  2. send code  3. verify code
    Route::get('login', [LoginController::class, 'show'])->name('admin.login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:login');

    Route::get('login/verify', [LoginController::class, 'verifyPage'])->name('admin.login.verify');
    Route::post('login/send-code', [LoginController::class, 'sendCode'])->middleware('throttle:otp')->name('admin.login.send-code');
    Route::post('login/verify', [LoginController::class, 'verify'])->middleware('throttle:otp')->name('admin.login.verify.submit');

    // Device limit reached: choose an old device to sign out
    Route::get('login/devices', [LoginController::class, 'devices'])->name('admin.login.devices');
    Route::post('login/devices', [LoginController::class, 'chooseDevice'])->name('admin.login.devices.choose');

    Route::post('login/cancel', [LoginController::class, 'cancel'])->name('admin.login.cancel');
});

Route::middleware(['auth', EnsureAdminIsActive::class])->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('logout', [LoginController::class, 'destroy'])->name('admin.logout');

    // Add the next admin pages here. A sidebar item shows up as soon as its route exists
    // (see config/admin.php).
});
