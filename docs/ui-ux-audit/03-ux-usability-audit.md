# 03. UX & USABILITY AUDIT: PAGE-BY-PAGE FRICTION LOG
**SIAKAD Enterprise & SPP — Usability Scores, Friction Points, Defect Catalog & Acceptance Criteria**  
**Audit Phase:** Stage A — Analysis & Discovery  
**Execution Date:** 2026-10-13  
**Status:** COMPLETE (READ-ONLY ANALYSIS)

---

## 1. Usability Scoring Matrix

Each core surface is evaluated across the six standard dimensions (1 = critical problem, 5 = excellent):
- **US:** Usability (task completion ease, clarity of purpose)
- **IA:** Information Architecture (logical grouping, navigation clarity)
- **IQ:** Interaction Quality (feedback, validation, error recovery)
- **VH:** Visual Hierarchy (typography, density, contrast, alignment)
- **A11y:** Accessibility (WCAG 2.1/2.2 AA, keyboard, touch targets)
- **PP:** Perceived Performance (rendering speed, layout shifts, clutter)

| Surface / Route | Primary Role | US | IA | IQ | VH | A11y | PP | Mean Score | Key Problem Observed |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :--- |
| **Global App Shell** (`layouts/app.blade.php`) | All Roles | 2 | 2 | 3 | 3 | 3 | 2 | **2.5 / 5** | 1,268-line monolith; duplicated navigation markup; floating bottom dock overlaps content; massive inline `!important` style overrides. |
| **Login Screen** (`auth/login.blade.php`) | Guest | 4 | 4 | 4 | 4 | 4 | 4 | **4.0 / 5** | Good baseline; clear focus states; Google SSO button is distinct. |
| **Student Dashboard** (`dashboard/mahasiswa.blade.php`)| Mahasiswa | 3 | 3 | 3 | 3 | 3 | 3 | **3.0 / 5** | Too many summary cards; duplicate views with `dashboard.siswa`; schedule cards text is cramped on small screens. |
| **Smart KRS** (`siakad/krs/index.blade.php`) | Mahasiswa | 3 | 3 | 3 | 3 | 3 | 3 | **3.0 / 5** | Heavy desktop table on mobile; prerequisite warnings could be clearer prior to clicking enroll. |
| **Presence Check-In** (`siakad/presensi/index.blade.php`)| Mahasiswa | 3 | 4 | 3 | 3 | 2 | 3 | **3.0 / 5** | Geolocation errors on iOS Safari lack actionable guidance; camera scanner touch target is small. |
| **Dosen BAP Log** (`siakad/dosen/bap`) | Dosen | 3 | 3 | 3 | 3 | 3 | 3 | **3.0 / 5** | Meeting progression stepper consumes vertical space; modal dialog for student attendance on tablet feels cramped. |
| **Dosen KRS Approval** (`siakad/dosen/krs-approval`) | Dosen | 4 | 4 | 3 | 4 | 3 | 4 | **3.7 / 5** | Solid approval table; needs batch approval capability for large cohorts. |
| **Grade Entry Table** (`web/nilai`) | Dosen, Admin | 3 | 3 | 3 | 3 | 3 | 3 | **3.0 / 5** | Wide spreadsheet view forces horizontal dragging on mobile/tablet without row freeze. |
| **Cashier Desk** (`web/pembayaran/create`) | Petugas | 4 | 4 | 4 | 4 | 3 | 4 | **3.8 / 5** | Live calculation works well; lacks mobile-optimized receipt preview before printing. |
| **Public Invoice Lookup** (`/cek-tagihan`) | Public, Guest | 4 | 4 | 4 | 4 | 4 | 4 | **4.0 / 5** | Clean rate-limited lookup; masked PII output table is legible. |
| **Diploma Verification** (`/verifikasi-ijazah/{token}`)| Public | 4 | 4 | 4 | 4 | 4 | 4 | **4.0 / 5** | Clear SHA-256 badge and truthfulness status; print view is well optimized. |

---

## 2. Prioritized Usability Defects & Proposed Corrections

### Finding UX-01: Global Layout Navigation Duplication & Floating Dock Interference (P1)
- **Affected Route & File:** All authenticated routes using [`resources/views/layouts/app.blade.php`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php).
- **Observed Behavior:**
  - Navigation menus are declared twice in the same template: lines 360–533 (Mobile Drawer) and lines 580–804 (Desktop Sidebar).
  - On mobile screens, the floating bottom dock bar (`fixed bottom-3 left-3 right-3`) covers floating action buttons, form submission buttons, and table footers.
  - Page contents rely on a hardcoded global `pb-24` padding class to compensate.
