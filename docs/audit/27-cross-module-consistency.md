# AUDIT 27: CROSS-MODULE CONSISTENCY & STATE MACHINE INVARIANTS
**SIAKAD Enterprise & SPP — Batch I Business Logic & Cross-Module Consistency**  
**Date:** 2026-10-13  
**Status:** VERIFIED & HARDENED  
**Scope:** Elimination of Contradictory States Across Academic, Financial, and Student Lifecycle Domains

---

## 1. Executive Summary
In compliance with Batch I.2 and I.5 of the Antigravity Master Execution Plan, this audit investigated cross-module boundaries to identify and resolve contradictory states between academic administration, student services, financial ledgers, and graduation clearance. All identified contradictions have been hardened with automated regression tests.

---

## 2. Hardened Cross-Module Invariants

### 2.1 Student Lifecycle vs Active Enrollment Invariant
- **Identified Contradiction:** Previously, `SmartKrsService::enrollClassWithPessimisticLock` validated financial clearance and prerequisite courses, but did not check `status_kelulusan`. Consequently, graduated alumni or students marked as `Drop Out` or `Dikeluarkan` could theoretically enroll in new classes.
- **Remediation:** Added a strict student lifecycle guard at the entry point of [`SmartKrsService.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/SmartKrsService.php):
  ```php
  $student = Siswa::find($idSiswa) ?? Mahasiswa::find($idSiswa);
  if ($student && in_array($student->status_kelulusan, ['Lulus', 'Lulus Wisuda', 'LULUS', 'Dikeluarkan', 'Drop Out', 'DO', 'Cuti'])) {
      throw new Exception("Mahasiswa dengan status akademik '{$student->status_kelulusan}' tidak diizinkan melakukan pengisian KRS.");
  }
  ```
- **Verification:** Verified by `test_graduated_student_cannot_enroll_krs` in [`StudentLifecycleInvariantsTest.php`](file:///home/masgansss/Projects/Dev/web_spp/tests/Feature/Academic/StudentLifecycleInvariantsTest.php).

### 2.2 Thesis Proposal & KKN Registration Lifecycle Invariant
- **Identified Contradiction:** Graduated students could access DBPTA proposal and KKN registration endpoints and create new thesis or fieldwork records.
- **Remediation:**
  - Hardened [`DbptaProposalController.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/DbptaProposalController.php) to reject proposal submissions from graduated or inactive students.
  - Hardened [`KknRegistrationService.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Domain/Academic/Services/KknRegistrationService.php) to reject KKN registrations from students with inactive/graduated statuses.
- **Verification:** Verified by `test_graduated_student_cannot_submit_dbpta_proposal` and `test_graduated_student_cannot_register_kkn`.

### 2.3 SKS Calculation Deduplication on Repeated Courses
- **Identified Contradiction:** If a student repeated a 4-SKS course to improve their grade from D to A, naive queries summing `krs_details.sks` would report 8 SKS instead of 4 SKS, potentially creating premature graduation clearance.
- **Remediation:** [`GraduationClearanceService.php`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/GraduationClearanceService.php) partitions enrollment attempts by `id_mk`, selects the highest grade point, and accumulates SKS for each unique passed course exactly once.
- **Verification:** Verified by `test_passed_sks_calculation_replaces_repeated_course_grade`.

### 2.4 Financial Clearance vs KRS Status Synchronization
- **Consistency Enforcement:**
  - Payment of UKT automatically writes a `CLEARED` record to `financial_clearances`.
  - Conversely, an unexpired grace period (`config('academic.ukt_grace_period_days')`) permits enrollment while keeping invoice status as unpaid, avoiding deadlock between academic registration and payment processing.

### 2.5 Role & Object Authorization Invariants
- **Supervisor Logbook Approval:** Lecturers can only approve bimbingan logbooks where they are the assigned advisor.
- **Student Ownership:** Students cannot view, edit, or submit forms on behalf of other students.
- **KRS Approval:** Academic advisors (`Dosen PA`) can approve KRS for their advisees; students cannot approve their own KRS.
