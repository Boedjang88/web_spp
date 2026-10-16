# AUDIT UI/UX, FRONTEND & AKSESIBILITAS (PHASE 5)
**Sistem Informasi Akademik & Keuangan Terpadu (SIAKAD Enterprise & SPP)**  
*Dokumen Evaluasi Desain Antarmuka, Interaksi Pengguna, Arsitektur Informasi & Aksesibilitas*

---

## 1. Ringkasan Eksekutif UI/UX

Audit UI/UX (Phase 5) mengevaluasi pengalaman pengguna (*user experience*), konsistensi desain visual, arsitektur informasi (*information architecture*), dan aksesibilitas (*accessibility / a11y*) pada seluruh antarmuka web, mencakup template **Blade Monolitik**, **Filament Admin Panel (`/admin`)**, dan **Filament Parent Panel (`/wali`)**.

Sistem saat ini memiliki tampilan modern bernuansa *Enterprise Navy Blue*, namun mengidap sejumlah penyakit desain struktural:
1. **Asimetri Menu Navigasi:** Menu pada *Desktop Sidebar* dan *Mobile Drawer* tidak sinkron (fitur penting seperti DBPTA Skripsi dan Wisuda lenyap pada tampilan mobile, sementara fitur LMS dan EDOM tidak tercantum di menu desktop).
2. **Ketergantungan Tailwind Play CDN di Produksi:** Layout utama memuat Tailwind via CDN runtime (`cdn.tailwindcss.com`) alih-alih file CSS terkompilasi (*pre-compiled build*), memicu penalti performa, render-blocking, dan kegagalan offline PWA.
3. **Spamming Notifikasi Ganda Tiga (Alert Fatigue):** Saat terjadi kegagalan validasi, pengguna dihujani sekaligus oleh **tiga elemen antarmuka**: banner merah inline, toast melayang di pojok kanan, dan modal popup yang memblokir layar.
4. **Konflik Skema Gelap (Dark Mode CSS Inconsistency):** Penggunaan selektor `!important` kasar di `app.blade.php` menimpa utilitas kelas Tailwind gelap, merusak rasio kontras pada form dan border.
5. **Hambatan Aksesibilitas (WCAG 2.1 AA):** Label form filter tanpa atribut `for`/`id`, tombol berbasis ikon tanpa `aria-label`, serta rasio kontras teks slate pada latar gelap yang berada di bawah ambang batas minimum 4.5:1.

---

## 2. Arsitektur Informasi & Navigasi Antarmuka