- **User Impact:** Mobile users experience obscured buttons, jumpy scrolling, and awkward tap targets when keyboards appear.
- **Proposed Correction:**
  - Extract navigation into a single clean Blade component (`<x-nav-menu />`).
  - Redesign mobile navigation into a clean top app bar with an accessible slide-over drawer and eliminate the floating bottom dock that obscures form inputs, or convert it to a natural sticky footer on appropriate screens.
- **Acceptance Criteria:** Zero duplicate navigation HTML; no form submit buttons obscured by fixed navigation; clean viewport height across iOS and Android browsers.

---

### Finding UX-02: Student Sidebar Cognitive Overload — 17 Flat Menu Items (P1)
- **Affected Route & File:** [`resources/views/layouts/app.blade.php`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php) (Student Menu lines 458–530).
- **Observed Behavior:**
  - A student is presented with 17 ungrouped menu items: Bio Data, Pembayaran UKT, Smart KRS, Presensi Mahasiswa, Nilai Semester, Tugas & LMS, KKN, Wisudawan, e-Surat Akademik, Pinjam Fasilitas Kampus, Grafik Performa, Evaluasi Dosen (EDOM), DBPTA Proposal, Bimbingan, Sidang Yudisium, Pemberkasan, Ubah Password Akun.
- **User Impact:** High cognitive load and search friction. Essential daily tasks (today's schedule and attendance check-in) are buried amidst once-in-a-degree tasks (Wisuda, KKN, Sidang).
- **Proposed Correction:**
  - Group student navigation into 4 logical task clusters with collapsible sections:
    1. **Utama (Daily):** Dashboard, Jadwal, Presensi Kuliah, Tugas LMS.
    2. **Akademik (Semester):** Smart KRS, Nilai & KHS, Evaluasi Dosen (EDOM).
    3. **Layanan & Keuangan:** Tagihan & UKT, e-Surat, Pinjam Fasilitas.
    4. **Tingkat Akhir (Senior):** KKN, DBPTA / Skripsi, Wisuda.
- **Acceptance Criteria:** Student navigation is scannable in < 3 seconds; frequent tasks accessible in 1 tap.

---

### Finding UX-03: Duplicate Blade Dashboard and Master Views (P2)
- **Affected Files:**
  - `resources/views/dashboard/mahasiswa.blade.php` vs `dashboard/siswa.blade.php` (exact duplicate files).
  - `resources/views/dashboard/dosen.blade.php` vs `dashboard/guru.blade.php` (exact duplicate files).
  - `resources/views/mahasiswa/*` vs `resources/views/siswa/*` (duplicate forms).
  - `resources/views/dosen/*` vs `resources/views/guru/*` (duplicate forms).
- **Observed Behavior:** Dual-domain legacy support resulted in identical copy-pasted templates.
- **User Impact:** Maintenance risk: bug fixes or UI updates applied to `mahasiswa.blade.php` are missing on `siswa.blade.php`.
- **Proposed Correction:**
  - Consolidate dashboard templates into unified, polymorphic views using the existing [`ResolvesStudentUser`](file:///home/masgansss/Projects/Dev/web_spp/app/Traits/ResolvesStudentUser.php) trait.
- **Acceptance Criteria:** Single source of truth for each dashboard; zero duplicate view logic.

---

### Finding UX-04: Dense Table Usability Failure on Mobile Devices (P2)
- **Affected Views:** `siakad/krs/index.blade.php`, `web/matakuliah/index.blade.php`, `web/nilai/index.blade.php`.
- **Observed Behavior:**
  - Tables maintain rigid min-widths (e.g. `min-w-[800px]`), resulting in uncontrolled horizontal dragging on mobile phones without visual cues that more columns exist.
- **User Impact:** Mobile users miss action buttons (e.g. "Ambil Kelas" or "Detail") located on the far right of the table.
- **Proposed Correction:**
  - Implement responsive table strategies:
    - On mobile (< 768px): Render prioritized data as responsive cards with clear primary CTAs and expandable detail drawers.
    - On desktop (>= 768px): Maintain dense tabular presentation.
- **Acceptance Criteria:** All primary actions visible within standard 375px mobile viewport without horizontal scrolling.

---

### Finding UX-05: Inconsistent Branding & Layout Title Stamps (P3)
- **Affected Views:** `layouts/app.blade.php`, `resources/views/dashboard/index.blade.php`.
- **Observed Behavior:** Sidebar brand header states "SIAP 5.6 Cloud", page title states "SIAKAD Enterprise", and footer states "SIAKAD Enterprise • Laravel 11.x".
- **User Impact:** Confusion regarding the software identity and platform version.
- **Proposed Correction:** Standardize institutional branding to "SIAKAD Enterprise" with configurable university name token.
- **Acceptance Criteria:** 100% consistent platform naming across title tags, sidebars, headers, and footers.
