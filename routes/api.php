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
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - RESTful API SIAKAD & SPP (Sanctum Protected)
|--------------------------------------------------------------------------
*/

// --- Public Routes (No Auth Required) ---
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
});
Route::get('/portal/siswa/{nisn}', [PortalController::class, 'cekSiswa'])->name('api.portal.siswa');

// --- Protected Routes (Requires Bearer Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth, Self-Service Profile & Password
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])->name('api.auth.me');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('api.auth.profile');
        Route::put('/change-password', [AuthController::class, 'changePassword'])->name('api.auth.change-password');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        
        // Admin-Only Provisioning: Register new user
        Route::post('/register', [AuthController::class, 'register'])->middleware('role:superadmin,admin')->name('api.auth.register');
    });

    // Dashboard & Metrics
    Route::get('/dashboard/summary', [DashboardController::class, 'summary'])->name('api.dashboard.summary');

    // --- User Management (Super Admin & Admin TU Only) ---
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });

    // --- Master Data & Keuangan SPP (Superadmin, Admin, Petugas) ---
    Route::middleware('role:superadmin,admin,petugas')->group(function () {
        // Financial Reports & Audit Log
        Route::get('/laporan/rekap', [LaporanController::class, 'rekap'])->name('api.laporan.rekap');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('api.activity-logs.index');

        // Master Data
        Route::apiResource('guru', GuruController::class);
        Route::apiResource('mapel', MapelController::class);
        Route::apiResource('kelas', KelasController::class);
        Route::apiResource('spp', SppController::class);
        Route::apiResource('siswa', SiswaController::class);

        // Pembayaran SPP (Store, Batch, Update, Delete)
        Route::post('/pembayaran/batch', [PembayaranController::class, 'batchStore'])->name('api.pembayaran.batch');
        Route::apiResource('pembayaran', PembayaranController::class);
    });

    // --- Academic Features (Admin & Dewan Guru) ---
    Route::middleware('role:superadmin,admin,guru')->group(function () {
        Route::apiResource('jadwal', JadwalController::class);
        Route::apiResource('nilai', NilaiController::class);
        Route::get('/presensi', [PresensiController::class, 'index'])->name('api.presensi.index');
        Route::post('/presensi/batch', [PresensiController::class, 'storeBatch'])->name('api.presensi.batch');
    });

    // --- Read-Only / Individual Data Access (All authenticated roles) ---
    Route::get('/nilai/rapor/{id}', [NilaiController::class, 'rapor'])->name('api.nilai.rapor');
    Route::get('/siswa/{id}/tunggakan', [SiswaController::class, 'tunggakan'])->name('api.siswa.tunggakan');
    Route::get('/siswa/{id}/surat-tagihan', [SiswaController::class, 'suratTagihan'])->name('api.siswa.surat-tagihan');
    Route::get('/siswa/{id}/kartu-ujian', [SiswaController::class, 'kartuUjian'])->name('api.siswa.kartu-ujian');
    Route::get('/pembayaran/{id}/kwitansi', [PembayaranController::class, 'kwitansi'])->name('api.pembayaran.kwitansi');
});
