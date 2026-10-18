<?php

// Loaded from routes/web.php INSIDE the admin group (login + active admin required).

use App\Http\Controllers\Admin\Sales\PaymentComboController;
use App\Http\Controllers\Admin\Sales\PaymentVoucherController;
use Illuminate\Support\Facades\Route;

// Combo offers: several courses sold together
Route::get('combos', [PaymentComboController::class, 'index'])->name('admin.combos.index');
Route::get('combos/create', [PaymentComboController::class, 'create'])->name('admin.combos.create');
Route::post('combos', [PaymentComboController::class, 'store'])->name('admin.combos.store');
Route::get('combos/{combo}/edit', [PaymentComboController::class, 'edit'])->whereNumber('combo')->name('admin.combos.edit');
Route::put('combos/{combo}', [PaymentComboController::class, 'update'])->whereNumber('combo')->name('admin.combos.update');
Route::delete('combos/{combo}', [PaymentComboController::class, 'destroy'])->whereNumber('combo')->name('admin.combos.destroy');

// Vouchers: one code for one student
Route::get('vouchers', [PaymentVoucherController::class, 'index'])->name('admin.vouchers.index');
Route::post('vouchers', [PaymentVoucherController::class, 'store'])->name('admin.vouchers.store');
Route::post('vouchers/{voucher}/resend', [PaymentVoucherController::class, 'resend'])->whereNumber('voucher')->name('admin.vouchers.resend');
Route::patch('vouchers/{voucher}/cancel', [PaymentVoucherController::class, 'cancel'])->whereNumber('voucher')->name('admin.vouchers.cancel');
Route::delete('vouchers/{voucher}', [PaymentVoucherController::class, 'destroy'])->whereNumber('voucher')->name('admin.vouchers.destroy');
