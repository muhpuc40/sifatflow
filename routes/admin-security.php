<?php

// Loaded from routes/web.php inside the admin group.

use App\Http\Controllers\Admin\Security\DeviceLimitController;
use Illuminate\Support\Facades\Route;

Route::get('security/device-limits', [DeviceLimitController::class, 'index'])->name('admin.device-limits.index');
Route::put('security/device-limits/{type}', [DeviceLimitController::class, 'update'])->name('admin.device-limits.update');