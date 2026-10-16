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
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('api.auth.login');
});
Route::get('/portal/siswa/{nisn}', [PortalController::class, 'cekSiswa'])
    ->middleware('strict.rate_limit:10,1')
    ->name('api.portal.siswa');

// Observability & Health Monitoring Diagnostic Endpoint (Hardened against topology leaks)
Route::get('/health', function (\Illuminate\Http\Request $request, \App\Services\Monitoring\SystemHealthService $healthService) {
    $report = $healthService->getSystemHealthReport();
    $statusCode = $report['status'] === 'HEALTHY' ? 200 : 503;

    $healthKey = config('app.health_check_key', env('HEALTH_CHECK_KEY'));
    $providedKey = $request->header('X-Health-Key') ?? $request->query('key');
    $isAuthorized = ($healthKey && hash_equals((string) $healthKey, (string) $providedKey))
        || ($request->user() && in_array($request->user()->role, ['admin', 'superadmin', 'baak']));

    if (app()->environment('production') && !$isAuthorized) {
        return response()->json([
            'status' => $report['status'] === 'HEALTHY' ? 'UP' : 'DOWN',
            'timestamp' => $report['timestamp'],
        ], $statusCode);
    }

    return response()->json($report, $statusCode);
})->name('api.health');

// Real-time Bank Host-to-Host (H2H) Webhook (Protected by Idempotency Layer & Rate Limiter)
Route::post('/h2h/webhook', [\App\Http\Controllers\Api\H2hWebhookController::class, 'callback'])
    ->middleware(['idempotent', 'throttle:30,1'])
    ->name('api.h2h.webhook');
Route::post('/v1/h2h/callback', [\App\Http\Controllers\Api\H2hWebhookController::class, 'callback'])
    ->middleware(['idempotent', 'throttle:30,1'])
    ->name('api.v1.h2h.callback');

// Modern Payment Gateway Webhooks (Midtrans & Xendit Reconciliation)
Route::prefix('v1/payment-gateway')->middleware(['idempotent', 'throttle:60,1'])->group(function () {
    Route::post('/webhook/midtrans', [\App\Http\Controllers\Api\PaymentGatewayWebhookController::class, 'handleMidtrans'])->name('api.payment-gateway.midtrans');
    Route::post('/webhook/xendit', [\App\Http\Controllers\Api\PaymentGatewayWebhookController::class, 'handleXendit'])->name('api.payment-gateway.xendit');
    Route::post('/webhook', [\App\Http\Controllers\Api\PaymentGatewayWebhookController::class, 'handleUnified'])->name('api.payment-gateway.unified');
});

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

    // --- Smart KRS Registration (Pessimistic Locking & SKS Caps) ---
    Route::get('/krs', [\App\Http\Controllers\Api\SmartKrsController::class, 'index'])->name('api.krs.index');
    Route::post('/krs', [\App\Http\Controllers\Api\SmartKrsController::class, 'store'])->name('api.krs.store');
    Route::delete('/krs/{id}', [\App\Http\Controllers\Api\SmartKrsController::class, 'destroy'])->name('api.krs.destroy');

    // --- Geo-fenced QR Attendance (Protected by Rate Limiting & Idempotency) ---
    Route::post('/presensi/qr-session/{idBap}', [\App\Http\Controllers\Api\GeoAttendanceController::class, 'generateSessionToken'])->middleware('throttle:30,1')->name('api.presensi.qr-session');
    Route::post('/v1/attendance/bap/{idBap}/token', [\App\Http\Controllers\Api\GeoAttendanceController::class, 'generateSessionToken'])->middleware('throttle:30,1')->name('api.v1.attendance.token');
    Route::post('/v1/attendance/kelas/{idKelasKuliah}/token', [\App\Http\Controllers\Api\GeoAttendanceController::class, 'generateClassToken'])->middleware('throttle:30,1')->name('api.v1.attendance.class.token');
    Route::post('/presensi/submit-qr', [\App\Http\Controllers\Api\GeoAttendanceController::class, 'submitAttendance'])->middleware(['idempotent', 'throttle:20,1'])->name('api.presensi.submit-qr');
    Route::post('/presensi/store', [\App\Http\Controllers\Api\GeoAttendanceController::class, 'store'])->middleware(['idempotent', 'throttle:20,1'])->name('api.presensi.store');
    Route::post('/v1/attendance/submit', [\App\Http\Controllers\Api\GeoAttendanceController::class, 'submitAttendance'])->middleware(['idempotent', 'throttle:20,1'])->name('api.v1.attendance.submit');
    Route::post('/v1/attendance/store', [\App\Http\Controllers\Api\GeoAttendanceController::class, 'store'])->middleware(['idempotent', 'throttle:20,1'])->name('api.v1.attendance.store');

    // --- OBE Competency Radar Analytics ---
    Route::get('/analytics/obe-radar/{idSiswa}', function ($idSiswa) {
        $service = app(\App\Services\Analytics\ObeMatrixService::class);
        return response()->json(['success' => true, 'data' => $service->computeStudentCplRadar((int) $idSiswa)]);
    })->name('api.obe.radar');

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
