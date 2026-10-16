# AUDIT DATABASE, SKEMA & INTEGRITAS DATA (PHASE 4)
**Sistem Informasi Akademik & Keuangan Terpadu (SIAKAD Enterprise & SPP)**  
*Dokumen Analisis Integritas Skema, Foreign Key, Konkurensi & Batasan Transaksi*

---

## 1. Ringkasan Eksekutif Integritas Data

Audit Skema dan Integritas Database (Phase 4) menganalisis 64 tabel, 34 berkas migrasi, ratusan batasan relasional (*foreign keys*), indeks, batasan transaksi atomik (*ACID boundaries*), dan mekanisme penguncian konkurensi (*locking concurrency*).

Database saat ini berada pada kondisi transisi arsitektural hibrida (*Dual-Domain Architecture*) antara sistem warisan sekolah (**SPP/Siswa/Guru**) dan sistem enterprise perguruan tinggi (**SIAKAD/Mahasiswa/Dosen**). Transisi yang belum tuntas ini menimbulkan divergensi foreign key, risiko penghapusan berantai pada buku besar keuangan (*dangerous cascading deletes*), 64 foreign key tanpa indeks, serta tidak adanya *unique constraint* di level database untuk mencegah *double payment* dan *double booking*.

### Ringkasan Metrik Skema Database

| Parameter Metrik | Nilai Faktual | Keterangan / Status |
| :--- | :---: | :--- |
| **Total Tabel Database** | 64 Tabel | Termasuk tabel sistem, cache, jobs, dan modul enterprise |
| **Berkas Migrasi** | 34 Migrasi | Seluruhnya memiliki method `up()` dan `down()` |
| **Pelanggaran FK Aktif (`PRAGMA foreign_key_check`)** | 0 Pelanggaran | Integritas baris saat ini valid berkat seeder terkoordinasi |
| **Divergensi Relasi Siswa vs Mahasiswa** | 10 vs 3 Tabel | 10 tabel akademik tinggi merujuk `siswas`, hanya 3 merujuk `mahasiswas` |
| **Divergensi Relasi Guru vs Dosen** | 5 vs 4 Tabel | 5 tabel akademik tinggi merujuk `gurus`, 4 merujuk `dosens` |
| **Foreign Key Tanpa Indeks Database** | **64 Foreign Keys** | Menyebabkan *full table scan* pada traversal relasi dan CASCADE |
| **Cascading Delete Berisiko Tinggi** | 3 Relasi Kritis | Kasir terhapus menghapus seluruh transaksi SPP; Tagihan VA menghapus bukti bank |
| **Tabel Tanpa DB Unique Constraint Kritis** | `pembayarans`, `presensi_mahasiswas` | Rawan *race condition* pembayaran ganda dan presensi ganda |

---

## 2. Temuan Kritis Integritas Skema (P0 — Critical)

### 2.1 Anomali Akuntansi: Penghapusan Berantai Buku Besar Keuangan (`ON DELETE CASCADE`)
- **Lokasi Migrasi:**
  - `database/migrations/2026_01_04_000004_create_pembayarans_table.php`
  - `database/migrations/2026_10_04_172000_create_enterprise_siakad_finance_h2h_tables.php`
- **Bukti Relasi Skema:**
  ```sql
  -- Tabel pembayarans
  FOREIGN KEY (id_petugas) REFERENCES users(id) ON DELETE CASCADE;
  FOREIGN KEY (id_siswa) REFERENCES siswas(id) ON DELETE CASCADE;

  -- Tabel transaksi_h2hs
  FOREIGN KEY (id_tagihan_va) REFERENCES tagihan_vas(id) ON DELETE CASCADE;
  ```
