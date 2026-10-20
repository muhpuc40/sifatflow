<?php

// Loaded from routes/web.php inside the admin group.

use App\Http\Controllers\Admin\Communication\EmailComposerController;
use Illuminate\Support\Facades\Route;

Route::get('communication/email', [EmailComposerController::class, 'create'])
    ->name('admin.communication.email.create');
Route::post('communication/email/image', [EmailComposerController::class, 'uploadImage'])
    ->name('admin.communication.email.image');
Route::post('communication/email', [EmailComposerController::class, 'send'])
    ->middleware('throttle:10,1')
    ->name('admin.communication.email.send');
