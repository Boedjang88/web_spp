# AUDIT API, INTEGRASI & ERROR HANDLING (PHASE 6)
**Sistem Informasi Akademik & Keuangan Terpadu (SIAKAD Enterprise & SPP)**  
*Dokumen Analisis RESTful API, Sanctum, Webhook, Circuit Breaker, Integrasi Eksternal & Kontrak Galat*

---

## 1. Ringkasan Eksekutif API & Integrasi

Audit API, Integrasi, dan Error Handling (Phase 6) mengevaluasi 81 rute API, arsitektur token **Laravel Sanctum**, webhook perbankan dan *payment gateway* (Bank Mitra H2H, Midtrans, Xendit), sistem *circuit breaker* (PDDIKTI Feeder, LMS Sync), serta saluran pengiriman pesan eksternal (Telegram, WhatsApp).

### Matriks Temuan Phase 6

| Kode Temuan | Tingkat Keparahan | Komponen / Endpoint | Ringkasan Masalah |
| :--- | :---: | :--- | :--- |
| **API-01** | **CRITICAL (P0)** | `GET /api/docs`<br>[`api-docs/index.blade.php#L371`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/api-docs/index.blade.php#L371) | Kredensial Superadmin (`admin@siakad.ac.id`, `password123`) tertanam hardcoded di JavaScript halaman dokumentasi publik tanpa autentikasi. |
| **API-02** | **HIGH (P1)** | `GET /api/health`<br>[`SystemHealthService.php#L25-L40`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Monitoring/SystemHealthService.php#L25-L40) | Endpoint diagnostik publik membocorkan versi PHP, versi Laravel, driver DB, metrik memori, dan ruang disk ke internet tanpa login. |
| **API-03** | **HIGH (P1)** | Sanctum Token Expiration<br>[`config/sanctum.php#L53`](file:///home/masgansss/Projects/Dev/web_spp/config/sanctum.php#L53) | Token Bearer Sanctum diatur tanpa batas kedaluwarsa (`expiration => null`), memberikan akses abadi jika token bocor. |
| **API-04** | **HIGH (P1)** | Kebocoran PII WhatsApp<br>[`WhatsAppNotificationService.php#L15`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Messaging/WhatsAppNotificationService.php#L15) | Nomor HP mahasiswa bernilai null di-*fallback* ke `'081234567890'`, mengirimkan surat peringatan akademik (EWS) ke orang asing. |
| **API-05** | **MEDIUM (P2)** | Inkoherensi Amplop Galat API<br>[`bootstrap/app.php#L23`](file:///home/masgansss/Projects/Dev/web_spp/bootstrap/app.php#L23) | `withExceptions` kosong; galat validasi `$request->validate()` dan unauthenticated 401 tidak menyertakan key `success: false`. |
| **API-06** | **MEDIUM (P2)** | Celah Idempotensi Finansial<br>[`IdempotencyMiddleware.php#L28`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Middleware/IdempotencyMiddleware.php#L28) | Middleware mengabaikan proteksi jika `Idempotency-Key` tidak dikirim; rute pembayaran SPP single/batch tidak dipasangi middleware. |
| **API-07** | **LOW (P3)** | Transformer Debt (Raw Eloquent)<br>[`SmartKrsController.php#L45`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Api/SmartKrsController.php#L45) | Sejumlah endpoint API mengembalikan model Eloquent mentah alih-alih API Resource transformer. |

---

## 2. Temuan Kritis (Critical Severity — P0)

### 2.1 Kredensial Administrator Tertanam di Halaman Publik `/api/docs`
- **Lokasi Kode:**
  - [`routes/web.php#L53`](file:///home/masgansss/Projects/Dev/web_spp/routes/web.php#L53)
  - [`resources/views/api-docs/index.blade.php#L366-L375`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/api-docs/index.blade.php#L366-L375)
- **Vulnerability Type:** CWE-798: Use of Hard-coded Credentials / CWE-306: Missing Authentication
- **Deskripsi Bukti:**
  Rute `GET /api/docs` didaftarkan di luar grup middleware `auth`, sehingga dapat diakses oleh siapa saja di internet tanpa kredensial. Pada file Blade view-nya, fungsi JavaScript `quickLogin()` secara terang-terangan memuat kredensial akun administrator:
  ```javascript
  async function quickLogin() {
      const res = await fetch('/api/auth/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ email: 'admin@siakad.ac.id', password: 'password123' })
      });
      const data = await res.json();
      if (data.data && data.data.token) {
          currentToken = data.data.token;
          document.getElementById('activeToken').value = currentToken;
          ...
  ```
- **Dampak Kritis:**
  Siapa pun di internet yang membuka URL `/api/docs` cukup menekan tombol *"Auto-Login & Set Token"*, atau memeriksa source code JavaScript halaman tersebut, untuk **langsung memperoleh token Sanctum Administrator** dan mengambil alih seluruh database universitas!
- **Rekomendasi Perbaikan:**
  1. Lindungi rute `/api/docs` dengan middleware `auth` dan `role:superadmin,admin` (atau aktifkan hanya pada lingkungan `APP_ENV=local`).
  2. Hapus fungsi `quickLogin()` dan kredensial hardcoded dari berkas view. Sediakan form input token manual bagi pengembang terotorisasi.

---

## 3. Temuan Tingkat Tinggi (High Severity — P1)

### 3.1 Kebocoran Informasi Sistem pada Endpoint Diagnostik Publik `GET /api/health`
- **Lokasi Kode:**
  - [`routes/api.php#L35-L39`](file:///home/masgansss/Projects/Dev/web_spp/routes/api.php#L35-L39)
  - [`app/Services/Monitoring/SystemHealthService.php#L25-L40`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Monitoring/SystemHealthService.php#L25-L40)
- **Vulnerability Type:** CWE-200: Exposure of Sensitive Information to an Unauthorized Actor
- **Deskripsi Bukti:**
  Uji coba langsung terhadap `GET /api/health` tanpa autentikasi menghasilkan response:
  ```json
  {
      "status": "HEALTHY",
      "timestamp": "2026-10-12T23:53:09+00:00",
      "environment": "local",
      "php_version": "8.2.12",
      "laravel_version": "11.47.0",
      "services": {
          "database": { "status": "OK", "connection": "sqlite" },
          "cache": { "status": "OK", "driver": "database" }
      },
      "system_metrics": {
          "disk": { "total_gb": 112.44, "used_gb": 83.04, "free_gb": 29.40 }
      }
  }
  ```
- **Dampak:**
  Penyerang dapat mengetahui versi presisi runtime PHP dan framework Laravel untuk mencari *Known Exploits / CVE*, mendeteksi driver database, serta memantau kapasitas disk server. Bila koneksi database terputus, `SystemHealthService#L62` bahkan membocorkan pesan error PDO mentah ke publik.
- **Rekomendasi Perbaikan:**
  - Untuk pemeriksaan liveness publik (*Kubernetes / Load Balancer probe*), cukup kembalikan status minimal: `{"status": "UP"}` dengan HTTP 200/503.
  - Pindahkan rincian metrik internal (PHP, disk, memory, latency) ke endpoint terproteksi: `GET /api/admin/system/health` dengan `auth:sanctum` dan `role:superadmin`.

---

### 3.2 Token Bearer Sanctum Tanpa Batas Kedaluwarsa (`expiration => null`)
- **Lokasi Kode:** [`config/sanctum.php#L53`](file:///home/masgansss/Projects/Dev/web_spp/config/sanctum.php#L53)
- **Bukti Faktual:**
  ```php
  'expiration' => null,
  ```
- **Dampak Keamanan:**
  Setiap token otentikasi yang diterbitkan melalui `POST /api/auth/login` aktif selamanya hingga dihapus manual. Jika perangkat mahasiswa/staf terinfeksi *malware* atau token dicuri dari log jaringan, penyerang memiliki akses permanen tanpa pernah kedaluwarsa.
- **Rekomendasi Perbaikan:**
  Atur waktu kedaluwarsa token Sanctum (misalnya 10.080 menit / 7 hari, atau 1.440 menit / 24 jam):
  ```php
  'expiration' => (int) env('SANCTUM_EXPIRATION', 1440), // 24 jam
  ```

---

### 3.3 Kebocoran PII Akademik via Nomor Telepon Fallback WhatsApp
- **Lokasi Kode:** [`app/Services/Messaging/WhatsAppNotificationService.php#L15, L41`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Messaging/WhatsAppNotificationService.php#L15)
- **Bukti Faktual:**
  ```php
  public function sendEwsRiskAlert(Siswa $siswa, string $riskType, string $description): void
  {
      $phone = $siswa->no_telp ?? '081234567890';
      // ... dispatch job
  }
  ```
- **Dampak Privasi (UU PDP):**
  Bila seorang mahasiswa belum mengisi kolom nomor telepon di biodatanya, notifikasi peringatan dini akademik berisiko tinggi (Drop-Out / nilai anjlok / tunggakan UKT) akan dikirimkan ke nomor sembarang **`081234567890`**.
- **Rekomendasi Perbaikan:**
  Jika `$siswa->no_telp` kosong atau tidak valid, batalkan pengiriman, catat peringatan di log sistem, dan jangan pernah menggunakan nomor telepon placeholder.

---

## 4. Temuan Tingkat Sedang & Rendah (P2 / P3)

### 4.1 Inkoherensi Format Amplop Galat API (*Error Envelope Divergence*)
- **Lokasi Kode:** [`bootstrap/app.php#L23-L25`](file:///home/masgansss/Projects/Dev/web_spp/bootstrap/app.php#L23-L25)
- **Bukti Faktual:**
  Blok `withExceptions` pada `bootstrap/app.php` dibiarkan kosong:
  ```php
  ->withExceptions(function (Exceptions $exceptions): void {
      //
  })
  ```
  Akibatnya, terjadi divergensi format JSON saat galat terjadi:
  
  **Kasus A: FormRequest turunan `BaseApiRequest` (Contoh: `StoreSppRequest`):**
  ```json
  {
      "success": false,
      "message": "Validasi gagal, silakan periksa input Anda.",
      "errors": { "tahun": ["Tahun SPP wajib diisi."] }
  }
  ```

  **Kasus B: Controller dengan `$request->validate()` langsung (Contoh: `SmartKrsController::store`):**
  ```json
  {
      "message": "The id kelas kuliah field is required. (and 1 more error)",
      "errors": { "id_kelas_kuliah": ["The id kelas kuliah field is required."] }
  }
  ```

  **Kasus C: Permintaan Unauthenticated (Sanctum 401):**
  ```json
  {
      "message": "Unauthenticated."
  }
  ```
- **Dampak Klien:**
  Aplikasi mobile atau frontend SPA yang bergantung pada kontrak seragam `data.success === false` akan mengalami kegagalan *parsing* atau menampilkan pesan yang tidak ramah pengguna.
- **Rekomendasi Perbaikan:**
  Konfigurasikan normalisasi exception global pada `bootstrap/app.php`:
  ```php
  $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
      if ($request->is('api/*') || $request->expectsJson()) {
          return response()->json([
              'success' => false,
              'message' => 'Validasi gagal, silakan periksa input Anda.',
              'errors' => $e->errors(),
          ], 422);
      }
  });

  $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
      if ($request->is('api/*') || $request->expectsJson()) {
          return response()->json([
              'success' => false,
              'message' => 'Unauthenticated. Silakan sertakan Bearer Token valid.',
          ], 401);
      }
  });
  ```

---

### 4.2 Celah Implementasi Idempotensi pada Pembayaran SPP
- **Lokasi Kode:** [`app/Http/Middleware/IdempotencyMiddleware.php#L28-L30`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Middleware/IdempotencyMiddleware.php#L28-L30)
- **Bukti Faktual:**
  ```php
  if (!$idempotencyKey) {
      return $next($request);
  }
  ```
  Jika klien tidak menyertakan header `Idempotency-Key` atau `nomor_transaksi_bank`, middleware langsung membiarkan request lewat tanpa perlindungan *duplicate submission*.
  Selain itu, pada [`routes/api.php#L112-L114`](file:///home/masgansss/Projects/Dev/web_spp/routes/api.php#L112-L114), rute pencatatan pembayaran kasir (`POST /api/pembayaran` dan `POST /api/pembayaran/batch`) **sama sekali tidak dipasangi middleware `idempotent`**!
- **Rekomendasi Perbaikan:**
  Pasang middleware `idempotent` pada seluruh endpoint mutasi finansial dan wajibkan keberadaan header kunci idempotensi untuk metode `POST` pembayaran.

---

### 4.3 Transformer Debt: Model Eloquent Mentah pada Sejumlah Endpoint
- **Lokasi Kode:** [`app/Http/Controllers/Api/SmartKrsController.php#L45-L50`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Api/SmartKrsController.php#L45-L50)
- **Bukti Faktual:**
  `SmartKrsController::index` mengembalikan model `TahunAkademik`, `Krs`, dan `KelasKuliah` secara mentah tanpa kelas `JsonResource`. Kolom-kolom internal database terekspos langsung, bertolak belakang dengan modul siswa/guru yang telah menggunakan `SiswaResource` dan `GuruResource`.
- **Rekomendasi Perbaikan:**
  Buat kelas resource resmi: `KrsResource`, `KelasKuliahResource`, dan `TahunAkademikResource` untuk mentransformasi data secara terstruktur dan aman.

---

## 5. Analisis Integrasi Eksternal & Pola Circuit Breaker

### 5.1 PDDIKTI Neo Feeder & LMS Sync Circuit Breaker
- **Evaluasi:** Pola *Circuit Breaker* pada [`app/Services/Integration/CircuitBreaker.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Integration/CircuitBreaker.php) telah dirancang dengan baik:
  - Tiga status: `CLOSED` (Normal), `OPEN` (Gagal berulang, request ditolak), `HALF_OPEN` (Mencoba kembali setelah masa cooldown 5 menit).
  - Terhubung aktif pada [`PddiktiFeederService::syncRecord`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Integration/PddiktiFeederService.php#L222) dan [`LmsSyncService::syncEnrollment`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Integration/LmsSyncService.php#L43).
  - Jika web service PDDIKTI Feeder di kementerian mengalami downtime/gangguan, circuit breaker mencegah antrean web server lokal kehabisan thread (*thread pool exhaustion*).

### 5.2 Notifikasi Asinkronus (Telegram & WhatsApp)
- **Evaluasi:** Pengiriman pesan Telegram dan WhatsApp telah didelegasikan ke antrean latar belakang (*background queue*):
  - `SendTelegramNotificationJob` (`tries = 3`, `timeout = 30`).
  - `SendWhatsAppNotificationJob` (`tries = 3`, `backoff = 5`).
  - Kegagalan API eksternal (misal Telegram API timeout) tidak menggagalkan transaksi akademik di database (*isolated side-effects*).

---

## 6. Standar Kontrak RESTful API Kanonikal

Seluruh endpoint REST API SIAKAD Enterprise wajib mematuhi kontrak terpadu:

```json
// Status 200 OK / 201 Created
{
    "success": true,
    "message": "Operasi berhasil dilakukan.",
    "data": { ... }
}

// Status 400 / 401 / 403 / 404 / 422 / 500
{
    "success": false,
    "message": "Deskripsi kesalahan yang ramah pengguna.",
    "errors": {
        "field_name": ["Rincian kesalahan input."]
    }
}
```
