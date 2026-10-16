# AUDIT 24: REAL MODULE INVENTORY & PERMISSION MATRIX
**SIAKAD Enterprise & SPP — Batch I Business Logic & Cross-Module Consistency**  
**Date:** 2026-10-13  
**Status:** VERIFIED & DOCUMENTED  
**Scope:** Complete Discovery of Models, Routes, Roles, Permissions, Workflows, and Test Coverage

---

## 1. Executive Summary
In compliance with Batch I.1 of the Antigravity Master Execution Plan, this document establishes the authoritative, verified module inventory of the actual SIAKAD Enterprise & SPP application. The system comprises **70 Eloquent models**, **14 core service namespaces**, **59 web and API routes**, and Filament administrative panels (`/admin` and `/parent`).

---

## 2. Comprehensive Module Catalog

### 2.1 Academic Administration & Scheduling Core
- **Primary Models:** `Fakultas`, `ProgramStudi`, `Kurikulum`, `MataKuliah`, `MataKuliahPrasyarat`, `KelasKuliah`, `JadwalKuliah`, `TahunAkademik`, `Ruangan`, `Gedung`.
- **Actors & Permissions:** `admin`, `petugas`, `dosen` (BAAK & Dekanat).
- **Core Workflows:**
  1. *Curriculum Definition:* Setting SKS totals, theory/practice breakdowns, course levels, and mandatory/elective categorization (`jenis_mk`).
  2. *Prerequisite Chaining:* Defining course dependencies with minimum grade rank barriers (`MataKuliahPrasyarat`).
  3. *Class Scheduling:* Class capacity definition (`kuota_maksimal`), lecturer assignment, and schedule clash validation (`validateNoStudentScheduleClash`).
- **Test Coverage:** `tests/Feature/Academic/SmartKrsInvariantsTest.php`, `tests/Feature/WeeklyScheduleAndSemesterIntegrationTest.php`.

### 2.2 Student Self-Service & Enrollment (Smart KRS & KHS)
- **Primary Models:** `Krs`, `KrsDetail`, `Nilai`, `NilaiObe`, `BobotPenilaian`, `PresensiKuliah`, `PresensiMahasiswa`.
- **Actors & Permissions:** `siswa` (student), `guru`/`dosen` (academic advisor / Dosen PA), `admin`.
- **Core Workflows:**
  1. *Financial Clearance Gate:* Validation of UKT payment or active grace period (`isFinanciallyCleared()`).
  2. *Lifecycle Gate:* Strict rejection of graduated (`Lulus`), dropped out (`DO`), or inactive students.
  3. *Credit Ceiling Matrix:* Dynamic SKS limit calculation based on previous semester IPS (18, 20, 22, 24 SKS).
  4. *Atomic Enrollment:* Pessimistic locking (`lockForUpdate()`) on class capacity to prevent over-enrollment race conditions.
  5. *Prerequisite Gate:* Verification of prior passing grades ($\ge$ C) before enrolling in advanced courses.
  6. *Schedule Clash Elimination:* Mutual exclusion on overlapping time intervals on identical weekdays.
- **Test Coverage:** `tests/Feature/Academic/SmartKrsInvariantsTest.php`, `tests/Feature/Academic/StudentLifecycleInvariantsTest.php`.

### 2.3 Advanced Academic Workflows (DBPTA & KKN)
- **Primary Models:** `TugasAkhir`, `PembimbingSkripsi`, `LogbookBimbingan`, `SidangSkripsi`, `PenilaianSidang`, `KknRegistrasi`.
- **Actors & Permissions:** `siswa`, `dosen` (supervisor/examiner), `admin` (Komisi Skripsi / LPPM).
- **Core Workflows:**
  1. *KKN Registration:* Precondition enforcement of minimum $\ge 100$ passed SKS (`config('academic.kkn_min_sks')`) and active student status.
  2. *DBPTA Proposal:* Precondition enforcement of minimum $\ge 110$ passed SKS (`config('academic.skripsi_min_sks')`) and active student status.
  3. *Supervision Logbook:* Student creates progress notes (`Pending`), supervisor conducts review and approves (`Disetujui`).
  4. *Final Defense (Sidang):* Three-examiner score aggregation (Presentation 20%, Mastery 40%, Methodology 40%) with automatic letter grade derivation.
- **Test Coverage:** `tests/Feature/Academic/AdvancedModulesWorkflowTest.php`, `tests/Feature/Academic/StudentLifecycleInvariantsTest.php`.

### 2.4 E-Surat Akademik & Student Governance
- **Primary Models:** `SuratAkademik`, `SkpiAktivitas`.
- **Actors & Permissions:** `siswa`, `admin` (BAAK Staff).
- **Core Workflows:**
  1. *Request Submission:* Student submits academic letter request (`Surat Keterangan Aktif`, `Surat Izin Penelitian`, `Surat Cuti`).
  2. *BAAK Review:* State machine transitions from `MENUNGGU_PERSETUJUAN` $\rightarrow$ `DISETUJUI` / `DITOLAK`.
  3. *Secure Document Streaming:* Temporary HMAC signed download links (`URL::temporarySignedRoute`) preventing unauthorized cross-tenant downloads.
- **Test Coverage:** `tests/Feature/StudentEsuratTest.php`, `tests/Feature/Security/SecurityVulnerabilityRegressionTest.php`.

### 2.5 Finance, SPP & UKT Invoicing
- **Primary Models:** `Spp`, `Pembayaran`, `Ukt`, `TagihanUkt`, `PembayaranUkt`, `BankMitra`, `FinancialClearance`, `TransaksiH2h`.
- **Actors & Permissions:** `admin`, `petugas` (Cashier/Finance Officer), Bank Partner Webhooks, Public Guest.
- **Core Workflows:**
  1. *Dual-Domain Ledger:* Historical school SPP monthly records bridged with university semester UKT invoices.
  2. *Idempotent Payments:* Database transactions wrapping payment recording, receipt generation, and clearance unlocking.
  3. *Bank H2H Webhooks:* HMAC SHA-256 signature verification and timestamp replay protection (< 300s).
  4. *Public Self-Service:* `/cek-tagihan` with rate limiting and PII masking.
- **Test Coverage:** `tests/Feature/Finance/FinancialLedgerRegressionTest.php`, `tests/Feature/Security/PublicVerificationSecurityTest.php`.

### 2.6 Graduation Clearance & Diploma Verification
- **Primary Models:** `Siswa`, `Mahasiswa`, `TugasAkhir`, `SidangSkripsi`, `SkpiAktivitas`.
- **Actors & Permissions:** `admin` (Rektorat / BAAK), Public Verifier.
- **Core Workflows:**
  1. *Graduation Audit:* Verification of 144 minimum passed credits, zero failing grades (D/E/BL/T) on mandatory courses, and minimum SKPI points ($\ge 100$).
  2. *Status Mutation:* Transition to `Lulus` with issuance of PIN (`nomor_ijazah`).
  3. *Public Verification (`/verifikasi-ijazah/{token}`):* Cryptographic validation of diploma authenticity, cache invalidation on revocation, and PII protection.
- **Test Coverage:** `tests/Feature/Academic/ExamClearanceAndGradeInvariantsTest.php`, `tests/Feature/Security/PublicVerificationSecurityTest.php`.
