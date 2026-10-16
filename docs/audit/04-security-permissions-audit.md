# AUDIT KEAMANAN, PERIZINAN & PRIVASI DATA (PHASE 3)
**Sistem Informasi Akademik & Keuangan Terpadu (SIAKAD Enterprise & SPP)**  
*Dokumen Audit Bukti Nyata & Analisis Celah Keamanan (Vulnerability Assessment)*

---

## 1. Ringkasan Eksekutif Keamanan

Audit Keamanan dan Kontrol Akses (Phase 3) menguji seluruh lapisan pertahanan aplikasi, mencakup autentikasi (Web Session, Sanctum Token, Google SSO), otorisasi (RBAC, Laravel Policy, Filament Access), validasi integritas webhook perbankan, perlindungan terhadap IDOR (*Insecure Direct Object Reference*), serta kepatuhan regulasi privasi data **Undang-Undang Perlindungan Data Pribadi (UU PDP No. 27/2022)**.

### Matriks Temuan Berdasarkan Tingkat Keparahan (Severity Matrix)

| Kategori | Critical (P0) | High (P1) | Medium (P2) | Low / Informational (P3) | Total |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **Autentikasi & SSO** | 1 | 1 | 0 | 0 | **2** |
| **Webhook & Keuangan H2H** | 1 | 0 | 0 | 0 | **1** |
| **Kebocoran PII & Privasi (UU PDP)**| 1 | 0 | 1 | 0 | **2** |
| **Otorisasi & IDOR (Anti-Tampering)**| 2 | 2 | 1 | 0 | **5** |
| **Arsitektur Middleware & Kebijakan**| 0 | 2 | 1 | 1 | **4** |
| **Total** | **5** | **5** | **3** | **1** | **14** |

---

## 2. Temuan Kritis (Critical Severity — P0)

