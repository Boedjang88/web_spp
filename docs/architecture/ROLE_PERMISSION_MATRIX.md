# Role & Permission Matrix: SIAKAD Enterprise

**Project:** Sistem Informasi Akademik & Keuangan Terpadu (SIAKAD Enterprise)  
**Date:** 2026-10-13  
**Deliverable:** `docs/architecture/ROLE_PERMISSION_MATRIX.md`

---

## 1. Enterprise Role Taxonomy

The system defines 6 primary authenticated roles plus Public guest access:

1. **Superadmin (`superadmin`):** Full system configuration, database audit access, role delegation, API credentials.
2. **Admin / BAAK (`admin`, `baak`):** Academic curriculum management, student & lecturer records, scheduling, grade unlocking, graduation approvals.
3. **Finance / Keuangan (`keuangan`, `petugas`):** Tuition tariff configuration, UKT generation, manual payment entry, bank reconciliation, financial clearance overrides.
4. **Lecturer / Dosen (`dosen`, `guru`):** Class roster inspection, teaching log (BAP) entry, student grading, academic advisory (KRS approval), thesis supervision.
5. **Student / Mahasiswa (`mahasiswa`, `siswa`):** Course enrollment (Smart KRS), attendance check-in, KHS & transcript viewing, billing inspection, thesis submission, e-Surat requests.
6. **Parent / Wali (`wali`, `orang_tua`):** Dedicated portal (`/parent`) for read-only monitoring of advisee attendance, semester grades, and tuition payment clearance.
7. **Public / Guest:** PMB self-registration (`/pmb/register`), public billing verification (`/cek-tagihan` with NIM+DOB), certificate QR verification.

---

## 2. Resource-to-Action Permission Matrix

| Resource / Action | Superadmin | BAAK / Admin | Finance | Dosen | Mahasiswa | Parent | Public |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **System Settings & Backups** | **CRUD** | - | - | - | - | - | - |
| **API Docs & Health Telemetry** | **Read** | **Read** | - | - | - | - | Minimal |
| **Faculty & Study Programs** | **CRUD** | **CRUD** | Read | Read | Read | - | - |
| **Curriculum & Courses** | **CRUD** | **CRUD** | - | Read | Read | - | - |
| **Class Scheduling & Rooms** | **CRUD** | **CRUD** | - | Read (Assigned) | Read (Enrolled) | - | - |
| **Student Master Records** | **CRUD** | **CRUD** | Read | Read (Advisees) | Read (Self Only) | Read (Advisee) | - |
| **Lecturer Master Records** | **CRUD** | **CRUD** | Read | Read (Self Only) | Read (Instructors) | - | - |
| **Smart KRS Registration** | Override | Override | - | - | **Submit/Draft** | - | - |
| **KRS Approval (Advising)** | Override | Override | - | **Approve/Reject** | - | - | - |
| **BAP & Teaching Verification** | Override | Audit | - | **CRUD (Own Class)** | Read | - | - |
| **Grading & OBE Assessment** | Override | Unlock/Edit | - | **Input/Update** | Read (Self Only) | Read (Advisee) | - |
| **KHS & Transcript Download** | Export | Export | - | Read (Advisees) | **Export (Self)** | Read (Advisee) | - |
| **Attendance Verification** | Audit | Audit | - | **Verify/Record** | Check-in (GPS) | Read (Advisee) | - |
| **UKT Invoicing & Tariffs** | **CRUD** | Read | **CRUD** | - | Read (Self Only) | Read (Advisee) | - |
| **Payment Entry & Reconciliation** | Audit | - | **CRUD** | - | - | - | - |
| **Public Tagihan Lookup** | Read | Read | Read | - | Read | Read | **NIM+DOB** |
| **DBPTA / Skripsi Management** | Manage | Manage | - | Supervise/Grade | Submit/Logbook | - | - |
| **E-Surat Requests & Printing** | Sign | Verify/Issue | - | - | Request | - | - |
| **EDOM Evaluations** | Audit | Analytics | - | Summary Read | Submit | - | - |
| **Early Warning System (EWS)** | Full Scan | Full Scan | View Risk | Advisee Alerts | - | - | - |

---

## 3. Object-Level Authorization & Anti-IDOR Protections

1. **Self-Ownership Guards:**
   - Any endpoint serving student-specific records (e.g. `/web/nilai/khs/{id}`, `/web/pembayaran/{id}/cetak`, `/web/siswa/{id}/kartu-ujian`) verifies that `$user->id_siswa === $record->id_siswa` or aborts with `403 Forbidden`.
2. **Lecturer Assignment Guards:**
   - Lecturers can only input grades and sign BAP for classes where their ID appears in `kelas_kuliahs.id_dosen` or the `dosen_kelas` pivot table.
3. **Advisor Scoping Guards:**
   - KRS approvals (`/siakad/dosen/krs-approval/{id}`) verify that the advising student's `id_dosen_wali` matches the authenticated lecturer.
