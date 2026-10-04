# Sistem Informasi Akademik & Keuangan Terpadu (SIAKAD Enterprise & SPP)

Sistem Informasi Akademik dan Keuangan Terpadu berbasis **PHP 8.2.12** dan **Laravel 11.x**. Sistem ini mengusung arsitektur Dual-Engine UI (Laravel Blade + Tailwind CSS untuk portal utama dan Filament PHP v3.2 untuk admin panel), didukung keamanan standar enterprise 2026, kepatuhan regulasi UU PDP No. 27/2022, integrasi Bank Host-to-Host (H2H) dengan proteksi idempotensi, serta Smart KRS berbasis *pessimistic locking* dan antrean terdistribusi.

---

## Daftar Isi
1. [Kredensial Akun Default (Demo)](#kredensial-akun-default-demo)
2. [Panduan Instalasi & Menjalankan di Lokal](#panduan-instalasi--menjalankan-di-lokal)
3. [Arsitektur 13 Modul Enterprise](#arsitektur-13-modul-enterprise)
4. [Fitur Keamanan & Hardening 2026](#fitur-keamanan--hardening-2026)
5. [Daftar Rute Web Monolith](#daftar-rute-web-monolith)
6. [Daftar Endpoint RESTful API (Sanctum)](#daftar-endpoint-restful-api-sanctum)
7. [Dokumentasi Interaktif & Postman](#dokumentasi-interaktif--postman)
8. [Automated Testing (PHPUnit 10.5)](#automated-testing-phpunit-105)

---

## Kredensial Akun Default (Demo)

Aplikasi telah dilengkapi seeder akun untuk seluruh level hak akses (*4-Tier RBAC Hierarchy*).

### 1. Akun Login Petugas, Pengajar, & Siswa (`/login`)

| Role / Tingkat Akses | Email | Password | Deskripsi Hak Akses |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `superadmin@sekolah.id` | `password123` | Akses penuh ke seluruh modul, audit trail, user provisioning, dan observability dashboard |
| **Admin TU / Keuangan** | `admin@sekolah.id` | `password123` | Manajemen akademik, data siswa/guru, tarif SPP, laporan keuangan, dan verifikasi KRS |
| **Petugas Loket / Kasir** | `petugas@sekolah.id` | `password123` | Entri pembayaran SPP single/batch, cetak kwitansi fisik, dan rekap penerimaan kas |
| **Dewan Guru / Dosen** | `guru@sekolah.id` | `password123` | Manajemen jadwal mengajar, presensi kelas harian, input penilaian, dan rekap e-rapor |
| **Siswa / Mahasiswa** | `siswa@sekolah.id` | `password123` | Portal mandiri siswa, registrasi Smart KRS, transkrip nilai, dan riwayat tagihan SPP |

---

### 2. Akses Portal Mandiri Siswa Tanpa Login (`/` atau `/cek-tagihan`)

Wali murid dan siswa dapat memeriksa status kelulusan akademik, riwayat pembayaran SPP, dan tunggakan secara instan menggunakan NISN terdaftar:

| Nama Siswa | NISN | NIS | Kelas | Keterangan Data |
| :--- | :--- | :--- | :--- | :--- |
| **Ahmad Fauzi** | `0051234567` | `2122001` | XII RPL 1 | Memiliki riwayat pembayaran lunas sebagian |
| **Siti Nurhaliza** | `0057654321` | `2122002` | XII RPL 1 | Siswa aktif |
| **Budi Santoso** | `0061122334` | `2223001` | XII TKJ 1 | Siswa aktif dengan tagihan berjalan |
| **Dewi Lestari** | `0069988776` | `2223002` | XII DKV 1 | Siswa aktif |

---

### 3. Kredensial API & Webhook Gateway (H2H Bank Partner)

| Komponen Gateway | Parameter / Header | Nilai / Format |
| :--- | :--- | :--- |
| **Endpoint Webhook H2H** | `URL` | `POST /api/h2h/webhook` |
| **Idempotency Key** | `Header: Idempotency-Key` | UUID v4 (Contoh: `e4b3c9a1-7d2e-4f1a-8c9b-1a2b3c4d5e6f`) |
| **Bank Mitra (BNI)** | `kode_bank` | `BNI` |
| **Secret Key HMAC** | `secret_key` | `secret_enterprise_bni_2026` |
| **Format Signature** | `HMAC-SHA256` | `hash_hmac('sha256', nomor_va . '|' . trx_id . '|' . nominal, secret_key)` |

---

## Panduan Instalasi & Menjalankan di Lokal

### 1. Prasyarat Sistem
- PHP >= 8.2.12 (dengan ekstensi `pdo_sqlite`, `pdo_mysql`, `openssl`, `mbstring`, `fileinfo`, `gd`)
- Composer >= 2.6
- SQLite / MySQL

### 2. Langkah Menjalankan Aplikasi
```bash
# 1. Masuk ke direktori proyek
cd /home/masgansss/Projects/Dev/web_spp

# 2. Konfigurasi berkas environment
cp .env.example .env

# 3. Generate Encryption Key
/opt/lampp/bin/php artisan key:generate

# 4. Jalankan Migrasi Database & Seeder Lengkap
/opt/lampp/bin/php artisan migrate:fresh --seed

# 5. Jalankan Server Pengembangan
/opt/lampp/bin/php artisan serve
```

Aplikasi dapat diakses melalui browser di: **`http://127.0.0.1:8000`**

---

## Arsitektur 13 Modul Enterprise

1. **Modul Core Academic Integration (BAAK)**: Fakultas, Program Studi, Kurikulum OBE, Mata Kuliah, Kelas Perkuliahan, dan Penjadwalan Bebas Bentrok.
2. **Next-Gen Student Portal**: Smart KRS dengan *pessimistic lock*, grafik analitik performa IPS/IPK, presensi QR geo-fenced, dan gerbang evaluasi dosen (EDOM).
3. **E-Dosen & Advisory Control Center**: Verifikasi KRS batch, multi-komponen penilaian (Tugas, UTS, UAS), dan Digital BAP audit-ready.
4. **Keuangan Host-to-Host (H2H)**: Billing VA dinamis, anti-replay webhook gateway, pelepasan kunci KRS instan (< 1 detik), dan rekonsiliasi SFTP bank otomatis.
5. **Tugas Akhir, Skripsi & Yudisium**: Kuota pembimbing skripsi, logbook digital interaktif, dan rubrik penilaian sidang skripsi.
6. **SKPI & OBE Matrix Engine**: Surat Keterangan Pendamping Ijazah (SKPI) bilingual dan radar matriks ketercapaian CPL/CPMK.
7. **Government Feeder & LMS Synchronization**: Pemetaan feeder PDDIKTI dan provisioning kelas daring Moodle/Canvas dengan *Circuit Breaker*.
8. **Executive Dashboard & Observability**: Real-time KPI widget dan health monitor performa server (`/api/health`).
9. **Tracer Study & Employer Feedback**: Portal pelacakan karier alumni dan formulir survei kepuasan industri berbasis token.
10. **Smart Facility & Resource Booking**: Peminjaman ruangan/lab dengan deteksi konflik jadwal otomatis.
11. **Comprehensive Audit Trail & Forensics**: Pencatatan riwayat perubahan data (sebelum/sesudah) dan identifikasi anomali IP/User-Agent.
12. **MFA, SSO & Multi-Tenant Security**: Otentikasi dua faktor berbasis TOTP Google Authenticator dan proteksi brute-force.
13. **Universal Data Processing (UU PDP)**: Enkripsi kolom sensitif database dan consent interceptor persetujuan privasi.

---

## Fitur Keamanan & Hardening 2026

- **Kepatuhan UU PDP No. 27/2022**: Kolom sensitif (`nik`, `nama_ibu_kandung`, `no_hp_wali`, `mfa_secret`) disimpan dalam bentuk terenkripsi pada basis data menggunakan Laravel native encryption cast. Akses web wajib menyetujui lembar digital consent (`/pdp/consent`).
- **Anti-Replay Idempotency Layer**: Mencegah *double-crediting* dan *race condition* pada webhook perbankan dengan *atomic locking* Redis/Cache dan header `X-Idempotent-Replay: true`.
- **SFTP Bank Reconciliation Command**: Perintah terjadwal `php artisan reconcile:bank-h2h` yang mengaudit log settlement bank terhadap buku besar SIAKAD.
- **Circuit Breaker Pattern**: Memutus panggilan integrasi eksternal (PDDIKTI Feeder & LMS) secara otomatis selama 5 menit jika terjadi 5 kegagalan berturut-turut untuk menjaga stabilitas sistem.
- **High-Concurrency KRS Throttling**: Double atomic locking per mahasiswa dan per kelas kuliah untuk menjamin kuota kursi akurat tanpa risiko kebuntuan database (*deadlock*).

---

## Daftar Rute Web Monolith

| Rute Web | Method | Akses Role | Keterangan Halaman |
| :--- | :--- | :--- | :--- |
| `/` | `GET` | Publik | Portal Mandiri Siswa & Cek Tagihan Publik |
| `/cek-tagihan` | `POST` | Publik | Pencarian data siswa via NISN |
| `/login` | `GET`, `POST` | Guest | Halaman autentikasi login |
| `/logout` | `POST` | Auth | Keluar sesi aplikasi |
| `/dashboard` | `GET` | Auth | Dashboard analitik terpersonalisasi |
| `/profile` | `GET`, `PUT` | Auth | Profil mandiri dan ubah password |
| `/pdp/consent` | `GET`, `POST` | Auth | Lembar persetujuan pemrosesan data pribadi |
| `/siakad/krs` | `GET`, `POST`, `DELETE` | Siswa | Portal pemilihan mata kuliah Smart KRS |
| `/siakad/analytics/performance` | `GET` | Siswa | Grafik tren performa IPK & IPS |
| `/web/users` | `RESOURCE` | Superadmin, Admin | Manajemen pengguna & hak akses 4-Tier |
| `/web/guru` | `RESOURCE` | Superadmin, Admin | Master data guru & pendidik |
| `/web/mapel` | `RESOURCE` | Superadmin, Admin | Master mata pelajaran & KKM |
| `/web/kelas` | `RESOURCE` | Superadmin, Admin | Master kelas perkuliahan |
| `/web/spp` | `RESOURCE` | Superadmin, Admin | Master tarif SPP per tahun ajaran |
| `/web/siswa` | `RESOURCE` | Superadmin, Admin | Data siswa & status administrasi |
| `/web/pembayaran` | `RESOURCE` | Superadmin, Admin, Petugas | Entri transaksi pembayaran SPP |
| `/web/laporan` | `GET` | Superadmin, Admin, Petugas | Laporan rekapitulasi kas masuk & export CSV |
| `/web/jadwal` | `RESOURCE` | Admin, Guru | Penjadwalan jam mengajar & ruang |
| `/web/nilai` | `RESOURCE` | Admin, Guru | Penilaian tugas, UTS, UAS, dan rapor |
| `/web/presensi` | `GET`, `POST` | Admin, Guru | Presensi kehadiran siswa harian |
| `/web/activity-logs` | `GET` | Superadmin, Admin | Audit Trail Log aktivitas sistem |

---

## Daftar Endpoint RESTful API (Sanctum)

Semua respons API menggunakan standar format JSON:
```json
{
  "success": true,
  "message": "Pesan status",
  "data": { ... }
}
```

- `POST /api/auth/login` — Autentikasi dan penerbitan Bearer Token Sanctum
- `GET /api/health` — Diagnostik performa server, latensi database, Redis, memory, dan disk
- `POST /api/h2h/webhook` — Webhook Host-to-Host pembayaran perbankan (*Idempotency Protected*)
- `GET /api/portal/siswa/{nisn}` — Data profil, tunggakan, dan nilai publik via NISN
- `GET /api/krs` — Daftar rencana studi dan pengambilan kelas
- `POST /api/krs` — Registrasi kelas perkuliahan berkecepatan tinggi
- `DELETE /api/krs/{id}` — Pembatalan kelas perkuliahan
- `POST /api/presensi/qr-session/{idBap}` — Generate sesi QR presensi terenkripsi geo-fenced
- `POST /api/presensi/submit-qr` — Validasi kehadiran presensi mahasiswa via koordinat GPS
- `GET /api/analytics/obe-radar/{idSiswa}` — Radar capaian kompetensi CPL/CPMK

---

## Dokumentasi Interaktif & Postman

1. **Web Interactive API Console**: Akses langsung melalui browser di **`http://127.0.0.1:8000/api/docs`** untuk mencoba seluruh endpoint API dengan fitur *Auto-Login Token Generator*.
2. **Postman Collection**: Berkas koleksi lengkap tersedia pada folder `postman/`:
   - `postman/SPP_Backend_REST_API.postman_collection.json`
   - `postman/SPP_Local_Environment.postman_environment.json`

---

## Automated Testing (PHPUnit 10.5)

Sistem telah diuji menggunakan PHPUnit 10.5 dengan tingkat keberhasilan 100% pada 109 pengujian fitur:

```bash
# Menjalankan seluruh test suite
/opt/lampp/bin/php ./vendor/bin/phpunit
```

### Hasil Pengujian:
```text
PHPUnit 10.5.65 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: /home/masgansss/Projects/Dev/web_spp/phpunit.xml

...............................................................  63 / 109 ( 57%)
..............................................                  109 / 109 (100%)

Time: 00:07.652, Memory: 80.50 MB

OK (109 tests, 419 assertions)
```
