<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Middleware\EnsureAdminIsActive;
use Illuminate\Support\Facades\Route;

// This Laravel app is the admin panel (admin.shifatflow.com). The API is in routes/api.php.

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->name('admin.login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:login');
});

Route::middleware(['auth', EnsureAdminIsActive::class])->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('logout', [LoginController::class, 'destroy'])->name('admin.logout');

    // Add the next admin pages here. A sidebar item shows up as soon as its route exists
    // (see config/admin.php).
});
