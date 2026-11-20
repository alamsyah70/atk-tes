<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/', [LoginController::class, 'authenticate'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
Route::get('/panduan', [DashboardController::class, 'panduan'])->middleware('auth');

Route::middleware(['auth'])->group(function () {
    // Penerimaan Barang
    Route::get('/databarang/penerimaan-barang',
        [DashboardController::class, 'penerimaanbarang']
    )->name('databarang.penerimaan');

    Route::post('/databarang/penerimaan-barang',
        [DashboardController::class, 'store']
    )->name('databarang.penerimaan.store');

    Route::get('/databarang/penerimaan-barang/{id}/pdf',
    [DashboardController::class, 'penerimaanPdf']
)->name('databarang.penerimaan.pdf');

    // Daftar Barang
    Route::get('/databarang/kelola-barang',
        [DashboardController::class, 'kelolabarang']
    )->name('databarang.barang');

    // UPDATE
    Route::put('/databarang/kelola-barang/{id}',
        [DashboardController::class, 'update']
    )->name('databarang.barang.update');

    // DELETE
    Route::delete('/databarang/kelola-barang/{id}',
        [DashboardController::class, 'destroy']
    )->name('databarang.barang.destroy');

    Route::resource('databarang/barangs', BarangController::class)
    ->except(['show']);

    Route::get('/barangs/search', [BarangController::class, 'search'])
    ->name('barangs.search');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/supplier', [SupplierController::class, 'index'])
        ->name('supplier.index');

    Route::post('/supplier', [SupplierController::class, 'store'])
        ->name('supplier.store');

    Route::put('/supplier/{id}', [SupplierController::class, 'update'])
        ->name('supplier.update');

    Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])
        ->name('supplier.destroy');
});

// Dashboard Admin
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard-admin', [DashboardController::class, 'index']);

    Route::get('/create-user', [RegisterController::class, 'index'])
        ->name('create-user');

    Route::post('/create-user', [RegisterController::class, 'store'])
        ->name('create-user.store');

    Route::put('/create-user/{id}', [RegisterController::class, 'update'])
        ->name('create-user.update');

    Route::delete('/create-user/{id}', [RegisterController::class, 'destroy'])
        ->name('create-user.destroy');
});

// PENGELUARAN BARANG
Route::get('/pengeluaran', [PengeluaranController::class, 'index'])
    ->name('pengeluaran.index');

Route::post('/pengeluaran', [PengeluaranController::class, 'store'])
    ->name('pengeluaran.store');

Route::get('/pengeluaran/{id}/pdf', [PengeluaranController::class, 'pdf'])
    ->name('pengeluaran.pdf');

Route::middleware(['auth'])->group(function () {
    Route::get('/laporan/penerimaan', [LaporanController::class, 'penerimaan'])
        ->name('laporan.penerimaan');

    Route::get('/laporan/penerimaan/pdf', [LaporanController::class, 'penerimaanPdf'])
        ->name('laporan.penerimaan.pdf');

    Route::get('/laporan/pengeluaran', [LaporanController::class, 'pengeluaran'])
        ->name('laporan.pengeluaran');

    Route::get('/laporan/pengeluaran/pdf', [LaporanController::class, 'pengeluaranPdf'])
        ->name('laporan.pengeluaran.pdf');

    Route::get('/laporan/penyesuaian', [LaporanController::class, 'penyesuaian'])
        ->name('laporan.penyesuaian');

    Route::get('/laporan/penyesuaian/pdf', [LaporanController::class, 'penyesuaianPdf'])
        ->name('laporan.penyesuaian.pdf');
});
