<?php
use App\Http\Controllers\CetakKwitansiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CekTagihanController;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


// Halaman Utama (Cek Tagihan)
Route::get('/', [CekTagihanController::class, 'index'])->name('cek.index');
Route::post('/cek-tagihan', [CekTagihanController::class, 'search'])->name('cek.search');

// Route Cetak Kwitansi (yang tadi udah dibuat)
Route::middleware('auth')->get('/cetak-kwitansi/{id}', CetakKwitansiController::class)->name('cetak.kwitansi');

// --- AREA DARURAT (HAPUS NANTI SETELAH DEPLOY) ---


// 1. Jalur buat Migrasi Database
Route::get('/setup-database', function() {
    // Migrasi tabel
    Artisan::call('migrate --force');
    // Link storage gambar
    Artisan::call('storage:link');
    
    return '✅ Database berhasil dimigrasi & Storage terhubung!';
});

// 2. Jalur buat Bikin Admin
Route::get('/setup-admin', function() {
    // Cek dulu udah ada user belum biar gak duplikat
    if (User::where('email', 'admin@sekolah.id')->exists()) {
        return '⚠️ User admin sudah ada bos!';
    }

    User::create([
        'name' => 'Super Admin',
        'email' => 'admin@sekolah.id',
        'password' => Hash::make('password123'), // Password default
        'role' => 'admin',
    ]);
    
    return '✅ User Admin BERHASIL dibuat! Login: admin@sekolah.id | Pass: password123';
});