- **Dampak Kritis:**
  1. **Kasir Dihapus = Buku Besar Lenyap:** Bila akun seorang petugas loket/kasir dihapus dari tabel `users`, mesin database secara otomatis **menghapus permanen seluruh riwayat transaksi pembayaran SPP yang pernah dicatat oleh kasir tersebut**! Ini merupakan pelanggaran fatal terhadap prinsip dasar akuntansi (*accounting ledger immutability*).
  2. **Mahasiswa Dihapus = Riwayat Finansial Lenyap:** Jika data profil siswa dihapus, seluruh bukti transaksi pembayaran senilai puluhan/ratusan juta rupiah musnah tanpa jejak audit.
  3. **Invoice Dihapus = Settlement Bank Hilang:** Jika tagihan VA dihapus dari `tagihan_vas`, rekaman pembayaran bank (`transaksi_h2hs`) ikut terhapus, menghilangkan bukti rekonsiliasi perbankan.
- **Rekomendasi Perbaikan:**
  Ubah batasan relasi pada entitas finansial:
  - `id_petugas`: Gunakan `ON DELETE RESTRICT` (mencegah penghapusan kasir yang memiliki riwayat transaksi) atau `ON DELETE SET NULL` dengan field audit terarsip (`nama_petugas_snapshot`).
  - `id_siswa` / `id_mahasiswa`: Gunakan `ON DELETE RESTRICT`. Siswa dengan riwayat finansial tidak boleh dihapus (*soft-delete* atau penonaktifan status).
  - `transaksi_h2hs.id_tagihan_va`: Gunakan `ON DELETE RESTRICT`.

---

### 2.2 Perpecahan Identitas Skema: Foreign Key Divergence (`siswas` vs `mahasiswas`, `gurus` vs `dosens`)
- **Lokasi Migrasi:**
  - `database/migrations/2026_10_04_170000_create_enterprise_siakad_core_tables.php`
  - `database/migrations/2026_10_04_171000_create_enterprise_siakad_krs_attendance_tables.php`
  - `database/migrations/2026_10_04_173000_create_enterprise_siakad_thesis_skpi_obe_tables.php`
  - `database/migrations/2026_10_04_200000_create_university_enterprise_refactored_tables.php`
- **Bukti Faktual Skema:**
  Tabel-tabel universitas yang seharusnya homogen terpecah ke dua dunia:

  ```
  +-------------------------------------+-------------------------------------+
  | Merujuk ke `siswas` (Sekolah)       | Merujuk ke `mahasiswas` (Univ)      |
  +-------------------------------------+-------------------------------------+
  | krs.id_siswa                        | tagihan_ukts.id_mahasiswa           |
  | financial_clearances.id_siswa       | pembayaran_ukts.id_mahasiswa        |
  | kkn_registrasis.id_siswa            | presensi_mahasiswas.id_mahasiswa    |
  | mbkm_konversis.id_siswa             |                                     |
  | presensi_kuliahs.id_siswa           |                                     |
  | skpi_aktivitas.id_siswa             |                                     |
  | surat_akademiks.id_siswa            |                                     |
  | tagihan_vas.id_siswa                |                                     |
  | tracer_studies.id_siswa             |                                     |
  | tugas_akhirs.id_siswa               |                                     |
  +-------------------------------------+-------------------------------------+
  ```

  Hal serupa terjadi pada tenaga pendidik:
  ```
  +-------------------------------------+-------------------------------------+
  | Merujuk ke `gurus` (Sekolah)        | Merujuk ke `dosens` (Univ)          |
  +-------------------------------------+-------------------------------------+
  | jadwal_kuliahs.id_guru              | kelas_kuliahs.id_dosen              |
  | krs.id_dosen_wali                   | mahasiswas.id_dosen_pa              |
  | logbook_bimbingans.id_guru          | bap_perkuliahans.id_dosen           |
  | pembimbing_skripsis.id_guru         | kkn_registrasis.id_dosen_dpl        |
  | penilaian_sidangs.id_guru           |                                     |
  +-------------------------------------+-------------------------------------+
  ```
