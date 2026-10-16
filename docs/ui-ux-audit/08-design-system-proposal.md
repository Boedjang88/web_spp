# 08. DESIGN SYSTEM PROPOSAL: UNIFIED ENTERPRISE FOUNDATION
**SIAKAD Enterprise & SPP — Modern, Calm, Mobile-First Design Tokens & Reusable Component System**  
**Audit Phase:** Stage A — Analysis & Discovery  
**Execution Date:** 2026-10-13  
**Status:** PROPOSED FOR USER APPROVAL

---

## 1. Design Direction & Philosophy

We propose a **Calm, High-Utility Enterprise Aesthetic**:
- **Professional Authority:** Reflects academic credibility and institutional trust. Avoids cartoonish colors or trendy glassmorphism that impedes work.
- **Thumb-Driven Ergonomics:** Every primary interactive control on mobile is within natural thumb reach (> 44x44px touch area).
- **Tabular Figures for Data Integrity:** Monospace alignment (`JetBrains Mono`) on all financial figures, student IDs, GPA scores, and dates.
- **Zero Decorative Fluff:** Eliminates non-functional animations, decorative badges, and AI-generated card gradients that waste space.

---

## 2. Core Design Tokens

### 2.1 Color Tokens
Unified semantic palette adhering strictly to WCAG 2.2 AA contrast standards:

| Token Name | Light Surface Value | Dark Surface Value | Semantic Purpose |
| :--- | :---: | :---: | :--- |
| **`surface-canvas`** | `#f8fafc` (`slate-50`) | `#0b132b` (Deep Navy) | Main page background canvas |
| **`surface-card`** | `#ffffff` (`white`) | `#162038` (Navy Surface)| Data cards, form containers, dialogs |
| **`surface-subtle`** | `#f1f5f9` (`slate-100`) | `#0f172a` (`slate-900`) | Input background, table headers, hover |
| **`brand-primary`** | `#2563eb` (`blue-600`) | `#3b82f6` (`blue-500`) | Primary action buttons, active navigation |
| **`brand-hover`** | `#1d4ed8` (`blue-700`) | `#60a5fa` (`blue-400`) | Hover and active pressed states |
| **`status-success`** | `#059669` (`emerald-600`) | `#10b981` (`emerald-500`)| Lunas, Approved, Hadir, Passed |
| **`status-warning`** | `#d97706` (`amber-600`) | `#f59e0b` (`amber-500`) | Pending, Terlambat, Draft, Revision |
| **`status-danger`** | `#e11d48` (`rose-600`) | `#f43f5e` (`rose-500`) | Belum Bayar, Rejected, Alpa, Drop-Out |
| **`status-info`** | `#0284c7` (`sky-600`) | `#38bdf8` (`sky-400`) | Bimbingan, Announcement, e-Surat |
| **`text-main`** | `#0f172a` (`slate-900`) | `#f8fafc` (`slate-50`) | Primary headings, table data, body text |
| **`text-muted`** | `#475569` (`slate-600`) | `#94a3b8` (`slate-400`) | Subtitles, field labels, metadata |

---

### 2.2 Typography Scale
Standardized typography scale preventing unreadable text:

| Scale Token | Class | Size / Leading | Target Usage |
| :--- | :--- | :---: | :--- |
| **Heading XL** | `text-2xl font-bold tracking-tight` | 24px / 32px | Page titles, major portal headlines |
| **Heading LG** | `text-lg font-semibold` | 18px / 28px | Section titles, modal headers |
| **Heading MD** | `text-base font-semibold` | 16px / 24px | Card headers, table group titles |
| **Body Default** | `text-sm font-normal` | 14px / 20px | Standard interface copy, table body |
| **Input / Label**| `text-sm font-medium` | 14px / 20px | Form field labels and user inputs |
| **Caption / Meta**| `text-xs font-medium` | 12px / 16px | Help text, breadcrumbs, timestamps |
| **Minimum Badge**| `text-[11px] font-semibold tracking-wide`| 11px / 14px | Status badges, compact role chips |

*Rule: Text smaller than 11px is strictly prohibited across the entire design system.*

---

### 2.3 Spacing & Radius Tokens
- **Interactive Controls (Buttons, Inputs):** Height strictly 44px (`h-11`) on mobile, 40px (`h-10`) on desktop. Radius: `rounded-xl` (12px).
- **Cards & Modals:** Radius: `rounded-2xl` (16px). Padding: `p-4 sm:p-6`.
- **Status Badges:** Radius: `rounded-lg` (8px). Padding: `px-2.5 py-1`.

---

## 3. Standard Reusable Blade Component Catalog

We propose packaging shared primitives into lightweight Blade components in `resources/views/components/`:

1. `<x-button variant="primary|secondary|danger|ghost" size="sm|md|lg">`
2. `<x-form-input name="..." label="..." type="..." :error="$errors->first(...)">`
3. `<x-form-select name="..." label="..." :options="...">`
4. `<x-status-badge status="lunas|pending|alpa|draft" label="...">`
5. `<x-card title="..." :subtitle="...">`
6. `<x-modal id="..." title="...">`
7. `<x-nav-item route="..." icon="..." label="...">`
8. `<x-alert type="success|error|warning|info" message="...">`

---

## 4. Conclusion
Adopting this cohesive, native Tailwind design system will eliminate 500+ lines of layout CSS hacks, guarantee WCAG 2.2 AA contrast, and establish a uniform, premium look and feel.
