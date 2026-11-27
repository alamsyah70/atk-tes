<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\StockCardController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\AuditLogController;


/*
|--------------------------------------------------------------------------
| LOGIN & LOGOUT
|--------------------------------------------------------------------------
*/

Route::get('/', [LoginController::class, 'index'])
    ->name('login');

Route::post('/', [LoginController::class, 'authenticate'])
    ->name('login.authenticate');

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| DASHBOARD & PANDUAN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('/dashboard-admin', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard.admin');

    Route::get('/panduan', [DashboardController::class, 'panduan'])
        ->name('panduan');

});


/*
|--------------------------------------------------------------------------
| PENERIMAAN BARANG
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Lihat daftar penerimaan
    Route::get('/databarang/penerimaan-barang',
        [DashboardController::class, 'penerimaanbarang']
    )
        ->middleware('permission:penerimaan.view')
        ->name('databarang.penerimaan');

    // Tambah penerimaan
    Route::post('/databarang/penerimaan-barang',
        [DashboardController::class, 'store']
    )
        ->middleware('permission:penerimaan.create')
        ->name('databarang.penerimaan.store');

    // Cetak PDF penerimaan
    Route::get('/databarang/penerimaan-barang/{id}/pdf',
        [DashboardController::class, 'penerimaanPdf']
    )
        ->middleware('permission:penerimaan.view')
        ->name('databarang.penerimaan.pdf');

});


/*
|--------------------------------------------------------------------------
| MASTER BARANG
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Halaman master barang versi lama
    Route::get('/databarang/kelola-barang',
        [DashboardController::class, 'kelolabarang']
    )
        ->middleware('permission:barang.view')
        ->name('databarang.barang');

    // Update barang versi lama
    Route::put('/databarang/kelola-barang/{id}',
        [DashboardController::class, 'update']
    )
        ->middleware('permission:barang.update')
        ->name('databarang.barang.update');

    // Hapus barang versi lama
    Route::delete('/databarang/kelola-barang/{id}',
        [DashboardController::class, 'destroy']
    )
        ->middleware('permission:barang.delete')
        ->name('databarang.barang.destroy');


    // Daftar barang
    Route::get('/databarang/barangs',
        [BarangController::class, 'index']
    )
        ->middleware('permission:barang.view')
        ->name('barangs.index');

    // Simpan barang
    Route::post('/databarang/barangs',
        [BarangController::class, 'store']
    )
        ->middleware('permission:barang.create')
        ->name('barangs.store');

    // Update barang
    Route::put('/databarang/barangs/{barang}',
        [BarangController::class, 'update']
    )
        ->middleware('permission:barang.update')
        ->name('barangs.update');

    // Hapus barang
    Route::delete('/databarang/barangs/{barang}',
        [BarangController::class, 'destroy']
    )
        ->middleware('permission:barang.delete')
        ->name('barangs.destroy');


    // Pencarian barang / autocomplete
    Route::get('/barangs/search',
        [BarangController::class, 'search']
    )
        ->middleware('permission:barang.view')
        ->name('barangs.search');

    // Cek kode barang
    Route::get('/cek-kode-barang',
        [BarangController::class, 'cekKodeBarang']
    )
        ->middleware('permission:barang.view')
        ->name('barangs.cek-kode');

});


/*
|--------------------------------------------------------------------------
| KARTU STOK / STOCK CARD
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/kartu-stok',
        [StockCardController::class, 'index']
    )
        ->middleware('permission:stock-card.view')
        ->name('stock-card.index');

});


/*
|--------------------------------------------------------------------------
| SUPPLIER
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Lihat supplier
    Route::get('/supplier',
        [SupplierController::class, 'index']
    )
        ->middleware('permission:supplier.view')
        ->name('supplier.index');

    // Tambah supplier
    Route::post('/supplier',
        [SupplierController::class, 'store']
    )
        ->middleware('permission:supplier.create')
        ->name('supplier.store');

    // Update supplier
    Route::put('/supplier/{id}',
        [SupplierController::class, 'update']
    )
        ->middleware('permission:supplier.update')
        ->name('supplier.update');

    // Hapus supplier
    Route::delete('/supplier/{id}',
        [SupplierController::class, 'destroy']
    )
        ->middleware('permission:supplier.delete')
        ->name('supplier.destroy');

});


/*
|--------------------------------------------------------------------------
| MANAJEMEN USER
|--------------------------------------------------------------------------
| Hanya Super Admin dan IT Cabang.
| Controller juga memiliki pemeriksaan hak akses tambahan.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/create-user',
        [RegisterController::class, 'index']
    )
        ->middleware([
            'role:Super Admin|IT Cabang',
            'permission:user.view',
        ])
        ->name('create-user');

    Route::post('/create-user',
        [RegisterController::class, 'store']
    )
        ->middleware([
            'role:Super Admin|IT Cabang',
            'permission:user.create',
        ])
        ->name('create-user.store');

    Route::put('/create-user/{id}',
        [RegisterController::class, 'update']
    )
        ->middleware([
            'role:Super Admin|IT Cabang',
            'permission:user.update',
        ])
        ->name('create-user.update');

    Route::delete('/create-user/{id}',
        [RegisterController::class, 'destroy']
    )
        ->middleware([
            'role:Super Admin|IT Cabang',
            'permission:user.delete',
        ])
        ->name('create-user.destroy');

});


/*
|--------------------------------------------------------------------------
| PENGELUARAN BARANG
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Lihat pengeluaran
    Route::get('/pengeluaran',
        [PengeluaranController::class, 'index']
    )
        ->middleware('permission:pengeluaran.view')
        ->name('pengeluaran.index');

    // Tambah pengeluaran
    Route::post('/pengeluaran',
        [PengeluaranController::class, 'store']
    )
        ->middleware('permission:pengeluaran.create')
        ->name('pengeluaran.store');

    // Cetak PDF pengeluaran
    Route::get('/pengeluaran/{id}/pdf',
        [PengeluaranController::class, 'pdf']
    )
        ->middleware('permission:pengeluaran.view')
        ->name('pengeluaran.pdf');

    // Update pengeluaran
    Route::put('/pengeluaran/{id}',
        [PengeluaranController::class, 'update']
    )
        ->middleware('permission:pengeluaran.update')
        ->name('pengeluaran.update');

    // Hapus pengeluaran
    Route::delete('/pengeluaran/{id}',
        [PengeluaranController::class, 'destroy']
    )
        ->middleware('permission:pengeluaran.delete')
        ->name('pengeluaran.destroy');

});


/*
|--------------------------------------------------------------------------
| LAPORAN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('laporan')->name('laporan.')->group(function () {

    // Halaman laporan utama
    Route::get('/', [LaporanController::class, 'laporan'])
        ->middleware('permission:laporan.view')
        ->name('index');


    // Laporan penerimaan
    Route::get('/penerimaan', [LaporanController::class, 'penerimaan'])
        ->middleware('permission:laporan.view')
        ->name('penerimaan');

    Route::get('/penerimaan/pdf', [LaporanController::class, 'penerimaanPdf'])
        ->middleware('permission:laporan.export')
        ->name('penerimaan.pdf');


    // Laporan pengeluaran
    Route::get('/pengeluaran', [LaporanController::class, 'pengeluaran'])
        ->middleware('permission:laporan.view')
        ->name('pengeluaran');

    Route::get('/pengeluaran/pdf', [LaporanController::class, 'pengeluaranPdf'])
        ->middleware('permission:laporan.export')
        ->name('pengeluaran.pdf');


    // Laporan penyesuaian
    Route::get('/penyesuaian', [LaporanController::class, 'penyesuaian'])
        ->middleware('permission:laporan.view')
        ->name('penyesuaian');

    Route::get('/penyesuaian/pdf', [LaporanController::class, 'penyesuaianPdf'])
        ->middleware('permission:laporan.export')
        ->name('penyesuaian.pdf');


    // Rekap penerimaan per supplier
    Route::get('/penerimaan-per-supplier/pdf',
        [LaporanController::class, 'penerimaanSupplierPdf']
    )
        ->middleware('permission:laporan.export')
        ->name('penerimaan.supplier.pdf');


    // Rekap pemakaian per divisi
    Route::get('/pemakaian-per-divisi/pdf',
        [LaporanController::class, 'pemakaianDivisiPdf']
    )
        ->middleware('permission:laporan.export')
        ->name('pemakaian.divisi.pdf');


    // Rekap pemakaian tahunan
    Route::get('/pemakaian-tahunan/pdf',
        [LaporanController::class, 'pemakaianTahunanPdf']
    )
        ->middleware('permission:laporan.export')
        ->name('pemakaian.tahunan.pdf');

});


/*
|--------------------------------------------------------------------------
| STOK OPNAME
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('stok-opname')->name('stok-opname.')->group(function () {

    // Daftar stok opname
    Route::get('/', [StokOpnameController::class, 'index'])
        ->middleware('permission:stok-opname.view')
        ->name('index');

    // Buat stok opname
    Route::post('/', [StokOpnameController::class, 'store'])
        ->middleware('permission:stok-opname.create')
        ->name('store');

    // Detail stok opname
    Route::get('/{stokOpname}', [StokOpnameController::class, 'show'])
        ->middleware('permission:stok-opname.view')
        ->name('show');

    // Simpan hasil pemeriksaan fisik
    Route::put('/{stokOpname}', [StokOpnameController::class, 'update'])
        ->middleware('permission:stok-opname.update')
        ->name('update');

    // Finalisasi stok opname
    Route::post('/{stokOpname}/finalize', [StokOpnameController::class, 'finalize'])
        ->middleware('permission:stok-opname.finalize')
        ->name('finalize');

    // Export PDF
    Route::get('/{stokOpname}/export/pdf', [StokOpnameController::class, 'exportPdf'])
        ->middleware('permission:stok-opname.view')
        ->name('export-pdf');

    // Export Excel / CSV
    Route::get('/{stokOpname}/export/excel', [StokOpnameController::class, 'exportExcel'])
        ->middleware('permission:stok-opname.view')
        ->name('export-excel');

    // Cetak lembar pemeriksaan fisik
    Route::get('/{stokOpname}/print-sheet', [StokOpnameController::class, 'printSheet'])
        ->middleware('permission:stok-opname.view')
        ->name('print-sheet');

    // Pembatalan stok opname
    // Hanya role yang memiliki stok-opname.cancel.
    Route::post('/{stokOpname}/cancel', [StokOpnameController::class, 'cancel'])
        ->middleware('permission:stok-opname.cancel')
        ->name('cancel');

});

/*
|--------------------------------------------------------------------------
| AUDIT LOG
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('audit-logs')->name('audit-logs.')->group(function () {

    Route::get('/', [AuditLogController::class, 'index'])
        ->middleware('permission:audit-log.view')
        ->name('index');

});