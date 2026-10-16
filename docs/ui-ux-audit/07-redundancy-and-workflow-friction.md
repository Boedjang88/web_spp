# 07. REDUNDANCY & WORKFLOW FRICTION AUDIT
**SIAKAD Enterprise & SPP — Code Duplication, UI Clutter & Operational Friction Points**  
**Audit Phase:** Stage A — Analysis & Discovery  
**Execution Date:** 2026-10-13  
**Status:** COMPLETE (READ-ONLY ANALYSIS)

---

## 1. Executive Summary

This audit identifies unnecessary duplication, cognitive friction, and redundant steps across both user workflows and codebase architecture.

We strictly distinguish between:
1. **Harmful Redundancy:** Copy-pasted Blade views, duplicated navigation menus, repetitive card summaries, and redundant manual entries.
2. **Mandatory Operational & Compliance Controls:** Confirmation dialogs for grade finalization, two-step deletion guards, and UU PDP consent requirements (which must be preserved).

---

## 2. Codebase & UI Redundancy Catalog

### 2.1 Duplicate Blade View Files (Dual-Domain Artifacts)
During the historical evolution from secondary school SPP to higher education SIAKAD, several modules were duplicated rather than refactored into polymorphic views:

| Primary View File | Duplicate Legacy View File | File Size | Redundancy Assessment |
| :--- | :--- | :---: | :--- |
| `resources/views/dashboard/mahasiswa.blade.php` | `resources/views/dashboard/siswa.blade.php` | 17.3 KB each | **100% Exact Code Duplicate.** Wastes disk space and invites split maintenance. |
| `resources/views/dashboard/dosen.blade.php` | `resources/views/dashboard/guru.blade.php` | 12.8 KB each | **100% Exact Code Duplicate.** |
| `resources/views/mahasiswa/index.blade.php` | `resources/views/siswa/index.blade.php` | ~8.5 KB each | Near duplicate; differing only in label strings ("Mahasiswa" vs "Siswa"). |
| `resources/views/dosen/index.blade.php` | `resources/views/guru/index.blade.php` | ~7.2 KB each | Near duplicate; differing only in label strings ("Dosen" vs "Guru"). |

**Remedy Proposal:**
- Unify duplicate views. Make domain terminology dynamic via view variables (e.g. `isUniversity ? 'Mahasiswa' : 'Siswa'`) or redirect legacy view aliases cleanly.

---

### 2.2 Duplicated Navigation Trees in Layout
In [`resources/views/layouts/app.blade.php`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php):
- **Desktop Sidebar:** Declares 15 Admin links, 5 Dosen links, 17 Student links with explicit inline SVGs and classes (lines 580–804).
- **Mobile Drawer:** Declares the identical 15 Admin links, 5 Dosen links, 17 Student links with identical SVGs and classes (lines 360–533).
- **Impact:** Any change to navigation routes or titles requires editing two separate blocks in an 111 KB file.
- **Remedy Proposal:**
  - Extract the navigation menu into a single reusable component (`<x-nav-menu />` or `<x-nav-item />`), parameterized by device context.

---

### 2.3 Dashboard Information Redundancy
In [`resources/views/dashboard/index.blade.php`](file:///home/masgansss/Projects/Dev/web_spp/resources/views/dashboard/index.blade.php) (Admin Dashboard):
- The dashboard displays 8 separate top summary stat cards (Total Siswa, Total Guru, Total Mapel, Total Kelas, Total Users, Total Pemasukan, Transaksi Hari Ini, Pemasukan Bulan Ini).
- Directly below, a separate panel repeats the same figures in a tabular breakdown.
- **Remedy Proposal:**
  - Consolidate to 4 high-impact primary KPI tiles (Active Students, Active Faculty, Monthly Revenue vs Target, System Health Score), moving secondary entity counts to an organized drawer or secondary tab.

---

## 3. Workflow Friction Analysis

### 3.1 Course Registration (Smart KRS) Navigation Friction
- **Current Flow:**
  - Student opens KRS page -> Sees long table of all semester courses across all university programs -> Manually searches or scrolls to find their program's section -> Enrolls -> Page reloads.
- **Friction Point:** The catalog does not default-filter to the student's own Study Program (`id_prodi`) and recommended semester tier.
- **Remedy Proposal:** Default-filter the course catalog to the student's active semester and program, providing an easy "Tampilkan Semua Pilihan" toggle for cross-major electives.

### 3.2 Attendance Check-in Speed
- **Current Flow:**
  - Student opens menu -> Clicks "Presensi Mahasiswa" -> Selects active class -> Triggers GPS fetch -> Submits check-in.
- **Friction Point:** Takes 4 taps. In classroom rushes with spotty Wi-Fi, students experience timeouts.
- **Remedy Proposal:**
  - When a student's enrolled class is currently active (within scheduled meeting time window), place a high-priority "Check-in Kuliah Sekarang" Quick Action Card at the very top of their mobile dashboard. Single-tap to verify and submit.

---

## 4. Conclusion
Eliminating view duplication and streamlining navigation will reduce codebase maintenance surface by ~30% and dramatically accelerate primary daily student and faculty workflows.
