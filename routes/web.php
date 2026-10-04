<?php

use App\Http\Controllers\CekTagihanController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\KelasController;
use App\Http\Controllers\Web\PembayaranController;
use App\Http\Controllers\Web\SiswaController;
use App\Http\Controllers\Web\SppController;
use App\Http\Controllers\Api\ApiDocsController;
use App\Http\Controllers\Web\LaporanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Monolith Full-Stack SPP (Blade & Session Auth)
|--------------------------------------------------------------------------
*/

// --- Public Routes: Cek Tagihan & Interactive API Docs ---
Route::get('/', [CekTagihanController::class, 'index'])->name('cek.index');
Route::post('/cek-tagihan', [CekTagihanController::class, 'search'])->name('cek.search');
Route::get('/api/docs', [ApiDocsController::class, 'index'])->name('api.docs');

// --- Guest Authentication Routes ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// --- Authenticated Web Routes (Admin & Petugas) ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Web CRUD: Kelas
    Route::resource('web/kelas', KelasController::class)->names('web.kelas');

    // Web CRUD: SPP
    Route::resource('web/spp', SppController::class)->names('web.spp');

    // Web CRUD: Siswa
    Route::get('web/siswa/{id}/surat-tagihan', [SiswaController::class, 'suratTagihan'])->name('web.siswa.suratTagihan');
    Route::resource('web/siswa', SiswaController::class)->names('web.siswa');

    // Web CRUD & Cetak: Pembayaran
    Route::get('web/pembayaran/{id}/cetak', [PembayaranController::class, 'cetakKwitansi'])->name('web.pembayaran.cetak');
    Route::resource('web/pembayaran', PembayaranController::class)->names('web.pembayaran');

    // Laporan Keuangan SPP & Export
    Route::get('web/laporan', [LaporanController::class, 'index'])->name('web.laporan.index');
    Route::get('web/laporan/cetak', [LaporanController::class, 'cetak'])->name('web.laporan.cetak');
    Route::get('web/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('web.laporan.exportCsv');

    // Audit Trail: Log Aktivitas Sistem
    Route::get('web/activity-logs', [\App\Http\Controllers\Web\ActivityLogController::class, 'index'])->name('web.activity-logs.index');
});