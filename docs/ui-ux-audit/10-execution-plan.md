# 10. EXECUTION PLAN: SEQUENTIAL PHASED ROADMAP
**SIAKAD Enterprise & SPP — Incremental, Low-Risk Implementation Strategy**  
**Audit Phase:** Stage A — Analysis & Discovery  
**Execution Date:** 2026-10-13  
**Status:** PROPOSED FOR USER APPROVAL

---

## 1. Execution Principles

Following the Master Directive, implementation in Stage B will proceed **strictly in ordered, verified phases**. Each phase:
1. Has clearly defined file boundaries and zero backend logic mutation.
2. Is tested against automated regression suites (`./vendor/bin/phpunit`), database foreign key checks (`PRAGMA foreign_key_check`), and asset compilation (`npm run build`).
3. Can be rolled back independently via Git if an unintended regression occurs.

---

## 2. Phase-by-Phase Roadmap

### Phase 1: Foundation & Design System Components
- **Tasks:**
  - Create standard Blade component primitives in `resources/views/components/`:
    - `<x-button />`, `<x-card />`, `<x-status-badge />`, `<x-modal />`, `<x-alert />`.
  - Harmonize color tokens in `resources/css/app.css` and `tailwind.config.js`.
- **Files Affected:** `resources/views/components/*`, `resources/css/app.css`.
- **Dependencies:** None.
- **Risk / Effort:** Low / S.
- **Verification:** `npm run build`, inspect component rendering in isolation.
- **Definition of Done:** Components created, tested with zero build errors.

---

### Phase 2: Application Shell & Navigation De-duplication
- **Tasks:**
  - Decompose `layouts/app.blade.php`: extract `<x-nav-menu />`, `<x-sidebar />`, and `<x-mobile-drawer />`.
  - Eliminate duplicated navigation markup between mobile and desktop trees.
  - Remove layout `!important` CSS overrides; replace with native Tailwind `dark:` variants.
  - Replace overlapping floating bottom dock on mobile with clean top header and slide-over drawer; remove `pb-24` void.
  - Standardize branding to "SIAKAD Enterprise".
- **Files Affected:** `resources/views/layouts/app.blade.php`, `resources/views/components/navigation/*`.
- **Dependencies:** Phase 1.
- **Risk / Effort:** Medium / M.
- **Verification:** Login and navigate across all 6 roles on mobile (375px) and desktop (1280px); verify zero obscured buttons; `./vendor/bin/phpunit`.
- **Definition of Done:** Single navigation source of truth; zero overlapping mobile dock; clean dark mode toggle.

---

### Phase 3: Role-Aware Navigation Grouping
- **Tasks:**
  - Restructure Student navigation menu from 17 flat items into 4 clear collapsible clusters (Utama, Akademik, Layanan & Keuangan, Tingkat Akhir).
  - Restructure Lecturer navigation into clear classroom vs advising sections.
- **Files Affected:** `resources/views/components/navigation/*`.
- **Dependencies:** Phase 2.
- **Risk / Effort:** Low / S.
- **Verification:** Verify all route links resolve correctly without 404s; test role permissions.
- **Definition of Done:** Scannable navigation tree; daily tasks accessible in 1 tap.

---

### Phase 4: Role-Based Dashboard Optimization & Redundancy Removal
- **Tasks:**
  - Consolidate duplicate dashboard files: unify `dashboard/mahasiswa.blade.php` (remove redundant `dashboard/siswa.blade.php` copy).
  - Unify `dashboard/dosen.blade.php` (remove redundant `dashboard/guru.blade.php` copy).
  - Add prominent "Kuliah Hari Ini & Presensi Cepat" action card to student dashboard when active class is scheduled.
  - Consolidate Admin dashboard to 4 primary KPI tiles.
- **Files Affected:** `resources/views/dashboard/*`, `app/Http/Controllers/Web/DashboardController.php`.
- **Dependencies:** Phases 1–3.
- **Risk / Effort:** Low / M.
- **Verification:** `./vendor/bin/phpunit tests/Feature/Academic/EndToEndRoleWorkflowTest.php`.
- **Definition of Done:** Zero duplicate dashboard views; student can check-in to active class in 1 tap from home.

---

### Phase 5: Mobile-Responsive Table & Smart KRS Optimization
- **Tasks:**
  - Implement adaptive card layout for mobile viewports (< 768px) on Smart KRS (`siakad/krs/index.blade.php`) and Class Schedule (`jadwal/index.blade.php`).
  - Ensure "Ambil Kelas" CTA is visible full-width on mobile cards without horizontal scrolling.
  - Retain dense data tables on desktop (>= 768px).
- **Files Affected:** `resources/views/siakad/krs/index.blade.php`, `resources/views/jadwal/index.blade.php`.
- **Dependencies:** Phases 1–4.
- **Risk / Effort:** Medium / M.
- **Verification:** Test course enrollment workflow on 375px mobile emulator and desktop browser; `./vendor/bin/phpunit tests/Feature/Academic/SmartKrsInvariantsTest.php`.
- **Definition of Done:** Zero horizontal scrolling on mobile; course enrollment accessible in 1 tap.

---

### Phase 6: Form Control & Validation Standardization
- **Tasks:**
  - Standardize inputs with `<x-form-input />` across high-frequency forms (`siswa/create`, `dosen/create`, `matakuliah/create`, `dbpta/proposal`).
  - Enforce single-column form grids on mobile (`grid-cols-1 md:grid-cols-2`).
  - Standardize 44px touch height and clear inline error associations.
- **Files Affected:** Form views across academic modules.
- **Dependencies:** Phase 1.
- **Risk / Effort:** Low / M.
- **Verification:** Submit valid and invalid forms; assert validation errors attach directly to input fields.
- **Definition of Done:** 100% single-column mobile forms; 44px minimum touch targets.

---

### Phase 7: Accessibility & Contrast Polish
- **Tasks:**
  - Audit and remediate remaining color contrast issues (replace `text-slate-400` with `text-slate-600` on white surfaces).
  - Add explicit `aria-label` attributes to all icon-only buttons (trash/delete, modal close, menu toggle).
  - Implement focus trapping and `Escape` key handler on mobile slide-over drawer.
- **Files Affected:** Layout and component templates.
- **Dependencies:** Phases 1–6.
- **Risk / Effort:** Low / S.
- **Verification:** Keyboard-only navigation audit (Tab, Shift+Tab, Enter, Escape); contrast analyzer.
- **Definition of Done:** WCAG 2.2 Level AA compliance verified with zero keyboard traps.

---

### Phase 8: Final Regression Verification & Handover
- **Tasks:**
  - Execute full automated test suite: `./vendor/bin/phpunit` (target: 284+ tests passing, 0 failures).
  - Verify SQLite foreign key integrity: `php artisan tinker --execute="dump(DB::select('PRAGMA foreign_key_check'));"`.
  - Compile final production assets: `npm run build`.
  - Update `TASKS.md` with final UI/UX redesign sign-off.
- **Files Affected:** None (testing & build).
- **Dependencies:** All previous phases.
- **Risk / Effort:** Low / S.
- **Verification:** 100% automated test pass; zero console errors; clean Git diff review.
- **Definition of Done:** Production build succeeds; zero functional regressions.

---

## 3. Rollback Strategy
Each phase is bounded to isolated Git commits. If an unexpected regression occurs during any phase, reverting that phase's commit restores the working tree instantly without database side-effects.
