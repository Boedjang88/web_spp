# SISTEM DESAIN & PANDUAN ANTARMUKA ENTERPRISE (SIAKAD DS)
**Sistem Informasi Akademik & Keuangan Terpadu (SIAKAD Enterprise)**  
*Dokumen Standar Desain, Token Visual, Komponen & Pedoman Aksesibilitas Terpadu*

---

## 1. Filosofi Desain: Anti-Slop Enterprise Utility

SIAKAD Enterprise melayani sivitas akademika (Mahasiswa, Dosen, Tenaga Kependidikan, Pimpinan Universitas, dan Orang Tua/Wali). Antarmuka sistem memprioritaskan **kepadatan informasi yang efisien (*high visual density*)**, **kejelasan navigasi (*instant legibility*)**, dan **keandalan interaksi (*deterministic feedback*)** di atas dekorasi kosmetik yang tidak perlu.

### Prinsip Utama Desain

1. **Kejelasan di Atas Ornamen (Clarity Over Decoration):**
   - Tidak menggunakan gradasi warna pastel pudar atau elemen mengambang yang menghabiskan ruang layar.
   - Setiap elemen antarmuka harus memiliki fungsi utilitas yang jelas.
2. **Kesesuaian Tipografi Kontekstual (Contextual Typography):**
   - Teks naratif dan label antarmuka menggunakan **Plus Jakarta Sans** untuk kenyamanan membaca.
   - Seluruh data tabular, angka keuangan (Rupiah), kode mata kuliah, NIM/NIDN/NIP, nilai IPK/SKS, dan stempel waktu menggunakan **JetBrains Mono** agar karakter angka sejajar dan mudah dipindai secara vertikal (*tabular figures*).
3. **Umpan Balik Bertingkat yang Tertib (Disciplined Feedback Channels):**
   - Dilarang membombardir pengguna dengan peringatan ganda (banner + toast + modal sekaligus).
   - Modal popup hanya boleh digunakan untuk **konfirmasi aksi destruktif atau krusial**.
   - Notifikasi sukses menggunakan **Toast non-intrusif** (hilang otomatis dalam 4 detik).
   - Kesalahan input form menggunakan **Inline Error** tepat di bawah bidang input terkait.
4. **Kepatuhan Aksesibilitas WCAG 2.1 AA:**
   - Rasio kontras teks minimum 4.5:1 untuk teks normal dan 3.0:1 untuk teks besar/badge.
   - Seluruh elemen interaktif dapat dioperasikan penuh melalui keyboard.
   - Status tidak boleh disampaikan hanya melalui warna saja (*color is never the sole indicator*).

---

## 2. Token Desain (Design Tokens)

### 2.1 Palet Warna Semantik (Color Palette)

#### A. Brand & Primary (Deep Enterprise Blue)
Warna identitas utama universitas yang mencerminkan otoritas, ketertiban, dan keanggunan akademik.

| Token | Nilai Hex | Penggunaan Utama |
| :--- | :---: | :--- |
| `brand-50` | `#eff6ff` | Latar belakang badge aktif / hover state lembut |
| `brand-100` | `#dbeafe` | Border badge / container informasi sekunder |
| `brand-200` | `#bfdbfe` | Border interaktif fokus |
| `brand-500` | `#3b82f6` | Aksen tombol sekunder / indikator aktif |
| `brand-600` | `#2563eb` | **Tombol Utama (Primary Action)** / Header aktif |
| `brand-700` | `#1d4ed8` | Hover state tombol utama |
| `brand-900` | `#1e3a8a` | Header kartu data / teks penekanan tinggi |
| `brand-950` | `#0b132b` | **Latar belakang dark mode kanonikal & sidebar desktop** |

#### B. Neutrals & Surface (Slate Foundation)
Fondasi kontras latar belakang dan teks, menjamin konsistensi antara Blade dan Filament.

| Token | Light Mode | Dark Mode | Fungsi |
| :--- | :---: | :---: | :--- |
| `surface-base` | `#f8fafc` (`slate-50`) | `#0b132b` (Deep Navy) | Latar belakang kanvas aplikasi |
| `surface-card` | `#ffffff` (`white`) | `#1c2541` (Surface Navy) | Latar belakang kartu data / modal |
| `surface-muted` | `#f1f5f9` (`slate-100`) | `#0f172a` (`slate-900`) | Baris tabel zebra / input background |
| `border-subtle` | `#e2e8f0` (`slate-200`) | `#1e293b` (`slate-800`) | Garis pemisah / border kartu |
| `border-focus` | `#3b82f6` (`blue-500`) | `#60a5fa` (`blue-400`) | Cincin fokus keyboard (`focus:ring`) |
| `text-primary` | `#0f172a` (`slate-900`) | `#f8fafc` (`slate-50`) | Judul utama dan isi tabel |
| `text-secondary` | `#475569` (`slate-600`) | `#94a3b8` (`slate-400`) | Label form, deskripsi subtitle |
| `text-muted` | `#64748b` (`slate-500`) | `#64748b` (`slate-500`) | Timestamp, metadata sekunder |

