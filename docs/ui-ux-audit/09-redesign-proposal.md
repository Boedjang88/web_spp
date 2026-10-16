# 09. REDESIGN PROPOSAL: SCOPE, BENEFIT & IMPACT MATRIX
**SIAKAD Enterprise & SPP — Detailed Architectural Redesign Proposals**  
**Audit Phase:** Stage A — Analysis & Discovery  
**Execution Date:** 2026-10-13  
**Status:** PROPOSED FOR USER APPROVAL

---

## 1. Executive Summary

This proposal outlines seven structured, non-destructive UI/UX improvements designed to solve the verified defects documented in Audits 01–07.

All proposals adhere to the **Zero Destructive Operations** invariant: routes, controllers, authorization guards, validation rules, and backend data contracts remain 100% intact.

---

## 2. Proposals Specification Matrix

### Proposal 1: Master Shell & Mobile Navigation Overhaul
- **Current State:** 1,268-line `layouts/app.blade.php` file with duplicated navigation markup, floating dock overlapping form submit buttons, and 300+ lines of inline `!important` CSS rules.
- **Verified Problem:** Mobile users cannot tap submit buttons covered by the dock; maintenance requires double-editing navigation.
- **Proposed State:**
  - Decompose `layouts/app.blade.php` into modular Blade components: `<x-nav-menu />`, `<x-header />`, `<x-sidebar />`, `<x-mobile-drawer />`.
  - Replace floating dock with an accessible top app header and slide-over navigation drawer.
  - Remove artificial `pb-24` padding.
- **User Benefit:** Clear viewports on mobile; zero obscured submit buttons; clean keyboard interactions.
- **Roles Affected:** All roles (`admin`, `dosen`, `mahasiswa`, `petugas`).
- **Files Affected:** `resources/views/layouts/app.blade.php`, `resources/views/components/navigation/*`.
- **Functional Risk:** Low.
- **Effort / Priority:** M / **P1**.
- **Nature of Change:** Visual & Layout Structure (Zero backend mutation).

---

