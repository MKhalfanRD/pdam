<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\operator\OperatorController;
use App\Http\Controllers\pembayaran\PembayaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\warga\WargaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    //admin
    Route::prefix('admin')->group(function(){
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/{id}/show', [AdminController::class, 'show'])->name('admin.show');
        Route::get('/summary', [AdminController::class, 'summary'])->name('admin.summary');
        Route::patch('/validasi/{pembayaran_id}', [AdminController::class, 'validasi'])->name('admin.validasi');
        Route::get( '/{validasi_id}/edit', [AdminController::class, 'edit'])->name('admin.edit');
        Route::patch('/{validasi_id}/edit', [AdminController::class, 'update'])->name('admin.update');
    });
    //operator
    Route::prefix('operator')->group(function () {
        Route::get('/', [OperatorController::class, 'index'])->name('operator.index');
        Route::get('/create', [OperatorController::class, 'create'])->name('operator.create');
        Route::post('/store', [OperatorController::class, 'store'])->name('operator.store');
        Route::get('/{id}/edit', [OperatorController::class, 'edit'])->name('operator.edit');
        Route::put('/{id}/edit', [OperatorController::class, 'update'])->name('operator.update');
        Route::get('/history', [OperatorController::class, 'history'])->name('operator.history');
    });
    //warga
    Route::prefix('warga')->group(function () {
        Route::get('/', [WargaController::class, 'index'])->name('warga.index');
        Route::get('/{id}/edit', [WargaController::class, 'edit'])->name('warga.edit');
        Route::put('/{id}/edit', [WargaController::class, 'update'])->name('warga.update');
        Route::get('/history', [WargaController::class, 'history'])->name('warga.history');
        Route::get('/detailHistory', [WargaController::class, 'detailHistory'])->name('warga.detailHistory');
    });
    //pembayaran
    Route::prefix('pembayaran')->group(function () {
        Route::get('/', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::get('/create', [PembayaranController::class, 'create'])->name('pembayaran.create');
        Route::post('/', [PembayaranController::class, 'store'])->name('pembayaran.store');
        Route::get( '/{pembayaran}/edit', [PembayaranController::class, 'edit'])->name('pembayaran.edit');
        Route::put( '/{pembayaran}/', [PembayaranController::class, 'update'])->name('pembayaran.update');
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
