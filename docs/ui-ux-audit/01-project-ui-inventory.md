# 01. PROJECT UI INVENTORY: SIAKAD ENTERPRISE
**SIAKAD Enterprise & SPP — Comprehensive UI Surface, Layout & Route Catalog**  
**Audit Phase:** Stage A — Analysis & Discovery  
**Execution Date:** 2026-10-13  
**Status:** COMPLETE (READ-ONLY ANALYSIS)

---

## 1. Executive Summary & Stack Overview

The **SIAKAD Enterprise & SPP** application presents a hybrid user interface architecture consisting of:
1. **Core Web Portals (Custom Blade Monolith):**
   - Framework: Laravel 11.x Blade templates.
   - Styling: Tailwind CSS v3 compiled via Vite v7.3.0 (`public/build/assets/`).
   - JavaScript Micro-interactions: Alpine.js (currently loaded via unpinned jsDelivr CDN script in layout, with custom inline vanilla JS helpers).
   - Fonts: Google Fonts `Plus Jakarta Sans` (UI body/headings) and `JetBrains Mono` (tabular data & codes).
2. **Administrative & Guardian Portals (Filament v3.2):**
   - Superadmin / System Admin: `/admin` (Filament panel using Livewire 3).
   - Parent / Guardian Portal: `/wali` (Filament panel for student attendance and tuition tracking).
3. **Public Self-Service Surfaces:**
   - Public Landing (`/` redirecting to `/login`).
   - Prospective Student Registration (`/pmb/register`).
   - Public Tuition Lookup (`/cek-tagihan`).
   - Digital Diploma Verification (`/verifikasi-ijazah/{token}`).
   - Employer Feedback Portal (`/survey/employer/{token}`).
   - Interactive API Console (`/api/docs`).

---

## 2. Global Application Shell Inventory

### 2.1 The Master Shell: `resources/views/layouts/app.blade.php`
- **File Metrics:** 1,268 lines, 111.7 KB.
- **Header:**
  - Desktop: Top sticky bar with institutional title, date stamp, theme toggle (Light/Dark), and optional API Console button.
  - Mobile: Minimal bar with hamburger menu toggle button and brand icon.
- **Sidebars & Navigation:**
  - **Desktop Sidebar:** Sticky left sidebar (`w-64`, `bg-slate-950`). Hardcodes 3 distinct role-based menu structures (`admin`, `dosen`, `mahasiswa`).
  - **Mobile Drawer:** Overlay drawer toggled by vanilla JS `toggleMobileMenu()`. Contains an exact duplicate of the desktop navigation markup (170+ duplicate lines of HTML and SVG icons).
  - **Mobile Bottom App Dock:** Floating dock bar (`fixed bottom-3 left-3 right-3 z-40 bg-slate-950/90 rounded-2xl`) containing 5 quick-action icons (Home, Action 1, Action 2, Action 3, Drawer toggle). Forces artificial padding `pb-24` across all page contents.
- **Theme Controls:**
  - Inline head script checking `localStorage.getItem('theme')` or `prefers-color-scheme`.
  - Massive block of inline CSS (lines 68–340+) using `!important` overrides to force dark navy backgrounds (`#0b132b`, `#1c2541`) onto standard Tailwind classes (`.bg-white`, `.bg-slate-50`, `.text-slate-900`).
- **Footer:** Simple copyright banner (`bg-white`, `border-t`).

---

## 3. Complete View & Module Inventory