- **Dampak Kritis:**
  1. Jika tabel `siswas` dan `mahasiswas` memiliki urutan auto-increment berbeda (misal Mahasiswa Budi memiliki `mahasiswas.id = 5` dan `siswas.id = 12`), maka:
     - Tagihan UKT mencari ID 5 di `tagihan_ukts`.
     - Validasi KRS mencari ID 12 di `financial_clearances` & `krs`.
     - Parent Portal mencari UKT untuk ID 12 di tabel `mahasiswas`, menampilkan data milik orang lain!
  2. Pada tabel `users`, ditemukan **20 user mahasiswa (User #34 s/d #53)** di mana `id_siswa = NULL` dan `id_mahasiswa = NULL`. Pengguna-pengguna ini langsung memicu kerentanan impersonasi Siswa #1 saat mengakses portal.
- **Rekomendasi Perbaikan:**
  - Tetapkan entitas kanonikal untuk SIAKAD Enterprise: jadikan `mahasiswas` dan `dosens` sebagai entitas primer untuk semua modul perguruan tinggi.
  - Sediakan migrasi penyelarasan data (*bridge migration*) yang menjamin setiap akun mahasiswa terisi pasangan `id_siswa` dan `id_mahasiswa` yang konsisten (atau satukan menjadi satu entitas terpadu).

---

### 2.3 Hilangnya Batasan Unik di Level Database pada Entitas Pembayaran & Presensi
- **Lokasi Migrasi:**
  - `database/migrations/2026_01_04_000004_create_pembayarans_table.php` (`pembayarans`)
  - `database/migrations/2026_10_05_000000_create_presensi_mahasiswa_and_enhanced_lms_tables.php` (`presensi_mahasiswas`)
- **Bukti Faktual Skema:**
  1. Pada tabel `pembayarans`: **TIDAK ADA indeks unik sama sekali**. Pengecekan pembayaran ganda hanya dilakukan di level PHP (`Pembayaran::where(...)`). Jika dua kasir mencatat pembayaran secara bersamaan, atau jika tombol form ditekan dua kali secara cepat (*rapid double-click*), dua baris pembayaran untuk bulan dan tahun yang sama akan berhasil tersimpan.
  2. Pada tabel `presensi_mahasiswas`: **TIDAK ADA indeks unik** pada pasangan `(id_bap, id_mahasiswa)`. Sedangkan pada tabel lama `presensi_kuliahs`, terdapat `UNIQUE (id_bap, id_siswa)`. Akibatnya, pada sistem QR/GPS presensi baru, mahasiswa dapat mengirimkan presensi berulang kali untuk pertemuan kuliah yang sama.
- **Rekomendasi Perbaikan:**
  Tambahkan migrasi penambahan *compound unique constraint*:
  ```php
  // Tabel pembayarans
  $table->unique(['id_siswa', 'bulan_dibayar', 'tahun_dibayar'], 'unique_pembayaran_siswa_periode');

  // Tabel presensi_mahasiswas
  $table->unique(['id_bap', 'id_mahasiswa'], 'unique_presensi_bap_mahasiswa');
  ```

---

## 3. Temuan Tingkat Tinggi Konkurensi & Transaksi (P1 — High)

### 3.1 Operasi Multi-Tabel Finansial Berjalan Tanpa `DB::transaction`
- **Lokasi Kode:**
  - [`app/Http/Controllers/Web/PembayaranController.php#L83-L145`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/PembayaranController.php#L83-L145)
  - [`app/Http/Controllers/Api/PembayaranController.php#L128-L160`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Api/PembayaranController.php#L128-L160)
- **Bukti Analisis:**
  Saat kasir menyimpan transaksi pembayaran SPP:
  1. Aplikasi membuat rekaman `Pembayaran::create()` di tabel `pembayarans`.
  2. Memeriksa sisa tunggakan, lalu memperbarui `FinancialClearance::updateOrCreate()`.
  3. Memperbarui `TagihanUkt::where(...)->update(['status_pembayaran' => 'Lunas'])`.
  4. Mencoba menyinkronkan data ke `PembayaranUkt::create()`.
  5. Mencatat log aktivitas audit.
  
  **Seluruh rangkaian di atas dieksekusi secara berurutan TANPA dibungkus blok `DB::transaction(function() { ... })`!**
- **Dampak Kritis:**
  Bila terjadi galat (misal koneksi terputus saat update `TagihanUkt` atau kegagalan insert `PembayaranUkt`), baris pembayaran kasir di `pembayarans` telah ter-*commit*, sementara tagihan UKT dan status KRS mahasiswa tetap terkunci (*partial write / state corruption*).
- **Rekomendasi Perbaikan:**
  Bungkus seluruh mutasi data kasir ke dalam satu blok transaksi atomik:
  ```php
  DB::transaction(function () use ($validated, $siswa, ...) {
      // 1. Insert Pembayaran
      // 2. Update FinancialClearance
      // 3. Update TagihanUkt
      // 4. Insert PembayaranUkt
  });
  ```

---

### 3.2 Kebocoran Kuota Kelas Saat Dosen Menolak KRS (`DosenKrsApprovalController`)
- **Lokasi Kode:** [`app/Http/Controllers/Web/DosenKrsApprovalController.php#L51-L56`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/DosenKrsApprovalController.php#L51-L56)
- **Bukti Analisis:**
  - Saat mahasiswa mengambil mata kuliah, `SmartKrsService::enrollClassWithPessimisticLock` menaikkan counter: `$kelas->increment('total_terisi')`.
  - Jika dosen wali menolak KRS mahasiswa melalui `DosenKrsApprovalController::reject`, controller hanya mengubah `status_krs = 'Ditolak'` tanpa mengurangi kuota:
    ```php
    $krs = Krs::findOrFail($id);
    $krs->update([
        'status_krs' => 'Ditolak',
        'catatan_pembimbing' => $request->input('catatan_pembimbing'),
    ]);
    ```
  - `total_terisi` pada `kelas_kuliahs` **tidak didekremen**!
- **Dampak:**
  Mahasiswa lain tidak dapat mengambil kursi kelas tersebut karena kuota terkunci palsu (*capacity leak*). Jika mahasiswa mengajukan ulang KRS, kuota akan terinkremen dua kali untuk orang yang sama.
- **Rekomendasi Perbaikan:**
  Pada saat penolakan atau pembatalan KRS, lakukan dekremen kuota di dalam transaksi terkunci:
  ```php
  foreach ($krs->details as $detail) {
      $detail->kelasKuliah()->lockForUpdate()->decrement('total_terisi');
  }
  ```

---

### 3.3 Utang Indeks Database: 64 Foreign Key Berjalan Tanpa Indeks
- **Bukti Faktual Skema:**
  Pemeriksaan `PRAGMA foreign_key_list` terhadap seluruh indeks yang ada membuktikan bahwa **64 kolom foreign key tidak memiliki indeks pendukung**.
  
  **Daftar Foreign Key Tanpa Indeks Paling Kritis (High Read Traffic):**
  1. `users.id_siswa`, `users.id_mahasiswa`, `users.id_guru`, `users.id_dosen` (Diperiksa pada setiap request authenticated!)
  2. `pembayarans.id_siswa`, `pembayarans.id_petugas`, `pembayarans.id_spp`
  3. `krs.id_dosen_wali`
  4. `jadwal_kuliahs.id_kelas_kuliah`, `jadwal_kuliahs.id_ruangan`, `jadwal_kuliahs.id_guru`
  5. `presensi_mahasiswas.id_bap`
  6. `transaksi_h2hs.id_tagihan_va`
  7. `bap_perkuliahans.id_ruangan`, `bap_perkuliahans.id_guru`
  8. `activity_logs.user_id`
  9. `tagihan_vas.id_bank_mitra`, `tagihan_ukts.id_bank_mitra`
- **Dampak Performa:**
  - Setiap query relasi `User::with('mahasiswa')` atau `Pembayaran::where('id_siswa', $id)` mengeksekusi *full table scan*.
  - Operasi penghapusan dengan `ON DELETE CASCADE` atau `ON DELETE SET NULL` terpaksa memindai seluruh baris tabel anak tanpa bantuan indeks B-Tree.
- **Rekomendasi Perbaikan:**
  Buat berkas migrasi `add_foreign_keys_performance_indexes.php` untuk mengindeks seluruh 64 kolom tersebut.

---

### 3.4 Keterbatasan Driver SQLite di Bawah Beban Puncak Konkurensi
- **Lokasi Konfigurasi:** [`config/database.php#L34-L44`](file:///home/masgansss/Projects/Dev/web_spp/config/database.php#L34-L44)
- **Bukti Konfigurasi:**
  ```php
  'sqlite' => [
      'driver' => 'sqlite',
      'database' => env('DB_DATABASE', database_path('database.sqlite')),
      'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
      'busy_timeout' => null,
      'journal_mode' => null,
      'synchronous' => null,
      'transaction_mode' => 'DEFERRED',
  ],
  ```
- **Analisis Risiko:**
  1. `busy_timeout => null`: Jika dua request web mencoba menulis ke database pada milidetik yang sama, SQLite tidak akan menunggu dan langsung melempar exception: `SQLSTATE[HY000]: General error: 5 database is locked`.
  2. `transaction_mode => 'DEFERRED'`: Kunci eksklusif baru diminta saat operasi tulis pertama, meningkatkan kemungkinan *deadlock* antar-koneksi konkuren.
  3. `lockForUpdate()` di `SmartKrsService`: Pada SQLite, klausul `SELECT ... FOR UPDATE` diabaikan (*no-op*), sehingga isolasi pesimistik hanya aktif efektif jika berjalan di MySQL/PostgreSQL.
- **Rekomendasi Perbaikan:**
  - Untuk lingkungan pengujian SQLite: Setel `'busy_timeout' => 5000` (5 detik) dan aktifkan `'journal_mode' => 'WAL'` (*Write-Ahead Logging*).
  - Untuk lingkungan produksi: Wajibkan migrasi ke MySQL 8.0+ atau PostgreSQL 16+ agar *row-level locking* (`lockForUpdate`) berfungsi secara riil.

---

## 4. Evaluasi Tipe Data & Presisi Desimal Finansial

| Kolom & Tabel | Tipe Data Saat Ini | Kesesuaian Standar | Analisis Presisi & Catatan |
| :--- | :---: | :---: | :--- |
| `tagihan_ukts.total_tagihan` | `decimal(12, 2)` | **Sesuai** | Mampu menampung hingga Rp 9.999.999.999,99 |
| `transaksi_h2hs.jumlah_dibayar` | `decimal(12, 2)` | **Sesuai** | Presisi standar perbankan |
| `reconciliation_logs.total_amount_bank` | `decimal(15, 2)` | **Sesuai** | Mampu menampung akumulasi kas hingga Rp 9 triliun |
| `pembayarans.jumlah_bayar` | `INTEGER` | **Kurang Tepat** | Sistem warisan SPP menggunakan integer rupiah; perlu diselaraskan ke decimal |
| `spps.nominal` | `INTEGER` | **Kurang Tepat** | Sistem warisan SPP menggunakan integer rupiah |
| `krs_details.bobot_mutu` | `decimal(3, 2)` | **Sesuai** | Skala IPK 0.00 s/d 4.00 |
| `krs_details.nilai_akhir_angka` | `decimal(5, 2)` | **Sesuai** | Skala nilai angka 0.00 s/d 100.00 |
| `presensi_mahasiswas.latitude` | `decimal(10, 7)` | **Sesuai** | Presisi koordinat GPS hingga ~1.1 cm |

---

## 5. Rencana Aksi Pemulihan Integritas Database

```
+----+------------------------------------------------------+----------+-------------+
| No | Tindakan Rekayasa Database                            | Prioritas| File Target |
+----+------------------------------------------------------+----------+-------------+
| 1  | Ubah ON DELETE CASCADE id_petugas & id_siswa ke RESTRICT| P0       | Migrasi Baru|
| 2  | Tambah Unique Constraint pada pembayarans & presensi | P0       | Migrasi Baru|
| 3  | Bungkus Web & Api PembayaranController ke DB::trans.  | P0       | Controller  |
| 4  | Perbaiki kebocoran kuota kelas di DosenKrsApproval   | P1       | Controller  |
| 5  | Buat indeks pendukung untuk 64 Foreign Keys tak berindeks| P1       | Migrasi Baru|
| 6  | Konfigurasi SQLite WAL & busy_timeout = 5000         | P2       | config/db   |
| 7  | Sinkronisasi pasangan id_siswa & id_mahasiswa akun user | P2       | Seeder/Patch|
+----+------------------------------------------------------+----------+-------------+
```