### Proposal 2: Grouped Role-Aware Student Navigation
- **Current State:** 17 flat menu items in an unorganized list for students.
- **Verified Problem:** High cognitive load; daily tasks (today's schedule, attendance check-in) are buried amongst rare tasks (graduation, thesis).
- **Proposed State:**
  - Group student navigation into 4 logical clusters:
    1. **Utama:** Dashboard, Jadwal Kuliah, Presensi.
    2. **Akademik:** Smart KRS, Nilai Semester, Evaluasi Dosen.
    3. **Layanan & Finansial:** Tagihan UKT, e-Surat, Pinjam Fasilitas.
    4. **Tingkat Akhir:** KKN, DBPTA / Skripsi, Wisuda.
- **User Benefit:** Students find what they need in 1 tap; scannable in < 3 seconds.
- **Roles Affected:** Mahasiswa.
- **Files Affected:** Navigation component.
- **Functional Risk:** Zero (exact same routes retained).
- **Effort / Priority:** S / **P1**.
- **Nature of Change:** Information Architecture & UX.

---

### Proposal 3: Dashboard Unification & High-Impact Action Tiles
- **Current State:** Duplicate dashboard templates (`dashboard/mahasiswa` vs `dashboard/siswa`, `dashboard/dosen` vs `dashboard/guru`).
- **Verified Problem:** Unnecessary file duplication; admin dashboard displays 8 top metric cards repeated in tables below.
- **Proposed State:**
  - Consolidate to unified `dashboard/mahasiswa.blade.php` and `dashboard/dosen.blade.php` using polymorphic model properties.
  - On Student Dashboard: Add prominent "Kuliah Hari Ini & Presensi" top card when a class is currently active.
  - On Admin Dashboard: Focus on 4 primary KPI tiles (Active Students, Faculty, Monthly Tuition Revenue, EWS Alert Count).
- **User Benefit:** Single-tap attendance check-in for students; cleaner maintenance; focused executive oversight.
- **Roles Affected:** Mahasiswa, Dosen, Admin.
- **Files Affected:** `resources/views/dashboard/*`, `app/Http/Controllers/Web/DashboardController.php`.
- **Functional Risk:** Low (Preserves all existing variables).
- **Effort / Priority:** M / **P1**.
- **Nature of Change:** Usability & Redundancy Removal.

---

### Proposal 4: Responsive Card Pattern for Dense Mobile Tables
- **Current State:** Course enrollment (KRS), class schedules, and payment ledger tables use fixed min-width tables with awkward horizontal scroll on mobile. Action buttons ("Ambil Kelas") are pushed far off-screen.
- **Verified Problem:** Mobile students miss critical action buttons.
- **Proposed State:**
  - Implement adaptive layout: On mobile (< 768px), render records as clean cards with primary actions prominent. On desktop (>= 768px), render high-density data tables.
- **User Benefit:** Mobile students can enroll in courses with 1 tap without horizontal swiping.
- **Roles Affected:** Mahasiswa, Dosen, Admin.
- **Files Affected:** `resources/views/siakad/krs/index.blade.php`, `resources/views/jadwal/index.blade.php`.
- **Functional Risk:** Low.
- **Effort / Priority:** M / **P2**.
- **Nature of Change:** Mobile Ergonomics.

---

### Proposal 5: Form Control & Validation Standardization
- **Current State:** Input controls differ across views (some have light borders, some have dark borders, some 2-column grids break on 320px screens).
- **Verified Problem:** Cramped inputs on small mobile viewports; inconsistent error messages.
- **Proposed State:**
  - Deploy standard `<x-form-input />`, `<x-form-select />`, `<x-form-textarea />` components with 44px touch height, accessible labels, deterministic IDs, and inline error feedback.
  - Enforce single-column form grids on mobile (`grid-cols-1 md:grid-cols-2`).
- **User Benefit:** Comfortable form filling on phones; clear validation recovery.
- **Roles Affected:** All roles.
- **Files Affected:** Form views across `siswa`, `dosen`, `matakuliah`, `dbpta`.
- **Functional Risk:** Zero.
- **Effort / Priority:** M / **P2**.
- **Nature of Change:** Component Standardization & WCAG AA Compliance.

---

### Proposal 6: Native Tailwind Dark Mode (Eliminate `!important` CSS Hacks)
- **Current State:** Master layout injects 300+ lines of inline `<style>` overriding `.bg-white` and `.bg-slate-50` with `!important`.
- **Verified Problem:** Overrides component-specific styling; causes layout paint flashes.
- **Proposed State:**
  - Remove layout `!important` block.
  - Utilize standard Tailwind `dark:` variant classes across components (e.g. `bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-50`).
- **User Benefit:** Seamless, flicker-free dark mode rendering; lighter layout asset size.
- **Roles Affected:** All roles.
- **Files Affected:** `resources/views/layouts/app.blade.php`, `resources/css/app.css`.
- **Functional Risk:** Low.
- **Effort / Priority:** S / **P2**.
- **Nature of Change:** CSS Architecture & Performance.

---

### Proposal 7: Institutional Branding Consistency
- **Current State:** Platform references "SIAP 5.6 Cloud" in sidebar and "SIAKAD Enterprise" in title/footer.
- **Verified Problem:** Software identity mismatch.
- **Proposed State:** Unify institutional name to "SIAKAD Enterprise" across all headers, sidebars, page titles, and footers.
- **User Benefit:** Professional, cohesive branding.
- **Roles Affected:** All users.
- **Files Affected:** `resources/views/layouts/app.blade.php`.
- **Functional Risk:** Zero.
- **Effort / Priority:** XS / **P3**.
- **Nature of Change:** Visual Consistency.

---

## 3. Scope Boundaries & What Will NOT Be Changed
To ensure absolute safety, the following items are **EXPLICITLY EXCLUDED** from the redesign:
1. **Zero Route Mutations:** All 309 existing route names, URLs, and HTTP methods remain untouched.
2. **Zero Destructive Database Operations:** No tables, columns, or ledgers are modified.
3. **Zero Backend Authorization Alterations:** Role middleware, anti-IDOR checks, and permission policies remain 100% authoritative.
4. **Filament Core Panels Untouched:** The Filament Admin (`/admin`) and Parent Portal (`/wali`) retain their native Filament v3.2 architectures, receiving only aligned color tokens and theme styling.