| Module / Domain | Directory Path | Templates Found | Roles Allowed | Primary UI Purpose |
| :--- | :--- | :--- | :--- | :--- |
| **Authentication** | `resources/views/auth/` | `login.blade.php`, `pdp-consent.blade.php` | Guest, Auth | Login screen, Google SSO, UU PDP consent modal. |
| **Public Services** | Root views | `welcome.blade.php`, `cek-tagihan.blade.php`, `kwitansi.blade.php` | Guest, Public | Portal landing, invoice lookup, raw receipt print. |
| **Dashboards** | `resources/views/dashboard/` | `index.blade.php`, `mahasiswa.blade.php`, `siswa.blade.php`, `dosen.blade.php`, `guru.blade.php` | Admin, Dosen, Mahasiswa | Role-tailored operational overviews. Note: `siswa`/`guru` views duplicate `mahasiswa`/`dosen`. |
| **Student (Mahasiswa)** | `resources/views/mahasiswa/` & `siswa/` | `index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`, `kartu-ujian.blade.php`, `surat-tagihan.blade.php` | Admin, BAAK, Mahasiswa | Student master records, exam card printing, billing notice printing. |
| **Lecturer (Dosen)** | `resources/views/dosen/` & `guru/` | `index.blade.php`, `create.blade.php`, `edit.blade.php` | Admin, BAAK | Faculty member master records and teaching profiles. |
| **Courses & Classes** | `resources/views/matakuliah/`, `mapel/`, `kelas/` | `index.blade.php`, `create.blade.php`, `edit.blade.php` | Admin, BAAK | Curriculum catalog, course prerequisites, classroom sections. |
| **Schedules & Timetable**| `resources/views/jadwal/` | `index.blade.php`, `create.blade.php`, `edit.blade.php` | Admin, Dosen, Mahasiswa | Weekly class timetable, room allocation, collision checks. |
| **Grading & Transcripts**| `resources/views/nilai/` | `index.blade.php`, `create.blade.php`, `edit.blade.php`, `khs.blade.php`, `rapor.blade.php` | Admin, Dosen, Mahasiswa | Component grading, KHS semester grade card, legacy report card. |
| **Tuition & Billing** | `resources/views/pembayaran/`, `spp/`, `ukt/` | `index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`, `kwitansi.blade.php` | Admin, Petugas, Mahasiswa | Cashier payment desk, UKT tariff matrix, transaction history. |
| **Smart KRS Portal** | `resources/views/siakad/krs/` | `index.blade.php` | Mahasiswa | Study planning, course registration, prerequisite validation. |
| **Attendance Portal**| `resources/views/siakad/presensi/`, `presensi/` | `index.blade.php` | Mahasiswa, Dosen | QR & GPS lecture attendance check-in, presence log. |
| **Tugas & LMS** | `resources/views/siakad/tugas/` | `index.blade.php`, `show.blade.php` | Mahasiswa, Dosen | Assignment submissions, grading feedback, deadlines. |
| **Thesis (DBPTA)** | `resources/views/siakad/dbpta/` | `proposal.blade.php`, `bimbingan.blade.php`, `sidang.blade.php`, `pemberkasan.blade.php` | Mahasiswa, Dosen, BAAK | Multi-stage final project lifecycle management. |
| **KKN & Wisuda** | `resources/views/siakad/kkn/`, `siakad/wisuda/` | `index.blade.php` | Mahasiswa, BAAK | Community service enrollment, graduation registration. |
| **E-Surat Akademik** | `resources/views/siakad/student/`, `siakad/baak/` | `esurat-index.blade.php`, `esurat.blade.php` | Mahasiswa, BAAK | Academic certificate requests and administrative approvals. |
| **Early Warning System**| `resources/views/siakad/baak/` | `ews.blade.php` | Admin, BAAK | Dropout risk radar, GPA tracking, attendance alerts. |
| **Executive AI Panel** | `resources/views/siakad/executive/` | `dashboard.blade.php` | Executive, Dekan, Rektor | Strategic university KPIs, admission vs revenue trends. |
| **User Administration** | `resources/views/users/`, `activity-logs/` | `index.blade.php`, `create.blade.php`, `edit.blade.php` | Admin, Superadmin | RBAC user management, forensic audit trail logs. |
| **Filament Admin** | `app/Filament/` & `resources/views/filament/` | Native Filament panels & custom widgets | Superadmin, Admin | Filament 3.2 backend panels. |
| **Filament Parent** | `app/Filament/Parent/` | Native Filament panel (`/wali`) | Wali Murid | Parent monitoring dashboard. |

---

## 4. Uninspected or Inactive Views
All 84 Blade templates in `resources/views/` and 2 Filament panel providers have been indexed and inspected directly against active route declarations in `routes/web.php`. Zero orphaned view directories exist.
