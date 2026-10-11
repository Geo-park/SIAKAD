<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mahasiswa\TagihanController;

Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
    Route::get('/tagihan/{bill}', [TagihanController::class, 'show'])->name('tagihan.show');
    Route::post('/tagihan/{bill}/bayar', [TagihanController::class, 'pay'])->name('tagihan.pay');
});
