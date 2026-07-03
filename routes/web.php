<?php

use App\Http\Controllers\Admin\BahanBakuController;
use App\Http\Controllers\Admin\BahanKeluarController;
use App\Http\Controllers\Admin\BahanMasukController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\KonversiProdukController;
use App\Http\Controllers\Admin\MasaSimpanController;
use App\Http\Controllers\Admin\PenjualanController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\VarianProdukController;
use App\Http\Controllers\Owner\LaporanPenjualanController;
use App\Http\Controllers\Owner\LaporanPersediaanController;
use App\Http\Controllers\Owner\UserManagementController;
use App\Models\VarianProduk;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('app.dashboard'));

Route::get('/dashboard', function () {
    return redirect()->route('app.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:owner|admin'])
    ->prefix('app')
    ->name('app.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('app')
    ->name('app.')
    ->group(function () {
        Route::resource('kategori', KategoriController::class)->except('show');
        Route::resource('produk', ProdukController::class)->except('show');

        Route::resource('varian-produk', VarianProdukController::class)
            ->parameters(['varian-produk' => 'varianProduk'])
            ->except(['show']);

        Route::resource('bahan-baku', BahanBakuController::class)
            ->parameters(['bahan-baku' => 'bahanBaku'])
            ->except(['show']);

        // Penjualan tidak bisa dihapus — destroy dihapus dari route
        Route::resource('penjualan', PenjualanController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update']);

        Route::get('api/varian-harga/{varianProduk}', function (VarianProduk $varianProduk) {
            return response()->json([
                'harga'  => $varianProduk->harga,
                'nama'   => $varianProduk->nama_varian,
                'ukuran' => $varianProduk->ukuran,
            ]);
        })->name('api.varian-harga');

        Route::resource('bahan-masuk', BahanMasukController::class)
            ->parameters(['bahan-masuk' => 'bahanMasuk'])
            ->except(['show']);

        Route::resource('bahan-keluar', BahanKeluarController::class)
            ->parameters(['bahan-keluar' => 'bahanKeluar'])
            ->except(['show']);

        Route::resource('konversi-produk', KonversiProdukController::class)
            ->parameters(['konversi-produk' => 'konversiProduk'])
            ->except(['show']);


        Route::get('masa-simpan', [MasaSimpanController::class, 'index'])
            ->name('masa-simpan.index');
    });

Route::middleware(['auth', 'verified', 'role:owner'])
    ->prefix('app')
    ->name('app.')
    ->group(function () {
        Route::get('/laporan/penjualan', [LaporanPenjualanController::class, 'index'])->name('laporan.penjualan');
        Route::get('/laporan/penjualan/pdf', [LaporanPenjualanController::class, 'exportPdf'])->name('laporan.penjualan.pdf');
        Route::get('/laporan/persediaan', [LaporanPersediaanController::class, 'index'])->name('laporan.persediaan');
        Route::get('/laporan/persediaan/pdf', [LaporanPersediaanController::class, 'exportPdf'])->name('laporan.persediaan.pdf');
        Route::resource('users', UserManagementController::class)->except(['show']);
    });

require __DIR__ . '/auth.php';
