# 05. MOBILE-FIRST AUDIT: RESPONSIVE INTERACTION GAPS
**SIAKAD Enterprise & SPP — Small Screen Evaluation (320px–767px), Breakpoint Behavior & Touch UX**  
**Audit Phase:** Stage A — Analysis & Discovery  
**Execution Date:** 2026-10-13  
**Status:** COMPLETE (READ-ONLY ANALYSIS)

---

## 1. Executive Summary

Because higher education students access information systems predominantly via smartphones (85%+ in modern university cohorts), a mobile-first design is a mission-critical functional requirement, not an optional visual enhancement.

This audit evaluates the system across five viewport tiers:
1. **Small Mobile (320–374 px):** iPhone SE, older compact Android devices.
2. **Standard Mobile (375–430 px):** iPhone 13/14/15/16, Samsung Galaxy S22/S23/S24.
3. **Large Mobile (431–767 px):** Foldable inner screens, large Pro Max devices.
4. **Tablet (768–1023 px):** iPad 10th gen, iPad Air, Galaxy Tab.
5. **Desktop (1024 px+):** Laptops, high-res administrative workstations.

---

## 2. Critical Mobile Findings by Viewport Class

### 2.1 The Floating Bottom Dock Collision (All Mobile Viewports < 768px)
- **The Defect:**
  - `layouts/app.blade.php` renders a floating bottom dock (`fixed bottom-3 left-3 right-3 z-40 bg-slate-950/90 rounded-2xl`).
  - When a student or teacher opens a form on mobile (e.g. `siakad/dbpta/proposal.blade.php`, `siakad/presensi/index.blade.php`, or `pembayaran/create.blade.php`), the floating dock directly sits on top of:
    - Primary "Submit / Simpan" buttons located at the bottom of the form card.
    - Pagination links at the bottom of data tables.
    - Validation error banners.
  - When the on-screen virtual keyboard opens (iOS Safari or Android Chrome), the fixed dock often jumps upward or creates strange viewport jitter.
- **The Artificial Compensation:**
  - The main container applies `pb-24 md:pb-0`. This leaves an unnatural 96px empty void at the bottom of every page on mobile, yet still fails when forms have dynamic validation or fixed submit bars.
- **Mobile-First Recommendation:**
  - Replace the floating dock with a clean, standardized **Native Bottom App Bar** or a responsive **Top App Header with Slide-Over Drawer**.
  - Ensure all sticky action bars on mobile are attached cleanly to the viewport base with safe-area insets (`env(safe-area-inset-bottom)`).

---

### 2.2 Horizontal Overflow on Administrative & Academic Tables
- **Affected Views:**
  - `web/jadwal/index.blade.php` (Timetable grid).
  - `web/nilai/index.blade.php` (Grade overview table).
  - `siakad/krs/index.blade.php` (Available course catalog).
  - `web/pembayaran/index.blade.php` (Payment ledger).
- **Observed Behavior on 375px Viewports:**
  - Tables contain 7 to 10 columns (Kode, Mata Kuliah, Dosen, Kelas, Ruang, Jam, SKS, Kuota, Aksi).
  - Minimum table widths force a horizontal scroll of 400px+ beyond the screen edge.
  - Action buttons ("Pilih Kelas", "Edit", "Hapus") reside in the far right column, completely invisible upon initial page load without horizontal swiping.
- **Mobile-First Recommendation:**
  - Implement a **Responsive Card Pattern** on mobile viewports (< 768px):
    - Display Course Name and Code as primary card header.
    - Display Dosen, SKS, Schedule, and Quota as compact metadata badges.
    - Provide a full-width, thumb-friendly primary action button ("Ambil Kelas") directly on the card.
  - On desktop viewports (>= 768px), retain the dense high-throughput tabular grid.

---

### 2.3 Form Multi-Column Cramping on Small Screens
- **Affected Views:** `resources/views/siswa/create.blade.php`, `guru/create.blade.php`, `web/matakuliah/create.blade.php`.
- **Observed Behavior:**
  - Several forms use `grid grid-cols-2 gap-4` without a responsive breakpoint prefix (missing `sm:` or `md:`).
  - On a 320px–375px phone screen, this renders two 140px-wide input fields side by side.
  - Long labels (e.g. "Tempat Lahir", "Nomor Induk Siswa Nasional") wrap awkwardly into 3–4 lines, making the form visually chaotic and prone to misclicks.
- **Mobile-First Recommendation:**
  - Strict rule: All form layouts must be single-column (`grid-cols-1`) on mobile, enhancing to two or three columns only on `md:` (768px+) screens.

---

### 2.4 Touch Target Size Violations (< 44x44 px)
- **WCAG Success Criterion 2.5.5 / 2.5.8:** Target Size (Minimum 44x44px or 24x24px with sufficient spacing).
- **Violations Observed:**
  - Table pagination buttons: `p-1 text-xs` (approx 28x28px total tap area).
  - Dropdown filter triggers: icon-only buttons with 24x24px dimensions.
  - Inline table action buttons (e.g. small trash can icon `w-4 h-4` in KRS detail): easily missed on capacitive touchscreens.
- **Mobile-First Recommendation:**
  - Ensure all clickable touch elements have a minimum interactive touch area of 44x44px via padding (`p-2.5` to `p-3`) or pseudo-element hit area expansions.

---

## 3. Summary of Mobile UX Target State

| UI Element | Current Mobile Behavior | Target Reengineered Mobile State |
| :--- | :--- | :--- |
| **Navigation** | Overlapping floating dock + duplicate drawer | Clean Top Header + Drawer + optional sticky contextual footer |
| **Tables** | Rigid horizontal scroll table with hidden actions | Prioritized responsive card list with full-width primary action |
| **Forms** | 2-column cramped grid, small touch inputs | Single-column thumb-friendly inputs (h >= 44px) |
| **Action Buttons** | Obscured by floating dock, small icons | Clear full-width sticky bottom action bar (where appropriate) |
| **Viewport Overflow** | Frequent unintended horizontal drift | 100% strict `overflow-x: hidden` zero-drift viewport |
