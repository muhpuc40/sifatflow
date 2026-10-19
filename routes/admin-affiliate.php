<?php

// Loaded from routes/web.php INSIDE the admin group (login + active admin required).

use App\Http\Controllers\Admin\AffiliateSettingController;
use Illuminate\Support\Facades\Route;

// Affiliate program settings: one global row (commission rate, cookie days, payout day, auto-approve...)
Route::get('affiliate/settings', [AffiliateSettingController::class, 'edit'])->name('admin.affiliate-settings.edit');
Route::put('affiliate/settings', [AffiliateSettingController::class, 'update'])->name('admin.affiliate-settings.update');
