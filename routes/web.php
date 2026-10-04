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
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\SiswaController;
use App\Http\Controllers\Web\SppController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Api\ApiDocsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIAKAD & Billing School ERP Monolith Full-Stack
|--------------------------------------------------------------------------
*/

// --- Public Routes: Portal Siswa Mandiri, Employer Feedback & Interactive API Docs ---
Route::get('/', [CekTagihanController::class, 'index'])->name('cek.index');
Route::post('/cek-tagihan', [CekTagihanController::class, 'search'])->name('cek.search');
Route::get('/api/docs', [ApiDocsController::class, 'index'])->name('api.docs');
Route::get('/survey/employer/{token}', [\App\Http\Controllers\Web\EmployerFeedbackPortalController::class, 'show'])->name('employer.feedback.show');
Route::put('/survey/employer/{token}', [\App\Http\Controllers\Web\EmployerFeedbackPortalController::class, 'update'])->name('employer.feedback.update');

// --- Guest Authentication Routes ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// --- Authenticated Web Routes (RBAC Protected) ---
Route::middleware(['auth', 'pdp.consent'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // UU PDP Consent Routes
    Route::get('/pdp/consent', [\App\Http\Controllers\Web\PdpConsentController::class, 'show'])->name('pdp.consent.show');
    Route::post('/pdp/consent', [\App\Http\Controllers\Web\PdpConsentController::class, 'store'])->name('pdp.consent.store');

    // Dashboard (Personalized per-role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Self-Service Profile & Password Management
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // --- Next-Gen Smart KRS Portal ---
    Route::get('siakad/krs', [\App\Http\Controllers\Web\SmartKrsController::class, 'index'])->name('siakad.krs.index');
    Route::post('siakad/krs', [\App\Http\Controllers\Web\SmartKrsController::class, 'store'])->name('siakad.krs.store');
    Route::delete('siakad/krs/{id}', [\App\Http\Controllers\Web\SmartKrsController::class, 'destroy'])->name('siakad.krs.destroy');
    Route::get('siakad/analytics/performance', function () {
        $siswa = auth()->user()->siswa;
        return view('siakad.analytics.performance', compact('siswa'));
    })->name('siakad.analytics.performance');

    // --- Student Presensi Perkuliahan (GPS & QR Portal) ---
    Route::get('siakad/presensi', [\App\Http\Controllers\Web\StudentAttendanceController::class, 'index'])->name('siakad.presensi.index');
    Route::post('siakad/presensi/check-in', [\App\Http\Controllers\Web\StudentAttendanceController::class, 'checkIn'])->name('siakad.presensi.checkIn');

    // --- Student Tasks & LMS Submissions ---
    Route::get('siakad/tugas', [\App\Http\Controllers\Web\StudentAssignmentController::class, 'index'])->name('siakad.tugas.index');
    Route::get('siakad/tugas/{id}', [\App\Http\Controllers\Web\StudentAssignmentController::class, 'show'])->name('siakad.tugas.show');
    Route::post('siakad/tugas/{id}/submit', [\App\Http\Controllers\Web\StudentAssignmentController::class, 'submit'])->name('siakad.tugas.submit');

    // --- UKT Portal & Billing Breakdown ---
    Route::get('siakad/ukt', [\App\Http\Controllers\Web\UktPortalController::class, 'index'])->name('siakad.ukt.index');
    Route::post('siakad/ukt/bayar/{id}', [\App\Http\Controllers\Web\UktPortalController::class, 'bayar'])->name('siakad.ukt.bayar');
    Route::get('siakad/ukt/kwitansi/{id}', [\App\Http\Controllers\Web\UktPortalController::class, 'cetakKwitansi'])->name('siakad.ukt.kwitansi');

    // --- Student Biodata & PDP Profile Completion ---
    Route::get('siakad/biodata', [\App\Http\Controllers\Web\StudentBiodataController::class, 'edit'])->name('siakad.biodata.edit');
    Route::put('siakad/biodata/update', [\App\Http\Controllers\Web\StudentBiodataController::class, 'update'])->name('siakad.biodata.update');

    // --- Super Admin & Admin TU Only: User Management ---
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::resource('web/users', UserController::class)->names('web.users');
        Route::get('web/activity-logs', [ActivityLogController::class, 'index'])->name('web.activity-logs.index');
        Route::resource('web/guru', GuruController::class)->names('web.guru');
        Route::resource('web/mapel', MapelController::class)->names('web.mapel');
        Route::resource('web/kelas', KelasController::class)->names('web.kelas');
        Route::resource('web/spp', SppController::class)->names('web.spp');
        Route::resource('web/siswa', SiswaController::class)->names('web.siswa');
        Route::resource('web/pembayaran', PembayaranController::class)->names('web.pembayaran');
        Route::get('web/laporan', [LaporanController::class, 'index'])->name('web.laporan.index');
        Route::get('web/laporan/cetak', [LaporanController::class, 'cetak'])->name('web.laporan.cetak');
        Route::get('web/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('web.laporan.exportCsv');
    });

    // --- Academic Features (Admin & Dewan Guru) ---
    Route::middleware('role:superadmin,admin,guru')->group(function () {
        Route::resource('web/jadwal', JadwalController::class)->names('web.jadwal');
        Route::resource('web/nilai', NilaiController::class)->names('web.nilai');
        Route::get('web/presensi', [PresensiController::class, 'index'])->name('web.presensi.index');
        Route::post('web/presensi/batch', [PresensiController::class, 'storeBatch'])->name('web.presensi.batch');
    });

    // --- Student / General Accessible Print Endpoints ---
    Route::get('web/nilai/rapor/{id}', [NilaiController::class, 'cetakRapor'])->name('web.nilai.rapor');
    Route::get('web/siswa/{id}/surat-tagihan', [SiswaController::class, 'suratTagihan'])->name('web.siswa.suratTagihan');
    Route::get('web/siswa/{id}/kartu-ujian', [SiswaController::class, 'kartuUjian'])->name('web.siswa.kartuUjian');
    Route::get('web/pembayaran/{id}/cetak', [PembayaranController::class, 'cetakKwitansi'])->name('web.pembayaran.cetak');

    // --- Enterprise Print Endpoints: SKPI & Digital BAP ---
    Route::get('siakad/skpi/print', function () {
        $service = app(\App\Services\Skpi\SkpiService::class);
        $skpi = $service->generateBilingualSupplement(auth()->user()->id_siswa ?? 1);
        return view('siakad.skpi.print', compact('skpi'));
    })->name('siakad.skpi.print');

    Route::get('siakad/bap/{id}/print', function ($id) {
        $bap = \App\Models\BapPerkuliahan::with(['kelasKuliah.mataKuliah', 'dosen', 'ruangan'])->findOrFail($id);
        return view('siakad.bap.print', compact('bap'));
    })->name('siakad.bap.print');

    // --- Hardened Signed Document Routes (Anti-IDOR & Expiry Protected) ---
    Route::middleware('signed.download')->group(function () {
        Route::get('secure-docs/pembayaran/{id}', [\App\Http\Controllers\Web\SecureDocumentDownloadController::class, 'downloadReceipt'])->name('pembayaran.cetak.signed');
        Route::get('secure-docs/kartu-ujian/{id_krs}', [\App\Http\Controllers\Web\SecureDocumentDownloadController::class, 'downloadExamPass'])->name('krs.kartu-ujian.signed');
        Route::get('secure-docs/rapor/{id_siswa}/{id_tahun_akademik}', [\App\Http\Controllers\Web\SecureDocumentDownloadController::class, 'downloadRapor'])->name('khs.cetak.signed');
        Route::get('secure-docs/skpi/{id_siswa}', [\App\Http\Controllers\Web\SecureDocumentDownloadController::class, 'downloadSkpi'])->name('skpi.cetak.signed');
        Route::get('secure-docs/lms-material/{id}', [\App\Http\Controllers\Web\SecureDocumentDownloadController::class, 'downloadLmsMaterial'])->name('lms.material.download.signed');
    });
});