<?php

use App\Http\Controllers\CekTagihanController;
use App\Http\Controllers\Web\ActivityLogController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\GuruController;
use App\Http\Controllers\Web\JadwalController;
use App\Http\Controllers\Web\KelasController;
use App\Http\Controllers\Web\LaporanController;
use App\Http\Controllers\Web\MapelController;
use App\Http\Controllers\Web\NilaiController;
use App\Http\Controllers\Web\PembayaranController;
use App\Http\Controllers\Web\PresensiController;
use App\Http\Controllers\Web\SiswaController;
use App\Http\Controllers\Web\SppController;
use App\Http\Controllers\Api\ApiDocsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIAKAD & Billing School ERP Monolith Full-Stack
|--------------------------------------------------------------------------
*/

// --- Public Routes: Portal Siswa Mandiri & Interactive API Docs ---
Route::get('/', [CekTagihanController::class, 'index'])->name('cek.index');
Route::post('/cek-tagihan', [CekTagihanController::class, 'search'])->name('cek.search');
Route::get('/api/docs', [ApiDocsController::class, 'index'])->name('api.docs');

// --- Guest Authentication Routes ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// --- Authenticated Web Routes (RBAC Protected) ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Academic Modules (SIAKAD) ---
    // 1. Guru & Tenaga Pendidik
    Route::resource('web/guru', GuruController::class)->names('web.guru');

    // 2. Mata Pelajaran
    Route::resource('web/mapel', MapelController::class)->names('web.mapel');

    // 3. Jadwal Pelajaran
    Route::resource('web/jadwal', JadwalController::class)->names('web.jadwal');

    // 4. Nilai & E-Rapor Siswa
    Route::get('web/nilai/rapor/{id}', [NilaiController::class, 'cetakRapor'])->name('web.nilai.rapor');
    Route::resource('web/nilai', NilaiController::class)->names('web.nilai');

    // 5. Presensi Kehadiran Siswa
    Route::get('web/presensi', [PresensiController::class, 'index'])->name('web.presensi.index');
    Route::post('web/presensi/batch', [PresensiController::class, 'storeBatch'])->name('web.presensi.batch');

    // --- Master Data Sekolah ---
    Route::resource('web/kelas', KelasController::class)->names('web.kelas');
    Route::resource('web/spp', SppController::class)->names('web.spp');

    // Data Siswa & Tagihan
    Route::get('web/siswa/{id}/surat-tagihan', [SiswaController::class, 'suratTagihan'])->name('web.siswa.suratTagihan');
    Route::resource('web/siswa', SiswaController::class)->names('web.siswa');

    // --- Transaksi Keuangan & SPP ---
    Route::get('web/pembayaran/{id}/cetak', [PembayaranController::class, 'cetakKwitansi'])->name('web.pembayaran.cetak');
    Route::resource('web/pembayaran', PembayaranController::class)->names('web.pembayaran');

    // Laporan Keuangan SPP & Export
    Route::get('web/laporan', [LaporanController::class, 'index'])->name('web.laporan.index');
    Route::get('web/laporan/cetak', [LaporanController::class, 'cetak'])->name('web.laporan.cetak');
    Route::get('web/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('web.laporan.exportCsv');

    // Audit Trail: Log Aktivitas Sistem
    Route::get('web/activity-logs', [ActivityLogController::class, 'index'])->name('web.activity-logs.index');
});