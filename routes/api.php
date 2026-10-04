<?php

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\MapelController;
use App\Http\Controllers\Api\NilaiController;
use App\Http\Controllers\Api\PembayaranController;
use App\Http\Controllers\Api\PortalController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\SppController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - RESTful API SIAKAD & SPP (Sanctum Protected)
|--------------------------------------------------------------------------
*/

// --- Public Routes (No Auth Required) ---
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
});
Route::get('/portal/siswa/{nisn}', [PortalController::class, 'cekSiswa'])->name('api.portal.siswa');

// --- Protected Routes (Requires Bearer Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth & Profile
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('api.auth.me');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    });

    // Dashboard & Metrics
    Route::get('/dashboard/summary', [DashboardController::class, 'summary'])->name('api.dashboard.summary');

    // Laporan Keuangan Rekap
    Route::get('/laporan/rekap', [LaporanController::class, 'rekap'])->name('api.laporan.rekap');

    // Audit Trail: Log Aktivitas
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('api.activity-logs.index');

    // --- Academic Modules (SIAKAD) ---
    // 1. Data Guru / Tenaga Pendidik
    Route::apiResource('guru', GuruController::class);

    // 2. Mata Pelajaran
    Route::apiResource('mapel', MapelController::class);

    // 3. Jadwal Pelajaran
    Route::apiResource('jadwal', JadwalController::class);

    // 4. Nilai & E-Rapor Siswa
    Route::get('/nilai/rapor/{id}', [NilaiController::class, 'rapor'])->name('api.nilai.rapor');
    Route::apiResource('nilai', NilaiController::class);

    // 5. Presensi Kehadiran Siswa
    Route::get('/presensi', [PresensiController::class, 'index'])->name('api.presensi.index');
    Route::post('/presensi/batch', [PresensiController::class, 'storeBatch'])->name('api.presensi.batch');

    // --- Master Data Sekolah ---
    Route::apiResource('kelas', KelasController::class);
    Route::apiResource('spp', SppController::class);

    // Data Siswa, Tagihan, & Surat Tagihan Resmi
    Route::get('/siswa/{id}/tunggakan', [SiswaController::class, 'tunggakan'])->name('api.siswa.tunggakan');
    Route::get('/siswa/{id}/surat-tagihan', [SiswaController::class, 'suratTagihan'])->name('api.siswa.surat-tagihan');
    Route::apiResource('siswa', SiswaController::class);

    // Transaksi Pembayaran, Batch, & Kwitansi
    Route::post('/pembayaran/batch', [PembayaranController::class, 'batchStore'])->name('api.pembayaran.batch');
    Route::get('/pembayaran/{id}/kwitansi', [PembayaranController::class, 'kwitansi'])->name('api.pembayaran.kwitansi');
    Route::apiResource('pembayaran', PembayaranController::class);
});
