<?php

// Loaded from routes/web.php inside the admin group.

use App\Http\Controllers\Admin\System\LogController;
use App\Http\Controllers\Admin\System\ServerController;

use Illuminate\Support\Facades\Route;

Route::get('system/logs', [LogController::class, 'index'])
    ->name('admin.logs.index');
Route::get('system/server', [ServerController::class, 'index'])
    ->name('admin.server.index');