#### C. Semantic Status (Feedback & Badges)

| Status | Teks & Ikon | Latar Badge (Light / Dark) | Border Badge | Penggunaan |
| :--- | :---: | :---: | :---: | :--- |
| **Success** | `#059669` (`emerald-600`) | `#ecfdf5` / `#064e3b` (30%) | `#a7f3d0` (`emerald-200`) | KRS Disetujui, Lunas, Hadir, Lulus |
| **Warning** | `#d97706` (`amber-600`) | `#fffbeb` / `#78350f` (30%) | `#fde68a` (`amber-200`) | Draft, Menunggu Verifikasi, Terlambat |
| **Danger** | `#dc2626` (`rose-600`) | `#fff1f2` / `#881337` (30%) | `#fecdd3` (`rose-200`) | Ditolak, Terkunci, Alpa, EWS Drop-Out |
| **Info** | `#0284c7` (`sky-600`) | `#f0f9ff` / `#0c4a6e` (30%) | `#bae6fd` (`sky-200`) | Pengumuman, Revisi Jadwal, Informasi |

---

### 2.2 Skala Tipografi (Typography Scale)

- **Font Keluarga Utama:** `'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif`
- **Font Tabular / Monospace:** `'JetBrains Mono', ui-monospace, monospace`

```
Display:    24px / 32px (1.5rem)   - font-extrabold - letter-spacing: -0.025em (Header Dashboard)
Heading 1:  20px / 28px (1.25rem)  - font-bold      - letter-spacing: -0.02em  (Judul Halaman Modul)
Heading 2:  16px / 24px (1.0rem)   - font-semibold  - letter-spacing: -0.01em  (Judul Kartu / Tabel)
Body:       13px / 20px (0.8125rem)- font-normal    - Teks isi konten utama & tabel
Small/Label:12px / 16px (0.75rem)  - font-medium    - Label input, tombol navigasi
Micro/Meta: 10px / 14px (0.625rem) - font-mono      - Badge status, timestamp, tag kuota
```

---

### 2.3 Skala Elevasi, Bayangan & Sudut Lengkung (Radii & Elevation)

- **Radius Sudut:**
  - `rounded-2xl` (`16px`): Kartu wadah utama (*Container Card*), banner header, modal dialog.
  - `rounded-xl` (`12px`): Sub-panel, grup tombol, drawer menu mobile.
  - `rounded-lg` (`8px`): Tombol aksi (*Buttons*), input field, dropdown select.
  - `rounded-full` (`9999px`): Badge status, avatar profil, meteran progress bar.
- **Elevasi Bayangan:**
  - `shadow-soft`: `0 1px 3px 0 rgba(15, 23, 42, 0.05)` (Default kartu data).
  - `shadow-floating`: `0 10px 25px -5px rgba(15, 23, 42, 0.15)` (Modal dialog & mobile dock).
  - `shadow-toast`: `0 4px 14px rgba(15, 23, 42, 0.12)` (Floating toast).

---

## 3. Standar Komponen Antarmuka (Component Specifications)

### 3.1 Tombol Aksi (Buttons Hierarchy)

```html
<!-- 1. Primary Button (Aksi Utama / Simpan / Setujui) -->
<button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 active:scale-[0.98] text-white text-xs font-semibold rounded-lg shadow-sm transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span>Simpan Perubahan</span>
</button>

<!-- 2. Secondary / Outline Button (Aksi Alternatif / Batal / Filter) -->
<button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-semibold rounded-lg transition">
    <span>Batal</span>
</button>

<!-- 3. Destructive Button (Aksi Bahaya / Hapus / Tolak) -->
<button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800 text-xs font-semibold rounded-lg transition">
    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
    <span>Hapus Data</span>
</button>
```

### 3.2 Form Input & Validasi Aksesibel

Setiap bidang input **wajib** memiliki pasangan `<label for="id">` dan pesan galat yang ditautkan melalui atribut `aria-describedby`:

```html
<div class="space-y-1">
    <label for="nama_lengkap" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
        Nama Lengkap Mahasiswa <span class="text-rose-500">*</span>
    </label>
    <input type="text" id="nama_lengkap" name="nama" value="{{ old('nama', $mahasiswa->nama ?? '') }}"
        required aria-required="true"
        aria-describedby="@error('nama') nama-error @else nama-help @enderror"
        class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border @error('nama') border-rose-500 focus:ring-rose-500 @else border-slate-200 dark:border-slate-700 focus:ring-brand-500 @enderror rounded-lg text-xs focus:outline-none focus:ring-2 transition">
    
    @error('nama')
        <p id="nama-error" class="text-[11px] text-rose-600 dark:text-rose-400 font-medium flex items-center gap-1 mt-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <span>{{ $message }}</span>
        </p>
    @else
        <p id="nama-help" class="text-[10px] text-slate-500 dark:text-slate-400">Sesuai dengan ijazah pendidikan sebelumnya.</p>
    @enderror
</div>
```

