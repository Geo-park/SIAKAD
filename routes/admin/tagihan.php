<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\PaymentPeriodController;

Route::middleware(['auth', 'role:admin', 'fakultas.scope'])->prefix('admin')->name('admin.')->group(function () {
    // Payment Periods
    Route::get('/payment-periods', [PaymentPeriodController::class, 'index'])->name('payment-periods.index');
    Route::post('/payment-periods', [PaymentPeriodController::class, 'store'])->name('payment-periods.store');
    Route::put('/payment-periods/{paymentPeriod}', [PaymentPeriodController::class, 'update'])->name('payment-periods.update');
    Route::patch('/payment-periods/{paymentPeriod}/toggle', [PaymentPeriodController::class, 'toggleActive'])->name('payment-periods.toggle');

    // Bills & Payments
    Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
    Route::post('/tagihan/massal', [TagihanController::class, 'storeMass'])->name('tagihan.store-mass');
    Route::get('/tagihan/{bill}', [TagihanController::class, 'show'])->name('tagihan.show');
    Route::post('/tagihan/{bill}/bayar', [TagihanController::class, 'processPayment'])->name('tagihan.process-payment');
    Route::delete('/tagihan/{bill}', [TagihanController::class, 'destroy'])->name('tagihan.destroy');
});
