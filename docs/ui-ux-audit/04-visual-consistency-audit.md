# 04. VISUAL CONSISTENCY AUDIT: DESIGN SYSTEM DISCONNECTS
**SIAKAD Enterprise & SPP — Typography, Spacing, Color Tokens & Component Styling Analysis**  
**Audit Phase:** Stage A — Analysis & Discovery  
**Execution Date:** 2026-10-13  
**Status:** COMPLETE (READ-ONLY ANALYSIS)

---

## 1. Executive Summary

A visual consistency audit across all 84 Blade views revealed that while an initial design system document exists ([`docs/design/DESIGN_SYSTEM.md`](file:///home/masgansss/Projects/Dev/web_spp/docs/design/DESIGN_SYSTEM.md)), actual implementation across view templates has drifted, exhibiting:
1. Competing primary action colors (Brand Blue vs Emerald Green vs Indigo).
2. Ad-hoc dark mode overrides with hundreds of inline `!important` CSS rules in the master layout.
3. Inconsistent border radius tokens (`rounded-lg`, `rounded-xl`, `rounded-2xl`, `rounded-3xl`).
4. Microscopic text sizes (`text-[9px]`, `text-[10px]`) that fall below accessibility legibility thresholds.
5. Inconsistent table typography (some use monospace figures, others standard proportional fonts).

---

## 2. Identified Inconsistency Dimensions

### 2.1 Competing Primary Action Colors
In enterprise software, the primary action color represents the user's primary mental anchor. In the current templates:
- **`layouts/app.blade.php`:** Uses `bg-brand-600` (`#2563eb`, Royal Blue) for active navigation and system buttons.
- **`siakad/dosen/*`:** Uses `bg-emerald-600` (`#059669`, Emerald Green) for primary actions and sidebar active links.
- **`siakad/dbpta/*`:** Uses `bg-indigo-600` (`#4f46e5`, Deep Indigo) for proposal and bimbingan CTAs.
- **`web/pembayaran/*`:** Uses `bg-blue-600` and `bg-emerald-600` interchangeably.
- **Defect:** Users moving across modules experience an unpredictable primary color language. A unified primary brand token with deliberate semantic accents is required.

---

### 2.2 Inline Dark Mode Override Hacks (`!important`)
In [`resources/views/layouts/app.blade.php`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php) lines 68–340:
```css
html.dark .bg-white {
    background-color: #1c2541 !important;
    color: #f8fafc !important;
}
html.dark .bg-slate-50,
html.dark .bg-slate-50\/50 {
    background-color: #0f172a !important;
    color: #cbd5e1 !important;
}
```
- **The Problem:** Rather than utilizing Tailwind's native `dark:` variant utilities (e.g. `bg-white dark:bg-slate-900`), the layout forces global `!important` CSS overrides.
- **Consequences:**
  - Breaches developer expectations: adding `dark:bg-slate-800` to a specific card is overridden by the layout's global `!important` rule.
  - Causes visual flickering during initial page paint.
  - Inflates layout bundle size unnecessarily.

---

### 2.3 Microscopic Typography & Scale Gaps
- In navigation and badges, classes such as `text-[9px]` and `text-[10px]` are used extensively:
  ```html
  <span class="text-[9px] font-bold font-mono ...">Mahasiswa</span>
  <span class="text-[10px] text-brand-300 font-mono block">NIM: ...</span>
  ```
- **The Problem:** Text rendered at 9px (especially in colored pastel tones like `text-brand-300` on dark slate) is nearly unreadable on mobile screens under outdoor daylight or for users with mild visual impairments.
- **Correction:** The absolute minimum readable text size for metadata must be 11px (`text-[11px]`), with standard supporting text at 12px (`text-xs`) and body at 14px (`text-sm`).

---

### 2.4 Inconsistent Border Radius Tokens
Cards, buttons, inputs, and modals display arbitrary corner rounding across views:
- Buttons: mix of `rounded-lg` (8px), `rounded-xl` (12px), `rounded-2xl` (16px), and `rounded-full` (pill).
- Tables: mix of `rounded-none`, `rounded-xl`, and `rounded-2xl`.
- Modals: mix of `rounded-2xl` and `rounded-3xl`.
- **Target Standard:**
  - Standard interactive controls (buttons, inputs, dropdowns): `rounded-lg` (8px) or `rounded-xl` (12px) uniformly.
  - Surface cards & containers: `rounded-xl` (12px) to `rounded-2xl` (16px).
  - Status badges: `rounded-md` (6px) or `rounded-full`.

---

### 2.5 Form Control Styling Divergence
- In `resources/views/siswa/create.blade.php` and `guru/create.blade.php`: Inputs use classic light-gray styling (`border-slate-300 focus:border-blue-500`).
- In `resources/views/siakad/dbpta/proposal.blade.php`: Inputs use modern dark-aware styling (`bg-slate-50 border-slate-200 dark:bg-slate-800/80 dark:border-slate-700`).
- In `resources/views/auth/login.blade.php`: Inputs use high-contrast glassmorphic styling.
- **Correction:** Consolidate all form inputs to a single reusable Blade input component (`<x-form-input />`, `<x-form-select />`).

---

## 3. Conclusion
Visual inconsistencies stem from incremental development across different project eras without an enforced shared component library. Unifying tokens and replacing layout `!important` hacks with native Tailwind `dark:` classes will immediately elevate visual calm and consistency.
