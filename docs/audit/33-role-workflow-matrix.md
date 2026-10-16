# AUDIT 33: ROLE WORKFLOW MATRIX & PERMISSION TAXONOMY
**SIAKAD Enterprise & SPP — End-to-End Role Journeys, RBAC Matrix & Anti-IDOR Protections**  
**Execution Date:** 2026-10-13  
**Status:** VERIFIED & HARDENED  
**Lead Engineer / Architect:** Antigravity Autonomous Lead Architect

---

## 1. Executive Summary & Persona Architecture

The **SIAKAD Enterprise & SPP** application enforces a strict, hierarchical 6-tier Role-Based Access Control (RBAC) architecture bridging traditional secondary school functions with university-level academic administration.

The six canonical user personas are:
1. **Super Admin / Admin BAAK (`admin`, `superadmin`, `baak`):** Full governance over academic calendars, curricula, courses, faculty, students, tuition structures, and user accounts.
2. **Dosen / Faculty Member (`dosen`, `guru`, `lecturer`):** Manages course attendance, official lecture logs (BAP), academic advising (Dosen PA), KRS approvals, and thesis guidance.
3. **Mahasiswa / Student (`mahasiswa`, `siswa`, `student`):** Manages study planning (Smart KRS), attendance check-in, assignment submissions, grade reports (KHS), academic letter requests, and graduation clearance.
4. **Petugas Keuangan / Cashier (`petugas`, `keuangan`):** Processes point-of-sale cash tuition payments, prints transaction receipts, and views ledger summaries.
5. **Wali Murid / Guardian (`wali`, `parent`, `orang_tua`):** Accesses dedicated Parent Portal (`/wali`) to monitor child attendance, academic progress, and payment status.
6. **Public / Unauthenticated (`guest`):** Interacts with public verification portals, diploma validation (`/verifikasi-ijazah/{token}`), invoice lookups (`/cek-tagihan`), and prospective student registration (`/pmb/register`).

---

## 2. Comprehensive Role-to-Route Capability Matrix

| Feature / URL | Admin / BAAK | Dosen | Mahasiswa | Petugas | Wali | Public | Enforcement Mechanism |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :--- |
| **Public Landing (`/`, `/login`)** | Read | Read | Read | Read | Read | Read | Open / Guest middleware |
| **Invoice Lookup (`/cek-tagihan`)** | Read | Read | Read | Read | Read | Read | Strict Rate-Limit + NIM/DOB match |
| **Diploma Verification (`/verifikasi-ijazah/{token}`)**| Read | Read | Read | Read | Read | Read | Rate-limited SHA256 verification |
| **Admin Panel (`/admin`)** | Full | 403 | 403 | 403 | 403 | 302 | `User::canAccessPanel` (Filament) |
| **Parent Portal (`/wali`)** | Full | 403 | 403 | 403 | Read | 302 | `User::canAccessPanel` (Filament) |
| **User Management (`/web/users`)** | Full | 403 | 403 | 403 | 403 | 302 | `role:superadmin,admin` |
| **Student Catalog (`/web/mahasiswa`)** | Full | 403 | 403 | 403 | 403 | 302 | `role:superadmin,admin` |
| **Course Catalog (`/web/matakuliah`)** | Full | 403 | 403 | 403 | 403 | 302 | `role:superadmin,admin` |
| **Tuition Matrix (`/web/ukt`)** | Full | 403 | 403 | 403 | 403 | 302 | `role:superadmin,admin` |
| **Early Warning System (`/siakad/baak/ews`)**| Full | 403 | 403 | 403 | 403 | 302 | `role:superadmin,admin` |
| **KRS Approval (`/siakad/dosen/krs-approval`)** | Full | Full | 403 | 403 | 403 | 302 | `DosenKrsApprovalController` PA check |
| **Lecture BAP (`/siakad/dosen/bap`)** | Full | Full | 403 | 403 | 403 | 302 | `DosenBapController` Faculty guard |
| **Smart KRS Registration (`/siakad/krs`)**| 403 | 403 | Full | 403 | 403 | 302 | Student identity resolution + lifecycle |
| **KHS Grade Report (`/web/nilai/khs/{id}`)**| Full | View | Own Only | View | View | 302 | Anti-IDOR ownership validation |
| **Payment Receipt (`/web/pembayaran/{id}/cetak`)**| Full | 403 | Own Only | Full | View | 302 | Anti-IDOR ownership validation |
| **E-Surat Requests (`/siakad/esurat`)** | Approval | 403 | Apply | 403 | 403 | 302 | Student profile linkage required |

---

## 3. Anti-IDOR (Insecure Direct Object Reference) Safeguards

1. **Academic Transcript & Grade Reports:**
   - Evaluated in [`NilaiController.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/NilaiController.php).
   - If the user is authenticated as a student, the requested student ID must match `$user->siswa?->id ?? $user->mahasiswa?->id`. Mismatches immediately abort with HTTP 403 Forbidden.
2. **Financial Receipts & Transaction Slips:**
   - Evaluated in [`PembayaranController.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/PembayaranController.php).
   - Authenticated students attempting to load receipts belonging to other students are rejected with HTTP 403 Forbidden.
3. **Automated Verification:**
   - Validated deterministically by `EndToEndRoleWorkflowTest::test_mahasiswa_user_journey_and_anti_idor_boundaries`.

---

## 4. Dual-Domain Identity Resolution Architecture

To eliminate historical regressions where code looked up `Siswa::first()`, the application uses [`App\Traits\ResolvesStudentUser`](file:///home/masgansss/Projects/Dev/web_spp/app/Traits/ResolvesStudentUser.php):
- Resolves polymorphic identity via `$user->siswa` or `$user->mahasiswa`.
- Maps unlinked accounts to null safely, preventing unauthorized privilege creep.
- Guarantees that unlinked users attempting to access student-bound endpoints (e.g. `/siakad/skpi/print`) are halted with HTTP 403.

---

## 5. Conclusion
The 6-tier role architecture provides strict separation of duties, comprehensive anti-IDOR protections, and safe dual-domain identity resolution.