### 2.1 Celah Autentikasi Superadmin via Google SSO Fallback
- **Lokasi Kode:** [`app/Http/Controllers/Web/GoogleSsoController.php#L33-L64`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/GoogleSsoController.php#L33-L64)
- **Vulnerability Type:** CWE-287: Improper Authentication / Authentication Bypass
- **Deskripsi Bukti:**
  Pada implementasi OAuth Google, jika variabel `services.google.client_id` tidak terdefinisi di file konfigurasi (kondisi *default* instalasi tanpa integrasi Google API Console aktif), sistem secara otomatis mengeksekusi *fallback mock login*:
  ```php
  // GoogleSsoController.php lines 33-35 & 52-54
  $user = User::where('role', 'mahasiswa')->first() ?? User::first();
  Auth::login($user);
  ```
- **Dampak Kritis:**
  Bila sistem dijalankan tanpa akun mahasiswa atau bila diakses langsung pada endpoint `/auth/google` / `/auth/google/callback`, penyerang tanpa kredensial apapun (*unauthenticated*) akan langsung masuk (*logged in*) sebagai `User::first()`, yaitu akun **Super Administrator** dengan hak kontrol penuh atas seluruh sistem dan database!
- **Rekomendasi Perbaikan:**
  Hapus kode *mock bypass* sepenuhnya dari kode produksi. Jika konfigurasi Google SSO tidak lengkap, endpoint wajib melempar `503 Service Unavailable` atau *redirect* ke halaman login dengan notifikasi kesalahan: *"Fitur Google SSO belum dikonfigurasi oleh administrator."*

---

### 2.2 Celah Bypass Signature Webhook Bank Mitra H2H, Midtrans, & Xendit
- **Lokasi Kode:**
  - [`app/Services/Finance/H2hBillingService.php#L137-L142`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Finance/H2hBillingService.php#L137-L142)
  - [`app/Services/Finance/H2hBillingService.php#L188-L194`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Finance/H2hBillingService.php#L188-L194)
  - [`app/Services/Finance/H2hBillingService.php#L283-L287`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Finance/H2hBillingService.php#L283-L287)
  - [`app/Http/Controllers/Api/H2hWebhookController.php#L27`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Api/H2hWebhookController.php#L27)
- **Vulnerability Type:** CWE-347: Improper Verification of Cryptographic Signature
- **Deskripsi Bukti:**
  1. Pada `H2hWebhookController`, validasi parameter signature didefinisikan sebagai opsional: `'signature' => 'nullable|string'`.
  2. Pada `H2hBillingService`, seluruh verifikasi kriptografis (*HMAC SHA256 / SHA512 / Token*) dibungkus dalam kondisi kondisional longgar:
     ```php
     // Bank Mitra H2H (line 137)
     if ($tagihan->bankMitra && $providedSignature) {
         // Verifikasi signature hanya dilakukan jika $providedSignature disediakan!
     }

     // Midtrans Webhook (line 189)
     if ($bankMitra && !empty($bankMitra->server_key) && $signatureKey) {
         // Signature hanya dicek jika $signatureKey ada!
     }

     // Xendit Webhook (line 283)
     if ($bankMitra && !empty($bankMitra->secret_key) && $callbackToken) {
         // Token hanya dicek jika $callbackToken ada!
     }
     ```
  3. Bila penyerang mengirimkan payload JSON tanpa menyertakan `signature`, `signature_key`, atau `x-callback-token`, **blok verifikasi diabaikan sepenuhnya**, dan pembayaran dinyatakan lunas!
  4. Bila tabel `bank_mitras` kosong atau tidak memiliki konfigurasi aktif, verifikasi juga dilewati 100%.
- **Dampak Kritis:**
  Penyerang mana pun di internet dapat memalsukan pelunasan tagihan SPP/UKT senilai jutaan rupiah untuk nomor VA atau invoice mana saja, membuka blokir Smart KRS secara ilegal, dan merusak rekapitulasi buku besar keuangan universitas.
- **Rekomendasi Perbaikan:**
  Wajibkan verifikasi *signature* secara ketat:
  - Tolak dengan status `401 Unauthorized` / `403 Forbidden` jika *signature* atau *callback token* kosong.
  - Tolak jika konfigurasi mitra bank tidak ditemukan atau kunci rahasia (*secret key*) belum tersetting.
  - Gunakan `hash_equals()` konstan-waktu terhadap payload mentah (*raw body*).

---

### 2.3 Kebocoran Masif Data PII Mahasiswa, Nilai, & Presensi ke Publik (Pelanggaran UU PDP)
- **Lokasi Kode:**
  - [`app/Http/Controllers/Web/CekTagihanController.php#L38-L43`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/CekTagihanController.php#L38-L43)
  - [`app/Http/Controllers/Api/PortalController.php#L13-L94`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Api/PortalController.php#L13-L94)
- **Vulnerability Type:** CWE-200: Exposure of Sensitive Information to an Unauthorized Actor
- **Deskripsi Bukti:**
  1. Endpoint publik `POST /cek-tagihan` mengizinkan pencarian siswa/mahasiswa berdasarkan nama parsial tanpa login dan tanpa Captcha:
     ```php
     $siswa = Siswa::where('nisn', $searchKey)
         ->orWhere('nis', $searchKey)
         ->orWhere('nama', 'like', "%{$searchKey}%")
         ->first();
     ```
  2. Response yang dikembalikan ke browser mencakup:
     - Nama lengkap, NISN, NIS, kelas/jurusan, alamat rumah, nomor telepon orang tua/wali.
     - Riwayat lengkap tagihan dan pembayaran uang sekolah/UKT.
     - **Seluruh transkrip nilai akademik** (`$siswa->nilais()->with('mapel')`).
     - **Seluruh catatan presensi harian** (`$siswa->presensis()`).
  3. Endpoint publik REST `GET /api/portal/siswa/{nisn}` membocorkan data yang identik melalui API tanpa autentikasi.
- **Dampak Kritis:**
  - Pelanggaran berat terhadap **Undang-Undang Perlindungan Data Pribadi (UU No. 27 Tahun 2022)** Pasal 20, Pasal 27, dan Pasal 67 (ancaman pidana denda dan sanksi administratif atas pengungkapan data pribadi tanpa hak).
  - Siapa pun dapat melakukan *scraping* seluruh data mahasiswa universitas hanya dengan mengiterasi nama umum Indonesia (*"Budi"*, *"Ahmad"*, *"Siti"*, dsb.).
- **Rekomendasi Perbaikan:**
  - Hapus pencarian nama parsial dari `CekTagihanController`.
  - Wajibkan otentikasi (login) untuk melihat data transkrip nilai, presensi, dan data kontak keluarga.
  - Untuk cek tagihan publik (jika dipertahankan), batasi output HANYA pada: Nomor Tagihan, Nominal Terutang, Status Bayar, dan Nama Mahasiswa yang disamarkan (*masked*, contoh: `M******* F*****`).

---

### 2.4 Celah IDOR Masif pada Seluruh Endpoint Cetak Nilai, Kwitansi, dan Tagihan
- **Lokasi Kode:**
  - [`app/Http/Controllers/Web/NilaiController.php#L196`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/NilaiController.php#L196) (`GET web/nilai/khs/{id}`, `GET web/nilai/rapor/{id}`)
  - [`app/Http/Controllers/Web/PembayaranController.php#L187`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/PembayaranController.php#L187) (`GET web/pembayaran/{id}/cetak`)
  - [`app/Http/Controllers/Web/MahasiswaController.php#L165`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/MahasiswaController.php#L165) (`GET web/mahasiswa/{id}/surat-tagihan`, `kartu-ujian`)
  - [`routes/api.php#L125-L129`](file:///home/masgansss/Projects/Dev/web_spp/routes/api.php#L125-L129) (`/api/nilai/rapor/{id}`, `/api/siswa/{id}/tunggakan`, `/api/siswa/{id}/surat-tagihan`, `/api/siswa/{id}/kartu-ujian`, `/api/pembayaran/{id}/kwitansi`)
- **Vulnerability Type:** CWE-639: Authorization Bypass Through User-Controlled Key (IDOR)
- **Deskripsi Bukti:**
  Endpoint cetak dokumen akademik dan keuangan berada di luar grup middleware `role:superadmin,admin`, sehingga terbuka untuk seluruh pengguna terotentikasi (*any logged-in student*). Namun, controller **tidak melakukan verifikasi kepemilikan**:
  ```php
  // NilaiController.php
  public function cetakKhs($id) {
      $siswa = Siswa::with(...)->findOrFail($id);
      return view('siakad.khs.print', compact('siswa', ...));
  }
  ```
  Mahasiswa dengan ID 10 dapat mengakses `/web/nilai/khs/1` atau `/api/siswa/1/tunggakan` dan langsung melihat/mencetak nilai dan tagihan mahasiswa ID 1.
- **Rekomendasi Perbaikan:**
  Pasang otorisasi kepemilikan berbasis Policy pada setiap endpoint:
  ```php
  if ($user->isMahasiswa() && (int)$user->id_mahasiswa !== (int)$id) {
      abort(403, 'Akses Ditolak: Anda hanya berhak mengakses dokumen akademik pribadi.');
  }
  ```

---

### 2.5 Celah Eskalasi Identitas Siluman: Fallback Universal `Siswa::first()`
- **Lokasi Kode:**
  - [`app/Http/Controllers/Web/DbptaProposalController.php#L18, L35`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/DbptaProposalController.php#L18)
  - [`app/Http/Controllers/Web/DbptaBimbinganController.php#L19, L38`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/DbptaBimbinganController.php#L19)
  - [`app/Http/Controllers/Web/DbptaSidangController.php#L19, L32`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/DbptaSidangController.php#L19)
  - [`app/Http/Controllers/Web/DbptaPemberkasanController.php#L18, L32`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/DbptaPemberkasanController.php#L18)
  - [`app/Http/Controllers/Web/StudentEsuratController.php#L17, L37`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/StudentEsuratController.php#L17)
  - [`app/Http/Controllers/Web/WisudaController.php#L18, L38`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/WisudaController.php#L18)
  - [`app/Http/Controllers/Web/KknController.php#L23`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/KknController.php#L23)
  - [`routes/web.php#L223`](file:///home/masgansss/Projects/Dev/web_spp/routes/web.php#L223)
- **Vulnerability Type:** CWE-284: Improper Access Control / Privilege Escalation
- **Deskripsi Bukti:**
  Pada sedikitnya **14 lokasi controller**, identitas mahasiswa diselesaikan dengan pola:
  ```php
  $siswa = $user?->siswa ?? $user?->mahasiswa ?? Siswa::first();
  $siswaId = $siswa?->id ?? 1;
  ```
  Bila akun pengguna yang sedang login tidak memiliki relasi mahasiswa (misalnya staf admin, dosen, atau mahasiswa baru yang belum di-*link* profil akademiknya), sistem **secara otomatis meniru (*impersonate*) Siswa ID 1 (`Siswa::first()`)**!
- **Dampak Kritis:**
  Pengguna tersebut dapat mengajukan proposal skripsi, mendaftar sidang yudisium, membooking kelulusan wisuda, mengajukan e-surat, dan mencetak SKPI atas nama Mahasiswa ID 1, merusak seluruh integritas riwayat mahasiswa bersangkutan.
- **Rekomendasi Perbaikan:**
  Hapus seluruh fallback `?? Siswa::first()` dan `?? 1`. Jika `$siswa` atau `$mahasiswa` null, lemparkan `abort(403, 'Akun Anda tidak memiliki profil mahasiswa aktif.')`.

---

## 3. Temuan Tingkat Tinggi (High Severity — P1)

### 3.1 Ghost Authorization Policies: Kebijakan Didefinisikan tapi 0 Kali Dipanggil
- **Lokasi Kode:** [`app/Policies/MahasiswaPolicy.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Policies/MahasiswaPolicy.php)
- **Bukti Analisis:**
  - Hanya ada 1 kelas Policy di seluruh aplikasi: `MahasiswaPolicy.php`.
  - Pencarian terhadap `$this->authorize(`, `Gate::`, dan middleware `can:` di seluruh direktori `app/Http/Controllers` dan `routes/` menghasilkan **0 panggilan**.
  - Otorisasi objek diabaikan sama sekali; sistem hanya mengandalkan pengecekan role makro di level rute (`role:superadmin,admin`).

### 3.2 Dead Security Middlewares & Endpoint 2FA Hilang
- **Lokasi Kode:**
  - [`app/Http/Middleware/EnsureMfaVerified.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Middleware/EnsureMfaVerified.php)
  - [`app/Http/Middleware/EdomBlockingGateMiddleware.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Middleware/EdomBlockingGateMiddleware.php)
  - [`bootstrap/app.php#L14-L22`](file:///home/masgansss/Projects/Dev/web_spp/bootstrap/app.php#L14-L22)
- **Bukti Analisis:**
  - `EnsureMfaVerified` dan `EdomBlockingGateMiddleware` tidak terdaftar di alias middleware `bootstrap/app.php` dan tidak terpasang pada satu pun rute.
  - `EnsureMfaVerified` me-redirect pengguna ke `route('mfa.verify.show')`, padahal rute `mfa.verify.show` **sama sekali tidak ada** di `routes/web.php`. Jika diaktifkan saat ini, aplikasi akan mengalami *crash* `RouteNotFoundException`.
  - Kedua middleware tersebut memiliki bug: hanya mengecek `if ($user->role === 'siswa')`, mengabaikan mahasiswa perguruan tinggi (`$user->role === 'mahasiswa'`).

### 3.3 Eskalasi Hak Akses Tidak Disengaja melalui Ekspansi Alias Role
- **Lokasi Kode:** [`app/Http/Middleware/RoleMiddleware.php#L48-L53`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Middleware/RoleMiddleware.php#L48-L53)
- **Bukti Analisis:**
  Daftar alias untuk role `'admin'` dan `'baak'` mencakup role `'petugas'`:
  ```php
  'admin' => ['baak', 'admin', 'admin_akademik', 'admin_keuangan', 'petugas'],
  ```
  Akibatnya, setiap rute yang mensyaratkan `middleware('role:superadmin,admin')`—termasuk registrasi akun baru (`POST /api/auth/register`), manajemen user (`web/users`), dan log aktivitas audit—secara otomatis dapat diakses dan dimanipulasi oleh akun dengan role **`petugas`** (kasir loket keuangan)!

### 3.4 Nihil Throttling / Rate Limiting pada Web Login (`POST /login`)
- **Lokasi Kode:** [`routes/web.php#L60`](file:///home/masgansss/Projects/Dev/web_spp/routes/web.php#L60)
- **Bukti Analisis:**
  Rute API `POST /api/auth/login` dilindungi `throttle:10,1`, tetapi rute otentikasi web utama `POST /login` tidak memiliki middleware throttle sama sekali. Penyerang dapat melancarkan serangan *brute-force* atau *credential stuffing* terhadap akun dosen/mahasiswa/admin tanpa batas percobaan.

### 3.5 Kebocoran Verifikasi pada Hardened Signed URLs
- **Lokasi Kode:** [`app/Http/Middleware/VerifySignedDownloadUrl.php#L28-L37`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Middleware/VerifySignedDownloadUrl.php#L28-L37)
- **Bukti Analisis:**
  Middleware `signed.download` dimaksudkan untuk mencegah IDOR pada unduhan berkas rahasia, namun memiliki tiga kecacatan logika:
  1. Hanya mengecek `role === 'siswa'`, mengabaikan `role === 'mahasiswa'`.
  2. Mengecek `$request->route('id_siswa')`, padahal rute `secure-docs/pembayaran/{id}` menggunakan parameter `{id}` dan `secure-docs/kartu-ujian/{id_krs}` menggunakan `{id_krs}`. Nilai `$requestedSiswaId` selalu bernilai `null`, sehingga pengecekan IDOR tidak pernah berjalan!
  3. Rute unduhan lama yang tidak di-sign ([`routes/web.php#L212-L218`](file:///home/masgansss/Projects/Dev/web_spp/routes/web.php#L212-L218)) tetap dibiarkan aktif tanpa proteksi.

---

## 4. Temuan Tingkat Sedang (Medium Severity — P2)

### 4.1 Tabrakan ID Antar-Domain pada Scoping Filament Parent Panel
- **Lokasi Kode:**
  - [`app/Filament/Parent/Resources/NilaiObeResource.php#L50-L55`](file:///home/masgansss/Projects/Dev/web_spp/app/Filament/Parent/Resources/NilaiObeResource.php#L50-L55) (`where('id_siswa', $studentId)`)
  - [`app/Filament/Parent/Resources/TagihanUktResource.php#L49-L53`](file:///home/masgansss/Projects/Dev/web_spp/app/Filament/Parent/Resources/TagihanUktResource.php#L49-L53) (`where('id_mahasiswa', $studentId)`)
- **Bukti Analisis:**
  Akun wali memiliki field `id_siswa` atau `id_mahasiswa`. Tabel `siswas` dan `mahasiswas` adalah tabel terpisah dengan auto-increment independen. Jika wali ditautkan dengan `id_siswa = 5`, `TagihanUktResource` mencari tagihan untuk `id_mahasiswa = 5` di tabel mahasiswa, berpotensi mengekspos tagihan UKT milik mahasiswa lain.

### 4.2 Unggah File Avatar Langsung ke Webroot Publik
- **Lokasi Kode:** [`app/Http/Controllers/Web/ProfileController.php#L42-L47`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/ProfileController.php#L42-L47)
- **Bukti Analisis:**
  Avatar profil dipindahkan langsung ke direktori publik `public_path('uploads/avatars')` menggunakan `$file->getClientOriginalExtension()`. Meskipun divalidasi oleh `mimes:jpeg,png,jpg,webp`, praktik menyimpan berkas pengguna langsung di webroot tanpa UUID dan tanpa melewati `UploadSecurityGateway` berisiko jika server web salah mengkonfigurasi eksekusi skrip.

### 4.3 Bypass Persetujuan UU PDP pada Panel Filament
- **Lokasi Kode:** [`app/Providers/Filament/AdminPanelProvider.php#L71-L84`](file:///home/masgansss/Projects/Dev/web_spp/app/Providers/Filament/AdminPanelProvider.php#L71-L84)
- **Bukti Analisis:**
  Middleware `pdp.consent` (`UuPdpConsentMiddleware`) hanya dipasang pada rute web Blade di `routes/web.php#L68`. Panel Filament Admin (`/admin`) dan Panel Wali (`/wali`) tidak menyertakan middleware tersebut di pipeline-nya. Pengguna staf dan wali dapat mengakses data pribadi tanpa menandatangani persetujuan pemrosesan data digital.

---

## 5. Matriks Peran & Hak Akses (RBAC Matrix Saat Ini vs Rekomendasi Target)

```
+--------------------------------+--------------------+--------------------+--------------------+
| Modul / Fitur                  | Implementasi Saat  | Status Keamanan    | Rekomendasi Target |
|                                | Ini (Faktual)      | Audit              | (Hardened RBAC)    |
+--------------------------------+--------------------+--------------------+--------------------+
| Web Login Brute Force          | Unthrottled        | VULNERABLE (P1)    | throttle:5,1       |
| Google SSO Auth                | Fallback Superadmin| VULNERABLE (P0)    | Abort 503 / Strict |
| Bank H2H Webhook Callback      | Nullable Signature | VULNERABLE (P0)    | Mandatory HMAC     |
| Portal Cek Tagihan Publik      | Full PII, Nilai    | VULNERABLE (P0)    | Masked + Auth Only |
| Cetak KHS / Rapor Web          | IDOR (Any Student) | VULNERABLE (P0)    | Policy View Check  |
| Cetak Kwitansi Web             | IDOR (Any Student) | VULNERABLE (P0)    | Policy View Check  |
| API Cetak Kwitansi/Rapor       | IDOR (Any Student) | VULNERABLE (P0)    | Policy View Check  |
| DBPTA Thesis Proposal          | Fallback Siswa #1  | VULNERABLE (P0)    | Require Student Link|
| Wisuda Graduation Register     | Fallback Siswa #1  | VULNERABLE (P0)    | Require Student Link|
| User Provisioning (/register)  | Admin + Petugas(!) | VULNERABLE (P1)    | Superadmin Only    |
| Web User CRUD (/web/users)     | Admin + Petugas(!) | VULNERABLE (P1)    | Superadmin Only    |
| MFA 2FA Enforcement Gate       | Dead Middleware    | INACTIVE (P1)      | Register + Routes  |
| EDOM Evaluation Gate           | Dead Middleware    | INACTIVE (P1)      | Register + Routes  |
| Signed Document Downloads      | Bypass Logic Mahas.| VULNERABLE (P1)    | Strict Parameter ID|
| Filament Parent Panel Scoping  | ID Collision Risk  | RISK (P2)          | Unified Student ID |
+--------------------------------+--------------------+--------------------+--------------------+
```

---

## 6. Kepatuhan Regulasi Privasi Data (UU PDP No. 27/2022)

| Klausul UU PDP | Ketentuan Regulasi | Status Kepatuhan Saat Ini | Bukti / Catatan Audit |
| :--- | :--- | :---: | :--- |
| **Pasal 20** | Kewajiban memiliki dasar pemrosesan data pribadi sah (persetujuan eksplisit). | **PARSIAL** | Tabel `users` memiliki `consent_pdp_at`, namun rute publik membocorkan data tanpa consent. |
| **Pasal 27** | Kewajiban membatasi pemrosesan data pribadi sesuai tujuan awal. | **GAGAL** | Pencarian tagihan mengekspos transkrip nilai dan catatan kehadiran ke pihak ketiga unauthenticated. |
| **Pasal 35** | Kewajiban menjaga keamanan teknis data pribadi dari akses tanpa hak. | **GAGAL** | Celah IDOR pada 7 endpoint cetak memungkinkan mahasiswa mengunduh berkas rekan sejawat. |
| **Pasal 39** | Kewajiban mencegah akses data pribadi secara tidak sah (*Data Leakage*). | **GAGAL** | Cek tagihan publik dengan pencarian `%like%` memungkinkan *scraping* massal seluruh data sivitas akademika. |
| **Pasal 67** | Sanksi pidana dan denda atas penyebaran data pribadi tanpa persetujuan subjek data. | **RISIKO TINGGI** | Diperlukan penutupan akses publik PII segera (*Immediate Mitigation*). |

---

## 7. Rencana Aksi Remediasi Keamanan Prioritas

1. **Remediasi Darurat (Day 1 — P0):**
   - Hapus *auto-login fallback* di `GoogleSsoController.php`.
   - Wajibkan HMAC signature di `H2hWebhookController` dan `H2hBillingService`.
   - Tutup kebocoran PII di `CekTagihanController` & `PortalController` (hilangkan pencarian nama dan jangan kembalikan riwayat nilai/presensi ke publik).
   - Hapus fallback `?? Siswa::first()` di seluruh controller DBPTA, Wisuda, e-Surat, dan KKN.
2. **Pengerasan Otorisasi & Anti-IDOR (Day 2 — P1):**
   - Implementasikan Policy check pada semua rute cetak nilai, rapor, kwitansi, dan tagihan.
   - Perbaiki pemisahan role di `RoleMiddleware` agar role kasir/petugas tidak mewarisi hak akses administrasi sistem.
   - Tambahkan `throttle:5,1` pada rute `POST /login`.
3. **Pembersihan Arsitektur Keamanan (Day 3 — P2):**
   - Daftarkan `EnsureMfaVerified` dan `EdomBlockingGateMiddleware` di `bootstrap/app.php` setelah rute verifikasinya dilengkapi.
   - Terapkan `UuPdpConsentMiddleware` pada seluruh panel Filament.
