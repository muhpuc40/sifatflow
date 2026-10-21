<?php

// Loaded from routes/web.php INSIDE the admin group (login + active admin required).

use App\Http\Controllers\Admin\Affiliate\AffiliateController;
use App\Http\Controllers\Admin\Affiliate\AffiliateSettingController;
use Illuminate\Support\Facades\Route;

// Affiliate program settings: one global row (commission rate, cookie days, payout day, auto-approve...)
Route::get('affiliate/settings', [AffiliateSettingController::class, 'edit'])->name('admin.affiliate-settings.edit');
Route::put('affiliate/settings', [AffiliateSettingController::class, 'update'])->name('admin.affiliate-settings.update');

// Affiliates: list + approve/reject a pending one, suspend/reactivate an approved one.
Route::get('affiliates', [AffiliateController::class, 'index'])->name('admin.affiliates.index');
Route::patch('affiliates/{profile}/approve', [AffiliateController::class, 'approve'])->name('admin.affiliates.approve');
Route::patch('affiliates/{profile}/reject', [AffiliateController::class, 'reject'])->name('admin.affiliates.reject');
Route::patch('affiliates/{profile}/suspend', [AffiliateController::class, 'suspend'])->name('admin.affiliates.suspend');
Route::patch('affiliates/{profile}/reactivate', [AffiliateController::class, 'reactivate'])->name('admin.affiliates.reactivate');
