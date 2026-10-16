# 06. ACCESSIBILITY AUDIT: WCAG 2.2 LEVEL AA COMPLIANCE
**SIAKAD Enterprise & SPP — Contrast, Keyboard Traps, ARIA Semantics & Screen Reader Evaluation**  
**Audit Phase:** Stage A — Analysis & Discovery  
**Execution Date:** 2026-10-13  
**Status:** COMPLETE (READ-ONLY ANALYSIS)

---

## 1. Executive Summary

Public educational and administrative systems must be accessible to students, educators, and guardians of all physical abilities, in accordance with national accessibility standards and WCAG 2.2 Level AA guidelines.

This audit evaluates:
1. Color contrast ratios across light and dark modes.
2. Keyboard operability and visible focus indicators.
3. Form accessibility, programmatic label associations, and error announcement.
4. Screen-reader semantics and ARIA attributes.
5. Touch target dimensions on mobile surfaces.

---

## 2. Detailed WCAG 2.2 AA Findings

### 2.1 Color Contrast Failures (Criterion 1.4.3 / 1.4.11)
- **Defect 1: Pale Brand Text on Dark Surfaces**
  - Location: Master layout user profile and sidebar tags.
  - Markup: `<span class="text-[10px] text-brand-300 font-mono block">NIM: 2026001001</span>`
  - Current Contrast: `#93c5fd` (`brand-300`) on `#0f172a` (`slate-900`) achieves ~4.8:1 (passes for bold text), but when rendered in light mode or against lighter card backgrounds, contrast drops to **2.6:1** (Fails 4.5:1 requirement).
- **Defect 2: Secondary Slate Text on White Cards**
  - Location: Table metadata, timestamp notes in `activity-logs` and `laporan`.
  - Markup: `text-slate-400` (`#94a3b8`) on white (`#ffffff`).
  - Current Contrast: **2.9:1** (Fails 4.5:1 minimum).
  - Remedy: Replace `text-slate-400` with `text-slate-600` (`#475569`, 5.4:1 contrast) on all light surfaces.

---

### 2.2 Missing `aria-label` on Icon-Only Interactive Buttons (Criterion 4.1.2)
- **Affected Elements:**
  - Table delete/trash buttons: `<button type="submit" class="..."><svg ...></svg></button>`
  - Mobile menu toggle buttons: `<button onclick="toggleMobileMenu()"><svg ...></svg></button>`
  - Modal dismiss buttons: `<button onclick="closeModal()">&times;</button>`
- **The Problem:** Screen readers announce these buttons as "button", with zero contextual description of what clicking it will do.
- **Remedy:** Every icon button must have an explicit `aria-label` attribute (e.g. `aria-label="Hapus mata kuliah ini"` or `aria-label="Tutup menu navigasi"`).

---

### 2.3 Keyboard Focus Trapping & Drawer Management (Criterion 2.1.2 / 2.4.3)
- **Affected Component:** Mobile Navigation Drawer in `layouts/app.blade.php`.
- **Observed Behavior:**
  - Toggling `toggleMobileMenu()` displays the overlay drawer by removing `hidden`.
  - When open, pressing `Tab` continues tabbing through the underlying background page content rather than trapping focus within the drawer.
  - Pressing the `Escape` key does not dismiss the drawer.
- **Remedy:** Implement accessible drawer focus management (trap focus on open, return focus to trigger on close, dismiss on `Escape`).

---

### 2.4 Color-Independent Status Indicators (Criterion 1.4.1)
- **Criterion Requirement:** Color must not be used as the sole visual means of conveying information, indicating an action, or prompting a response.
- **Current Defect:**
  - In several table views (e.g. `web/mahasiswa/index.blade.php` and `web/presensi/index.blade.php`), student presence or enrollment status is indicated primarily via small colored dots (`w-2 h-2 rounded-full bg-emerald-500` vs `bg-rose-500`).
- **Remedy:** Every status badge must include explicit text labels (e.g. `Hadir`, `Izin`, `Alpa`, `Lunas`, `Belum Lunas`) alongside visual icons.

---

### 2.5 Form Label Programmatic Association (Criterion 1.3.1 / 3.3.2)
- **Current State:**
  - Most form inputs use standard `<label>` tags, but in some dynamically added forms (e.g. bulk presence checkboxes or quick search bars), inputs lack explicit `id` attributes matching `<label for="...">`.
- **Remedy:** Standardize with reusable form components ensuring every input generates a deterministic UUID and matching label.

---

## 3. Accessibility Compliance Scorecard

| WCAG Principle | Audit Status | Key Remediation Required |
| :--- | :---: | :--- |
| **1. Perceivable** | Partial | Replace low-contrast `text-slate-400` with `text-slate-600`; eliminate 9px font sizes. |
| **2. Operable** | Partial | Add `aria-label` to icon buttons; implement `Escape` key and focus trap on mobile drawer. |
| **3. Understandable** | Good | Flash alerts are distinct and dismissible; validation errors positioned near inputs. |
| **4. Robust** | Good | Semantic HTML5 structure (`header`, `nav`, `main`, `footer`, `aside`, `table`). |
