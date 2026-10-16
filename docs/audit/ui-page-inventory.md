# UI Page & Route Inventory: SIAKAD Enterprise

**Project:** Sistem Informasi Akademik & Keuangan Terpadu (SIAKAD Enterprise)  
**Date:** 2026-10-13  
**Deliverable:** `docs/audit/ui-page-inventory.md`

---

## 1. Inventory Summary
This inventory indexes key user-facing Blade views and Filament panels across all 309 registered routes, detailing role accessibility, component trees, usability status, and WCAG 2.1 AA compliance.

---

## 2. Page & Route Catalog

### 2.1 Public & Authentication Surfaces
| Route | Page Title | Allowed Roles | Primary User Task | UI / Framework Components | Usability & Accessibility Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/login` | Masuk ke SIAKAD | Guest | Authenticate via local credentials or Google SSO | Form card, Google OAuth button, CSRF token | Verified. Clean focus states, error alerts inline. |
| `/pmb/register` | Pendaftaran Mahasiswa Baru | Guest | Self-service student prospective registration | Multi-step form, validation feedback | Verified. Atomic transaction, no race conditions on NIM. |
| `/cek-tagihan` | Cek Tagihan Publik | Guest | Verify student invoice balance | NIM + DOB inputs, masked PII result table | Verified. UU PDP compliant (name & amount masked). |

### 2.2 Student Portal Surfaces (`/siakad/*` & `/web/*`)
| Route | Page Title | Allowed Roles | Primary User Task | UI / Framework Components | Usability & Accessibility Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/siakad/dashboard` | Dashboard Mahasiswa | Mahasiswa | Monitor academic progress, GPA trend, schedules | Metric cards, timetable widget, EWS indicator | Verified. High visual density, 100% mobile drawer parity. |
| `/siakad/krs` | Rencana Studi (Smart KRS) | Mahasiswa | Course enrollment with prerequisite & clash checks | Course catalog table, SKS cap badge, timetable preview | Verified. Pessimistic lock, dynamic SKS caps, conflict warnings. |
| `/siakad/presensi` | Presensi Perkuliahan | Mahasiswa | Check-in to active class sessions via QR / GPS | Geolocation picker, camera scanner, attendance log | Verified. Graceful permission errors, Haversine validation. |
| `/web/nilai/khs/{id}` | Kartu Hasil Studi (KHS) | Mahasiswa, BAAK | Inspect semester grades and download PDF KHS | Printable KHS layout, grade breakdown table | Verified. Anti-IDOR protected (self-only access for students). |
| `/siakad/ukt` | Portal Pembayaran UKT | Mahasiswa | Check invoice status, generate VA, view receipts | Invoices table, VA copy badge, receipt print modal | Verified. Zero negative balances, clean currency format. |
| `/siakad/dbpta/proposal` | Pengajuan Tugas Akhir | Mahasiswa | Submit thesis topic, abstract, and supervisor preference | File upload form, status tracking stepper | Verified. File validation (PDF only, max 5MB). |
| `/siakad/esurat` | Layanan Surat Mahasiswa | Mahasiswa | Request active student letter, transcript recommendation | Letter request form, digital verification badge | Verified. Digital signature token verification. |

### 2.3 Lecturer Portal Surfaces (`/siakad/dosen/*` & `/web/*`)
| Route | Page Title | Allowed Roles | Primary User Task | UI / Framework Components | Usability & Accessibility Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/siakad/dosen/dashboard` | Dashboard Dosen | Dosen | View assigned teaching classes, pending KRS approvals | Class schedule table, approval queue badge | Verified. Responsive grid, direct link to class rosters. |
| `/siakad/dosen/krs-approval` | Persetujuan KRS Mahasiswa | Dosen | Review and approve/reject advisee course plans | Advisee table, course diff drawer, approve/reject dialog | Verified. Anti-IDOR scoped to advising lecturer only. |
| `/siakad/dosen/bap` | Berita Acara Perkuliahan | Dosen | Record class meetings (1-16), attendance, topic notes | Meeting stepper, topic text area, student attendance toggle | Verified. Enforces 16 meetings before grading completion. |
| `/web/nilai` | Input Nilai Perkuliahan | Dosen, BAAK | Enter component scores (Tugas, UTS, UAS) | Grade matrix spreadsheet table, auto-calculating bobot | Verified. Grade lock deadline enforced. |

### 2.4 Administrative & Operator Surfaces (`/web/*` & `/admin/*`)
| Route | Page Title | Allowed Roles | Primary User Task | UI / Framework Components | Usability & Accessibility Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `/web/matakuliah` | Manajemen Mata Kuliah | BAAK, Admin | Course master, prerequisites, SKS allocation | Data table with search, filter by prodi/semester | Verified. Bounded pagination, instant search. |
| `/web/jadwal` | Penjadwalan Kuliah | BAAK, Admin | Assign room, time slot, and lecturer to sections | Timetable grid, collision detection alerts | Verified. Prevents double-booking of rooms/lecturers. |
| `/web/pembayaran` | Kasir & Pembayaran SPP/UKT | Keuangan, Admin | Manual payment receipt, invoice reconciliation | Payment form with live balance calculation, receipt print | Verified. Prevents cashier cascade deletes. |
| `/admin` | Filament Admin Panel | Superadmin, Admin | Enterprise configuration, role assignment, bulk exports | Filament 3.2 resource tables, KPI charts, health widgets | Verified. Restricted to privileged staff. |
| `/parent` | Filament Parent Portal | Orang Tua / Wali | Advisee academic tracking and fee status | Filament read-only widgets and advisee cards | Verified. Scoped strictly to parent's linked student. |
