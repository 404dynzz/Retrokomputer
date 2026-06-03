<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\BarangRusakController;
use App\Http\Controllers\SupplierController;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/settings/active', [SettingController::class, 'active']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Supplier
    Route::apiResource('suppliers', SupplierController::class);

    // Produk
    Route::apiResource('produk', ProdukController::class);

    // Transaksi (POS)
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::post('/transaksi', [TransaksiController::class, 'store']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);

    // Pembelian (Restock)
    Route::get('/pembelian', [PembelianController::class, 'index']);
    Route::post('/pembelian', [PembelianController::class, 'store']);
    Route::get('/pembelian/{id}', [PembelianController::class, 'show']);

    // Retur
    Route::get('/retur', [ReturController::class, 'index']);
    Route::post('/retur', [ReturController::class, 'store']);

    // Laporan / Dashboard
    Route::get('/laporan/dashboard', [LaporanController::class, 'dashboard']);
    Route::get('/laporan/stok', [LaporanController::class, 'stok']);
    Route::get('/riwayat-stok', [LaporanController::class, 'riwayatStok']);

    // Settings (CRUD)
    Route::post('/settings', [SettingController::class, 'update']);

    // Barang Rusak / Hilang
    Route::get('/barang-rusak', [BarangRusakController::class, 'index']);
    Route::post('/barang-rusak', [BarangRusakController::class, 'store']);
});