### 3.3 Data Table Enterprise Standard

1. Header tabel wajib menggunakan tag `<th scope="col">`.
2. Seluruh kolom angka/keuangan rata kanan (`text-right`) dengan font `font-mono`.
3. Seluruh tabel wajib dibungkus kontainer responsif `<div class="overflow-x-auto">`.
4. Baris memiliki efek zebra halus (`odd:bg-white even:bg-slate-50/60 dark:odd:bg-slate-900 dark:even:bg-slate-900/50`).

```html
<div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800 shadow-soft">
    <table class="w-full text-left text-xs border-collapse">
        <thead class="bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 uppercase text-[10px] font-semibold tracking-wider border-b border-slate-200 dark:border-slate-700">
            <tr>
                <th scope="col" class="py-3 px-4">Mata Kuliah</th>
                <th scope="col" class="py-3 px-3 text-center">Kelas</th>
                <th scope="col" class="py-3 px-3 text-right">SKS</th>
                <th scope="col" class="py-3 px-3 text-center">Status</th>
                <th scope="col" class="py-3 px-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr class="hover:bg-brand-50/40 dark:hover:bg-slate-800/60 transition">
                <td class="py-3 px-4 font-semibold text-slate-900 dark:text-slate-100">
                    <div>Rekayasa Perangkat Lunak</div>
                    <span class="text-[10px] text-slate-400 font-mono">TIF2040</span>
                </td>
                <td class="py-3 px-3 text-center font-mono font-bold text-slate-700 dark:text-slate-300">IF-A</td>
                <td class="py-3 px-3 text-right font-mono font-bold text-slate-900 dark:text-slate-100">3</td>
                <td class="py-3 px-3 text-center">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Disetujui
                    </span>
                </td>
                <td class="py-3 px-4 text-right">
                    <button class="p-1.5 text-slate-400 hover:text-brand-600 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition" aria-label="Lihat Rincian Mata Kuliah">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

### 3.4 Badge Status Terpadu (Compound Status Badges)

Seluruh badge harus memadukan **titik indikator (dot indicator)** dan **label teks eksplisit** agar ramah bagi penderita buta warna (*accessible to color-blind users*):

```html
<!-- Lunas / Disetujui (Success) -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800">
    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
    Lunas Terverifikasi
</span>

<!-- Menunggu Persetujuan / Draft (Warning) -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800">
    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
    Menunggu Persetujuan PA
</span>

<!-- Ditolak / Terkunci (Danger) -->
<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800">
    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
    KRS Ditolak (Revisi)
</span>
```

---

## 4. Penyelarasan Navigasi Desktop & Mobile

Untuk mengatasi asimetri menu temuan Phase 5, seluruh modul mahasiswa wajib dikelompokkan ke dalam struktur terpadu:

```
[Kategori Utama]
  ├── Dashboard
  └── Notifikasi Pengumuman

[Akademik Perkuliahan]
  ├── Smart KRS (Rencana Studi)
  ├── Presensi Kuliah (QR & Lokasi)
  ├── Tugas Kuliah & LMS
  ├── Nilai Semester & KHS
  └── Evaluasi Dosen (EDOM)

[Tugas Akhir & Kelulusan]
  ├── DBPTA: Pengajuan Proposal
  ├── DBPTA: Bimbingan & Logbook
  ├── DBPTA: Sidang Yudisium
  ├── DBPTA: Pemberkasan Ijazah
  ├── Pendaftaran KKN
  └── Pendaftaran Wisuda

[Layanan & Finansial]
  ├── Tagihan & Pembayaran UKT
  ├── e-Surat Akademik
  └── Peminjaman Fasilitas Kampus

[Akun Pribadi]
  ├── Profil & Biodata Mahasiswa
  └── Keamanan & Ubah Password
```

Struktur ini wajib dirender **100% identik** baik pada *Desktop Sidebar* maupun *Mobile Drawer*, dengan 4 menu terfavorit disematkan pada *Mobile Bottom Dock* (Home, Presensi, UKT, KRS, Menu).

---

## 5. Rencana Migrasi Asset Frontend (Tailwind Build Pipeline)

1. **Hapus Tailwind Play CDN:** Cabut `<script src="https://cdn.tailwindcss.com"></script>` dari `layouts/app.blade.php`.
2. **Kompilasi CSS Terpadu:** Bangun bundle CSS menggunakan `@vite(['resources/css/app.css', 'resources/js/app.js'])`.
3. **Penyatuan Tema Filament:** Daftarkan token warna `brand-50` s/d `brand-950` ke dalam `AdminPanelProvider.php` dan `ParentPanelProvider.php` agar warna antarmuka Filament dan Blade identik.
4. **Pembersihan CSS Kasar:** Hapus aturan `!important` global dark mode pada `app.blade.php`, gantikan dengan semantic classes Tailwind.
