# 🎓 Web SPP Enterprise Flagship Pro - Sistem Pembayaran SPP Sekolah (Laravel 11)

> **Ujian Akhir Semester (UAS) - Project-Based Assessment**  
> Proyek ini dibangun sebagai aplikasi **Enterprise Dual-Mode** untuk memenuhi kedua opsi penilaian UAS dengan kualitas standar industri:
> - **Opsi A: Aplikasi Web Monolith Full-Stack (Blade, Interactive Chart.js, Multi-Month Batch Payment, Portal Mandiri Siswa, Surat Tagihan Resmi Ber-KOP, Audit Trail Log Aktivitas, WhatsApp Integration, Laporan & CSV Export, Auth Session).**
> - **Opsi B: Backend RESTful API terproteksi Laravel Sanctum (Token Auth, Public Portal API, Interactive Web API Docs `/api/docs`, Automated Postman Collection & Environment).**

---

## 📑 Daftar Isi
1. [🌟 Fitur Unggulan (Enterprise Features)](#-fitur-unggulan-enterprise-features)
2. [🛠️ Panduan Instalasi & Menjalankan di Lokal](#-panduan-instalasi--menjalankan-di-lokal)
3. [🔑 Akun Default Demo](#-akun-default-demo)
4. [🖥️ Opsi A: Web Monolith Full-Stack (Blade UI)](#-opsi-a-aplikasi-web-monolith-full-stack-blade-ui)
5. [⚡ Opsi B: Backend RESTful API (Sanctum & Docs)](#-opsi-b-backend-restful-api-sanctum--docs)
6. [🌐 Interactive API Docs UI di Browser (`/api/docs`)](#-interactive-api-docs-ui-di-browser-apidocs)
7. [📮 Panduan Import Postman Collection](#-panduan-import-postman-collection)
8. [🧪 Automated Testing (49 PHPUnit Tests)](#-automated-testing-49-phpunit-tests)

---

## 🌟 Fitur Unggulan (Enterprise Features)

1. **👨‍🎓 Portal Mandiri Siswa & Wali Murid (`/` & `/cek-tagihan`)**:
   - Siswa atau wali murid dapat memasukkan 10 digit NISN untuk mengecek status pembayaran, total tunggakan, dan riwayat pelunasan.
   - Dilengkapi tombol cetak kwitansi per transaksi dan tombol cetak **Surat Rekapitulasi Tagihan SPP Resmi**.
2. **📄 Surat Tagihan SPP Resmi Format KOP Sekolah (`/web/siswa/{id}/surat-tagihan`)**:
   - Dokumen tagihan resmi A4 ber-KOP SMK Merdeka Belajar, nomor surat dinamis, tabel breakdown bulan lunas vs nunggak, petunjuk pembayaran rekening/loket, dan tanda tangan + stempel digital Kepala Tata Usaha.
3. **🛡️ Audit Trail / Log Aktivitas Sistem (`/web/activity-logs`)**:
   - Rekam jejak real-time setiap aksi pengguna (Login web/API, logout, penambahan/penghapusan siswa, perubahan tarif SPP, pencatatan transaksi batch, dan cetak kwitansi/surat) lengkap dengan alamat IP dan User-Agent.
4. **📊 Grafik Analitik Interaktif (Chart.js)**:
   - Line/Area chart tren penerimaan pembayaran SPP per bulan (Januari - Desember) untuk tahun berjalan.
   - Doughnut chart distribusi proporsi siswa aktif di setiap kelas.
5. **💳 Pembayaran Multi-Bulan Sekaligus (Batch Payment)**:
   - Petugas dapat mencentang beberapa bulan sekaligus (contoh: Juli, Agustus, September) dan memprosesnya dalam 1 kali transaksi.
   - Perhitungan total estimasi biaya realtime di frontend.
6. **📱 Integrasi WhatsApp Notification & Reminder (`wa.me`)**:
   - **Kirim Bukti Pembayaran via WA**: Satu klik tombol langsung membuka WhatsApp dengan template pesan resmi nomor kwitansi, periode, tanggal, dan nominal.
   - **Kirim Pengingat Tagihan SPP via WA**: Satu klik tombol di detail siswa yang memiliki tunggakan untuk mengirim daftar rincian bulan nunggak dan total rupiah langsung ke nomor wali murid.
7. **📑 Rekapitulasi Laporan Keuangan & Export CSV**:
   - Filter transaksi berdasarkan rentang tanggal (`Dari Tanggal` s/d `Sampai Tanggal`), filter per kelas, dan per petugas.
   - **Export CSV / Excel**: Download instan berkas data transaksi.
   - **Cetak Laporan Resmi**: Tampilan format kop surat sekolah, tabel rekapitulasi, kalimat terbilang, dan tanda tangan kepala sekolah/bendahara.
8. **⚡ Interactive API Docs UI di Browser (`/api/docs`)**:
   - Antarmuka visual bawaan web di mana dosen/penguji dapat langsung mengetes semua endpoint API, auto-login token generator, dan melihat respons JSON secara langsung di browser tanpa perlu menginstal aplikasi pihak ketiga.
9. **🔒 Keamanan & Validasi Anti-Duplikasi**:
   - Mencegah siswa membayar bulan dan tahun yang sama dua kali (Double-payment protection).
   - Validasi data terisolasi menggunakan Laravel Form Requests.

---

## 🛠️ Panduan Instalasi & Menjalankan di Lokal

### 1. Prasyarat
- PHP >= 8.2 (atau binary XAMPP di `/opt/lampp/bin/php`)
- SQLite (atau MySQL)
- Composer

### 2. Langkah Setup di Lokal
```bash
# 1. Masuk ke direktori proyek
cd /home/masgansss/Projects/Dev/web_spp

# 2. Salin environment file (jika belum ada)
cp .env.example .env

# 3. Generate App Key
/opt/lampp/bin/php artisan key:generate

# 4. Jalankan Migrasi Database & Seeder
/opt/lampp/bin/php artisan migrate:fresh --seed

# 5. Jalankan Local Development Server
/opt/lampp/bin/php artisan serve
```

Aplikasi aktif di: **`http://127.0.0.1:8000`**

---

## 🔑 Akun Default Demo

| Role | Email | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@sekolah.id` | `password123` | Akses penuh CRUD data master, transaksi SPP, laporan keuangan, log aktivitas audit, dan kelola user |
| **Petugas Loket** | `petugas@sekolah.id` | `password123` | Entri pembayaran SPP, cetak kwitansi, kirim WA, surat tagihan, dan rekap data |

---

## 🖥️ Opsi A: Aplikasi Web Monolith Full-Stack (Blade UI)

| Rute Web | Method | Keterangan Halaman |
| :--- | :--- | :--- |
| `/` | `GET` | **Portal Mandiri Siswa & Cek Tagihan Publik** |
| `/cek-tagihan` | `POST` | Proses pencarian data siswa & riwayat kwitansi via NISN |
| `/login` | `GET` / `POST` | Halaman & proses autentikasi session login petugas/admin |
| `/logout` | `POST` | Logout & invalidasi session |
| `/dashboard` | `GET` | **Dashboard analitik, grafik Chart.js, metrik keuangan & ringkasan** |
| `/web/activity-logs` | `GET` | **Audit Trail: Log aktivitas sistem real-time & riwayat akses** |
| `/web/kelas` | `RESOURCE` | Master data kelas (Index, Create, Store, Edit, Update, Delete) |
| `/web/spp` | `RESOURCE` | Master tarif SPP per tahun ajaran |
| `/web/siswa` | `RESOURCE` | Data siswa, info tunggakan, filter kelas, & search |
| `/web/siswa/{id}/surat-tagihan` | `GET` | **Cetak Surat Pemberitahuan & Rincian Tagihan Resmi (KOP Sekolah)** |
| `/web/pembayaran` | `RESOURCE` | Transaksi pembayaran (Single & Multi-Bulan Batch) |
| `/web/pembayaran/{id}/cetak` | `GET` | **Cetak Kwitansi Pembayaran Resmi (dengan kalimat terbilang rupiah)** |
| `/web/laporan` | `GET` | Filter rekapitulasi laporan transaksi pembayaran SPP |
| `/web/laporan/cetak` | `GET` | Format cetak dokumen rekapitulasi laporan |
| `/web/laporan/export-csv` | `GET` | Export data transaksi ke format CSV / Excel |

---

## ⚡ Opsi B: Backend RESTful API (Sanctum Protected)

Semua endpoint API mengembalikan format JSON standar:
```json
{
  "success": true,
  "message": "Pesan status berhasil",
  "data": { ... }
}
```

### Daftar Endpoint API Lengkap:

#### 1. Public Endpoints (No Auth)
- `POST /api/auth/login` - Autentikasi email & password, mengembalikan Bearer Token Sanctum.
- `POST /api/auth/register` - Registrasi petugas/admin baru.
- `GET /api/portal/siswa/{nisn}` - **Portal mandiri siswa: Profil, status lunas/nunggak, total rupiah, dan riwayat kwitansi via NISN.**

#### 2. Protected Endpoints (Requires `Authorization: Bearer <token>`)
- `GET /api/auth/me` - Profil user yang sedang login.
- `POST /api/auth/logout` - Revoke token Sanctum.
- `GET /api/dashboard/summary` - Metrik analitik pemasukan hari ini, bulan ini, total siswa, dan grafik bulanan.
- `GET /api/activity-logs` - **Audit trail log aktivitas sistem dengan filter tipe aksi & pencarian.**
- `GET /api/laporan/rekap` - Rekapitulasi laporan pemasukan dengan filter tanggal & kelas.
- `API RESOURCE /api/kelas` - CRUD Master Data Kelas.
- `API RESOURCE /api/spp` - CRUD Master Data SPP.
- `API RESOURCE /api/siswa` - CRUD Data Siswa.
- `GET /api/siswa/{id}/tunggakan` - Perhitungan otomatis daftar bulan nunggak & total tagihan rupiah.
- `GET /api/siswa/{id}/surat-tagihan` - **Generate payload surat tagihan resmi ber-KOP dan nomor surat.**
- `API RESOURCE /api/pembayaran` - CRUD Transaksi Pembayaran.
- `POST /api/pembayaran/batch` - **Transaksi pembayaran multi-bulan sekaligus (Batch Payment).**
- `GET /api/pembayaran/{id}/kwitansi` - Data kwitansi digital resmi dengan nomor KWT dan teks terbilang.

---

## 🌐 Interactive API Docs UI di Browser (`/api/docs`)

Buka URL: **`http://127.0.0.1:8000/api/docs`**
- Penguji/dosen dapat langsung menekan tombol **"Auto-Login & Set Token"**.
- Token Sanctum akan otomatis tersimpan di memory browser.
- Klik tombol **"Test"** pada endpoint mana pun untuk melihat live response JSON secara instan tanpa aplikasi eksternal!

---

## 📮 Panduan Import Postman Collection

Berkas Postman telah disediakan di direktori `postman/`:
1. `postman/SPP_Backend_REST_API.postman_collection.json`
2. `postman/SPP_Local_Environment.postman_environment.json`

### Cara Menggunakan di Postman:
1. Buka aplikasi Postman.
2. Klik tombol **Import** di pojok kiri atas.
3. Drag & drop kedua berkas JSON di atas.
4. Pilih environment **"Web SPP Local"** di pojok kanan atas.
5. Jalankan request `1. Authentication > Login (Admin)` &rarr; Token Sanctum otomatis tersimpan ke environment variable `{{bearer_token}}`.
6. Semua request lain siap dieksekusi secara otomatis!

---

## 🧪 Automated Testing (49 PHPUnit Tests)

Aplikasi dilengkapi dengan suite pengujian otomatis menyeluruh untuk menguji seluruh fungsionalitas Web Monolith dan RESTful API Sanctum.

### Menjalankan Test Suite:
```bash
/opt/lampp/bin/php ./vendor/bin/phpunit
```

### Hasil Pengujian:
```text
PHPUnit 10.5.65 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: /home/masgansss/Projects/Dev/web_spp/phpunit.xml

.................................................                 49 / 49 (100%)

Time: 00:03.377, Memory: 70.50 MB

OK (49 tests, 175 assertions)
```
Semua 49 skenario pengujian berhasil 100% tanpa error!