### 2.1 Asimetri Kritis Menu Desktop vs Mobile
- **Lokasi Kode:** [`resources/views/layouts/app.blade.php#L406-L444`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php#L406-L444) (Mobile) vs [`#L627-L700`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php#L627-L700) (Desktop)
- **Bukti Faktual Divergensi:**
  Daftar menu navigasi mahasiswa ditulis terpisah secara hardcoded antara drawer mobile dan sidebar desktop, menyebabkan ketidaksinkronan parah:

  ```
  +--------------------------------+-----------------------+-----------------------+
  | Fitur / Modul Mahasiswa        | Desktop Sidebar       | Mobile Drawer Menu    |
  +--------------------------------+-----------------------+-----------------------+
  | Smart KRS                      | ADA                   | ADA                   |
  | Presensi Perkuliahan           | ADA                   | ADA                   |
  | Pembayaran UKT                 | ADA                   | ADA                   |
  | Kelengkapan Biodata            | ADA                   | ADA                   |
  | e-Surat Akademik               | ADA                   | ADA                   |
  | DBPTA (Proposal Skripsi)       | ADA                   | HILANG (Tidak Ada)    |
  | DBPTA (Bimbingan Skripsi)      | ADA                   | HILANG (Tidak Ada)    |
  | DBPTA (Sidang Yudisium)        | ADA                   | HILANG (Tidak Ada)    |
  | DBPTA (Pemberkasan Ijazah)     | ADA                   | HILANG (Tidak Ada)    |
  | Pendaftaran KKN                | ADA                   | HILANG (Tidak Ada)    |
  | Pendaftaran Wisuda             | ADA                   | HILANG (Tidak Ada)    |
  | Tugas & LMS                    | HILANG (Tidak Ada)    | ADA                   |
  | Grafik Radar OBE               | HILANG (Tidak Ada)    | ADA                   |
  | Evaluasi Dosen (EDOM)          | HILANG (Tidak Ada)    | ADA                   |
  | Pinjam Fasilitas Kampus        | HILANG (Tidak Ada)    | ADA                   |
  +--------------------------------+-----------------------+-----------------------+
  ```
- **Dampak Pengguna:**
  - Mahasiswa yang membuka portal dari ponsel pintar **tidak dapat mengakses menu Skripsi, KKN, dan Wisuda** dari navigasi utama.
  - Mahasiswa yang membuka portal dari laptop/desktop **tidak menemukan link tugas LMS, kuesioner EDOM, atau grafik radar OBE**.
- **Rekomendasi Perbaikan:**
  Satukan sumber data menu navigasi ke dalam *View Composer* atau komponen Blade tunggal (`<x-nav-menu :role="$role" />`) yang dirender secara konsisten baik di desktop maupun mobile.

---

### 2.2 Branding & Penamaan Tidak Konsisten
- Header Desktop: `"SIAP 5.6 Cloud"` ([`app.blade.php#L472`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php#L472))
- Header Mobile: `"SIAKAD ENTERPRISE"` ([`app.blade.php#L275`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php#L275))
- Judul Halaman: `"SIAKAD Enterprise - Sistem Informasi Akademik & Keuangan"` ([`app.blade.php#L6`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php#L6))
- Filament Admin: `"SIAKAD & SPP Enterprise"` ([`AdminPanelProvider.php#L30`](file:///home/masgansss/Projects/Dev/web_spp/app/Providers/Filament/AdminPanelProvider.php#L30))
- Filament Parent: `"Portal Wali Mahasiswa"` ([`ParentPanelProvider.php#L29`](file:///home/masgansss/Projects/Dev/web_spp/app/Providers/Filament/ParentPanelProvider.php#L29))

Pengguna menjumpai 4 variasi nama merek sistem yang berbeda, mengurangi kesan koherensi enterprise profesional.

---

## 3. Desain Interaksi & Pengalaman Pengguna (Interaction UX)

### 3.1 Spamming Notifikasi Ganda Tiga (Alert Fatigue)
- **Lokasi Kode:** [`resources/views/layouts/app.blade.php#L761-L788`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php#L761-L788) & [`#L1143-L1161`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php#L1143-L1161)
- **Bukti Interaksi:**
  Ketika sebuah aksi server gagal atau mengembalikan galat validasi form:
  1. Baris 769 merender **Flash Alert Banner Merah** tepat di atas konten halaman.
  2. Baris 1148 mengeksekusi JavaScript `showToast('error', ...)` yang memunculkan **Toast Melayang** di pojok kanan atas.
  3. Baris 1149 & 1160 mengeksekusi `showAlertModal(...)` yang memunculkan **Modal Dialog Gelap** yang mengaburkan dan memblokir seluruh interaksi layar!
- **Dampak UX:**
  Pengguna merasa terganggu (*cognitive overload*) karena harus menutup modal dialog, menutup toast, dan membaca banner di saat yang bersamaan untuk satu pesan kesalahan yang sama.
- **Rekomendasi Perbaikan:**
  Standardisasi saluran umpan balik (*Feedback Channel Standard*):
  - **Inline Alert:** Khusus untuk ringkasan form inline saat terdapat kesalahan input.
  - **Toast:** Khusus untuk notifikasi sukses aksi asinkronus (misal: "Draft KRS berhasil disimpan").
  - **Modal Dialog:** HANYA untuk konfirmasi aksi destruktif (misal: "Batalkan mata kuliah ini? Kuota akan dilepaskan"). Jangan gunakan modal untuk menampilkan pesan error form rutin.

---

### 3.2 Risiko Tombol Terkunci Palsu pada Proteksi Double-Post
- **Lokasi Kode:** [`resources/views/layouts/app.blade.php#L1045-L1068`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php#L1045-L1068)
- **Bukti Analisis:**
  Listener form global menonaktifkan tombol submit dan mengganti teksnya menjadi spinner *"Memproses..."*:
  ```javascript
  document.addEventListener('submit', function(e) {
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
          submitBtn.disabled = true;
          // Timeout fallback 10 detik
          setTimeout(() => { submitBtn.disabled = false; ... }, 10000);
      }
  });
  ```
  Jika form memiliki validasi sisi klien (atau form disubmit via `fetch()` tanpa me-reload halaman), tombol akan **terkunci selama 10 detik penuh**, membuat pengguna mengira aplikasi mengalami *freeze/hang*.
- **Rekomendasi Perbaikan:**
  Gunakan penanganan status loading per-form atau per-komponen, kembalikan status aktif tombol segera jika terjadi galat validasi sisi klien.

---

## 4. Desain Visual, Skema Warna & Konflik CSS (Visual Design & Tokens)

### 4.1 Risiko Kritis Penggunaan Tailwind Play CDN di Produksi
- **Lokasi Kode:** [`resources/views/layouts/app.blade.php#L29`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php#L29)
- **Bukti Faktual:**
  `<script src="https://cdn.tailwindcss.com"></script>`
- **Dampak Teknis:**
  1. Tailwind dikompilasi secara *just-in-time* oleh mesin JavaScript browser setiap kali halaman dimuat (*client-side compiler overhead*), memperlambat First Contentful Paint (FCP) hingga 300–800ms.
  2. Bergantung pada jaringan internet publik CDN; jika CDN lambat atau jaringan intranet universitas memblokir akses luar, tampilan antarmuka hancur total (*unstyled HTML*).
  3. Menggagalkan kapabilitas offline Service Worker PWA yang telah didaftarkan di baris 12.
- **Rekomendasi Perbaikan:**
  Kompilasi Tailwind CSS v4 secara statis menggunakan Vite (`npm run build` -> `resources/css/app.css` -> `@vite(['resources/css/app.css', 'resources/js/app.js'])`).

---

### 4.2 Konflik Dark Mode Overrides (`!important` Clashing)
- **Lokasi Kode:** [`resources/views/layouts/app.blade.php#L81-L100`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php#L81-L100)
- **Bukti Gaya:**
  ```css
  html.dark .bg-white {
      background-color: #1c2541 !important;
      color: #f8fafc !important;
  }
  html.dark .bg-slate-50, html.dark .bg-slate-100 {
      background-color: #0f172a !important;
      color: #cbd5e1 !important;
  }
  ```
- **Masalah:**
  Pemberian aturan kasar `!important` menimpa seluruh kelas utilitas warna turunan yang dibuat di level view anak. Misalnya jika view anak menetapkan `dark:bg-slate-900` atau `dark:bg-zinc-900`, CSS global memaksanya menjadi `#1c2541`, menciptakan tabrakan warna permukaan (*surface depth flattening*) dan membuat border card tampak hilang.
- **Rekomendasi Perbaikan:**
  Hapus override CSS kasar `!important`. Terapkan token warna semantik (`bg-surface-light dark:bg-surface-dark`) secara teratur melalui utility classes Tailwind.

---

### 4.3 Ketidakkonsistenan Komponen Antar-Modul

| Aspek Komponen | Template Akademik (`siakad/*`) | Template Finansial Kasir (`pembayaran/*`) | Filament Panels (`/admin`, `/wali`) |
| :--- | :--- | :--- | :--- |
| **Warna Focus Ring** | `focus:ring-blue-500` | `focus:ring-indigo-500` | `focus:ring-primary-500` |
| **Radius Card** | `rounded-2xl` (16px) | `rounded-xl` (12px) | `rounded-xl` (12px) |
| **Radius Button** | `rounded-xl` (12px) | `rounded-xl` (12px) | `rounded-lg` (8px) |
| **Gaya Tabel Header**| `bg-slate-100 dark:bg-slate-800` | `bg-slate-50` | `bg-gray-50 dark:bg-white/5` |
| **Tipografi Angka** | `font-mono font-bold` | `font-mono` reguler | Standar Sans |

Perbedaan komponen antar-modul memberikan kesan aplikasi dibangun oleh beberapa tim berbeda tanpa panduan desain terpadu.

---

## 5. Evaluasi Aksesibilitas (WCAG 2.1 AA Compliance)

| Kriteria WCAG | Status Audit | Lokasi & Temuan Faktual | Tindakan Remediasi |
| :--- | :---: | :--- | :--- |
| **1.3.1 Info and Relationships** | **GAGAL** | Filter dropdown di [`siakad/krs/index.blade.php#L39-L50`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/siakad/krs/index.blade.php#L39-L50) tidak memiliki atribut `id` dan tag `<label>` tidak memiliki atribut `for`. | Tambahkan atribut `id="semester_filter"` dan `for="semester_filter"`. |
| **1.4.3 Contrast (Minimum)** | **PARSIAL** | Teks kategori navigasi sidebar `text-slate-500` pada latar `bg-slate-950` memiliki rasio kontras 3.1:1 (di bawah batas 4.5:1). | Ubah menjadi `text-slate-400` atau `text-slate-300` (rasio > 5.5:1). |
| **2.1.1 Keyboard** | **LOLOS** | Seluruh tombol dan tautan dapat diakses via navigasi Tab keyboard; modal dapat ditutup dengan tombol `Escape`. | Pertahankan dan pastikan fokus terjebak di dalam modal saat terbuka (*focus trap*). |
| **2.4.4 Link Purpose (In Context)** | **PARSIAL** | Tombol aksi cetak dan hapus pada tabel presensi/nilai hanya menampilkan ikon SVG tanpa teks tersembunyi untuk screen reader. | Tambahkan atribut `aria-label="Cetak KHS Mahasiswa"` pada setiap tombol berbasis ikon. |
| **4.1.2 Name, Role, Value** | **PARSIAL** | Komponen flash alerts telah menggunakan `role="alert"` dan `aria-live="polite"`, namun toast asinkron belum terdaftar di live region. | Bungkus `toastContainer` dengan `role="region" aria-live="polite" aria-label="Notifikasi"`. |

---

## 6. Heuristik Usabilitas Nielsen Norman (10 Heuristics Evaluation)

1. **Visibilitas Status Sistem (Score: 7/10):**
   - *Kelebihan:* Progress bar atas saat navigasi halaman dan status badge (Draft, Disetujui, Lunas) sangat jelas.
   - *Kekurangan:* Tombol form memproses submit selama 10 detik tanpa indikator progres persentase.
2. **Kesesuaian Sistem dengan Dunia Nyata (Score: 8/10):**
   - Terminologi akademik (KRS, SKS, KHS, UKT, BAP, EDOM) menggunakan istilah resmi pendidikan tinggi Indonesia.
3. **Kendali & Kebebasan Pengguna (Score: 6/10):**
   - Mahasiswa tidak memiliki tombol *Undo* saat membatalkan mata kuliah di KRS; tombol pembatalan langsung menghapus permanen rekaman detail.
4. **Konsistensi & Standar (Score: 5/10):**
   - Terjadi inkonsistensi parah pada sidebar desktop vs mobile, serta variasi nama merek sistem (SIAP 5.6 vs SIAKAD Enterprise).
5. **Pencegahan Kesalahan (Score: 7/10):**
   - Mesin Smart KRS mencegah bentrok jadwal secara otomatis, namun konfirmasi dialog hanya mengandalkan JavaScript `confirm()` bawaan browser.
6. **Pengenalan daripada Mengingat (Score: 8/10):**
   - Meteran SKS di bagian atas KRS memudahkan mahasiswa melihat sisa kuota tanpa harus menghitung manual.
7. **Fleksibilitas & Efisiensi Penggunaan (Score: 6/10):**
   - Pengisian nilai dosen belum mendukung input cepat dengan navigasi tombol panah keyboard (*spreadsheet-like grid navigation*).
8. **Desain Estetik & Minimalis (Score: 6/10):**
   - Mengalami *alert fatigue* akibat notifikasi ganda tiga (inline + toast + modal).
9. **Membantu Pengguna Mengenali & Memulihkan Galat (Score: 7/10):**
   - Pesan galat berbahasa Indonesia komunikatif, namun modal error kerap menampilkan payload JSON mentah jika parsing gagal.
10. **Bantuan & Dokumentasi (Score: 6/10):**
    - Tersedia tautan API Console, namun tidak ada panduan tata cara pengisian KRS atau prosedur pengajuan skripsi bagi mahasiswa baru.
