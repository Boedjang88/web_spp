# UI/UX Regression Verification Checklist: SIAKAD Enterprise

**Project:** Sistem Informasi Akademik & Keuangan Terpadu (SIAKAD Enterprise)  
**Date:** 2026-10-13  
**Deliverable:** `docs/audit/ui-regression-checklist.md`

---

## 1. Quality Gates & Acceptance Verification

| Category | Verification Item | Standard / Criteria | Status | Evidence / Notes |
| :--- | :--- | :--- | :---: | :--- |
| **Aesthetics & Tokens** | Typography | Inter / Plus Jakarta Sans for UI, JetBrains Mono for codes/grades/Rupiah | **PASS** | Defined in `DESIGN_SYSTEM.md` & `resources/css/app.css` |
| | Color Palette | Deep Navy (`brand-950`), Slate canvas (`surface-base`), accessible contrast | **PASS** | Verified via Tailwind design system |
| | WCAG 2.1 AA Contrast | Minimum 4.5:1 on table headers, subtitles, badges, and button labels | **PASS** | Tested on light & dark surfaces |
| **Forms & Input UX** | Input Labels | Every input has an explicit label and placeholder | **PASS** | Evaluated on all Blade CRUD forms |
| | Inline Validation | Server & client errors appear directly under invalid input field | **PASS** | Form inputs preserve old values on validation fail |
| | Double-Submit Lock | Submit buttons disable with loading spinner on first click | **PASS** | Handled in Alpine/JS form submission handlers |
| | Masked PII Display | NIK, mother's maiden name, and phone masked where displayed | **PASS** | UU PDP compliance enforced |
| **Tables & Data Display** | Tabular Figures | Numbers, NIM, SKS, and Rupiah currency align vertically with monospaced figures | **PASS** | Monospaced numeric alignment enabled |
| | Bounded Pagination | Large collections capped with pagination (10/25/50/100 rows per page) | **PASS** | No unbounded `::all()` or `::get()` in tables |
| | Empty State Handling | Empty tables show informative empty state illustrations and call-to-actions | **PASS** | Consistent empty state components |
| **Modals & Feedback** | Alert Fatigue | Zero blocking alert modals on page load (`showAlertModal` removed from DOM load) | **PASS** | Replaced with auto-dismissing toast notifications |
| | Destructive Confirm | Irreversible actions (Delete, Reject, Drop Course) require confirmation dialog | **PASS** | Modals explicitly display item name and consequences |
| **Navigation & Responsive**| Desktop/Mobile Parity | Mobile drawer menu contains 100% of the links present in desktop sidebar | **PASS** | Verified across all roles (Mahasiswa, Dosen, BAAK) |
| | Screen Breakpoints | Usable without horizontal scroll on mobile (375px+), tablet, and desktop | **PASS** | Responsive table wrappers (`overflow-x-auto`) |
| **Security & Permissions** | Anti-IDOR Authorization| Direct URL parameter tampering returns 403 Forbidden | **PASS** | Tested in `SecurityVulnerabilityRegressionTest.php` |
| | CSRF Protection | All state-changing POST/PUT/DELETE forms contain `@csrf` tokens | **PASS** | Protected by Laravel `VerifyCsrfToken` middleware |
| | Asset Compilation | Zero runtime dependencies on external CDNs for production styling | **PASS** | Vite compiled bundle in `public/build/assets/` |
