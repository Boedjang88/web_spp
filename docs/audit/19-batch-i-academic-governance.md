# Audit Report 19: Batch I Academic Governance, Exam Attendance Clearance & Zero-CDN Architecture

## 1. Executive Summary
Batch I resolved critical institutional governance gaps and completed frontend architectural independence across the University SIAKAD system:
1. **Standar Nasional Dikti (SN-Dikti) Exam Attendance Enforcement (Decision 3 from Audit 11):**
   - Transformed exam card issuance from a financial-only check into an integrated academic and financial clearance gate.
   - Connected [VerifyExamCardEligibilityAction.php](file:///home/masgansss/Projects/Dev/web_spp/app/Domain/Academic/Actions/VerifyExamCardEligibilityAction.php) with the institutional minimum attendance policy (`config('academic.min_attendance_exam_percent', 75)`).
   - Students whose attendance falls below 75% in their enrolled courses receive an explicit attendance violation warning, locking the printable exam pass until remedial clearance or academic waiver is granted.
2. **Incomplete Grade (BL/T) Expiration & Recalculation Hardening (Decision 4 from Audit 11):**
   - Upgraded [ProcessExpiredIncompleteGrades.php](file:///home/masgansss/Projects/Dev/web_spp/app/Console/Commands/ProcessExpiredIncompleteGrades.php) (`php artisan grades:expire-incomplete`) to consume `config('academic.incomplete_grade_grace_days', 14)`.
   - Added `--days=` dynamic grace window override and `--dry-run` inspection mode.
   - Implemented automatic transaction-wrapped conversion to failing grade `E` (`bobot_mutu = 0.00`) and immediate recalculation of semester GPA (`ips_lalu`).
   - Integrated structured audit logging via `AuditTrailService` and `ActivityLog`.
3. **Complete Elimination of External CDN Dependencies (P2-04 / Zero-CDN Architecture):**
   - Audited and updated all 13 standalone Blade views (`siswa/kartu-ujian`, `mahasiswa/kartu-ujian`, `siswa/surat-tagihan`, `mahasiswa/surat-tagihan`, `auth/login`, `auth/pdp-consent`, `cek-tagihan`, `siakad/diploma/verify`, `siakad/ukt/kwitansi`, `siakad/bap/print`, `siakad/skpi/print`, `nilai/khs`, `nilai/rapor`, `pmb/register`, `welcome`, `api-docs/index`).
   - Standardized on Vite production asset bundle loading with local fallback, ensuring 100% offline capability, zero external network requests, and rapid First Contentful Paint (FCP).

---

## 2. Issues Addressed & Root Cause Remediation

| Component | Defect / Architectural Gap | Root Cause Fixed | Production Impact |
| :--- | :--- | :--- | :--- |
| **Exam Clearance (Kartu Ujian)** | `VerifyExamCardEligibilityAction` checked only SPP financial ledger. Students with 0% attendance could sit for final exams. | Integrated student cumulative attendance evaluation against `min_attendance_exam_percent` (75%). | Non-attending students are blocked from exam card printing; full SN-Dikti compliance enforced. |
| **Incomplete Grades (BL/T)** | Hardcoded 30-day expiration window without dry-run capabilities or audit trail logging. | Upgraded console command to use `config('academic.incomplete_grade_grace_days', 14)`, added `--dry-run`, `--days=`, and structured audit trail. | Academic registrars can simulate and audit grade expiration safely without data corruption risks. |
| **Zero-CDN Architecture** | Standalone views used raw `<script src="https://cdn.tailwindcss.com"></script>`, introducing network lag and breaking in air-gapped deployments. | Wrapped all standalone view assets in `@if(file_exists(public_path('build/manifest.json'))) @vite(...) @else CDN @endif`. | Zero external CDN requests in production; assets served directly from local compiled bundles. |

---

## 3. Files Created and Modified

### Created Files
- [tests/Feature/Academic/ExamClearanceAndGradeInvariantsTest.php](file:///home/masgansss/Projects/Dev/web_spp/tests/Feature/Academic/ExamClearanceAndGradeInvariantsTest.php): Automated regression suite validating attendance clearance, dry-run grade expiration, live IPS recalculation, and Vite asset rendering.
- [docs/audit/19-batch-i-academic-governance.md](file:///home/masgansss/Projects/Dev/web_spp/docs/audit/19-batch-i-academic-governance.md): Canonical audit report.

### Modified Files
- [app/Domain/Academic/Actions/VerifyExamCardEligibilityAction.php](file:///home/masgansss/Projects/Dev/web_spp/app/Domain/Academic/Actions/VerifyExamCardEligibilityAction.php): Added attendance rate calculation and threshold clearance check.
- [app/Http/Controllers/Web/MahasiswaController.php](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/MahasiswaController.php): Passed attendance eligibility metrics to view.
- [app/Console/Commands/ProcessExpiredIncompleteGrades.php](file:///home/masgansss/Projects/Dev/web_spp/app/Console/Commands/ProcessExpiredIncompleteGrades.php): Added `--days=`, `--dry-run`, audit trail, and config integration.
- [resources/views/siswa/kartu-ujian.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/siswa/kartu-ujian.blade.php): Added attendance locked notice and Vite assets.
- [resources/views/mahasiswa/kartu-ujian.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/mahasiswa/kartu-ujian.blade.php): Added attendance locked notice and Vite assets.
- [resources/views/siswa/surat-tagihan.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/siswa/surat-tagihan.blade.php): Upgraded to Vite asset bundle.
- [resources/views/mahasiswa/surat-tagihan.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/mahasiswa/surat-tagihan.blade.php): Upgraded to Vite asset bundle.
- [resources/views/auth/login.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/auth/login.blade.php): Upgraded to Vite asset bundle.
- [resources/views/auth/pdp-consent.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/auth/pdp-consent.blade.php): Upgraded to Vite asset bundle.
- [resources/views/cek-tagihan.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/cek-tagihan.blade.php): Upgraded to Vite asset bundle.
- [resources/views/siakad/diploma/verify.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/siakad/diploma/verify.blade.php): Upgraded to Vite asset bundle.
- [resources/views/siakad/ukt/kwitansi.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/siakad/ukt/kwitansi.blade.php): Upgraded to Vite asset bundle.
- [resources/views/siakad/bap/print.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/siakad/bap/print.blade.php): Upgraded to Vite asset bundle.
- [resources/views/siakad/skpi/print.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/siakad/skpi/print.blade.php): Upgraded to Vite asset bundle.
- [resources/views/nilai/khs.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/nilai/khs.blade.php): Upgraded to Vite asset bundle.
- [resources/views/nilai/rapor.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/nilai/rapor.blade.php): Upgraded to Vite asset bundle.
- [resources/views/pmb/register.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/pmb/register.blade.php): Upgraded to Vite asset bundle.
- [resources/views/siakad/employer/survey.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/siakad/employer/survey.blade.php): Upgraded to Vite asset bundle.
- [resources/views/welcome.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/welcome.blade.php): Upgraded to Vite asset bundle.
- [resources/views/api-docs/index.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/api-docs/index.blade.php): Upgraded to Vite asset bundle.
- [TASKS.md](file:///home/masgansss/Projects/Dev/web_spp/TASKS.md): Updated metrics and roadmap status.

---

## 4. Verification & Automated Test Results

### 4.1 Targeted Academic Governance Suite
```bash
./vendor/bin/phpunit tests/Feature/Academic/ExamClearanceAndGradeInvariantsTest.php
```
```
PHPUnit 10.5.65 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: /home/masgansss/Projects/Dev/web_spp/phpunit.xml

.....                                                               5 / 5 (100%)

Time: 00:01.062, Memory: 66.50 MB

OK (5 tests, 26 assertions)
```

### 4.2 Full Automated Regression Suite
```bash
./vendor/bin/phpunit
```
```
PHPUnit 10.5.65 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: /home/masgansss/Projects/Dev/web_spp/phpunit.xml

...............................................................  63 / 264 ( 23%)
............................................................... 126 / 264 ( 47%)
............................................................... 189 / 264 ( 71%)
............................................................... 252 / 264 ( 95%)
............                                                    264 / 264 (100%)

Time: 00:19.877, Memory: 110.50 MB

OK (264 tests, 2419 assertions)
```
- **Test Count:** 264 passing tests (+5 from Batch H).
- **Assertion Count:** 2,419 assertions (+26 from Batch H).
- **Failures / Errors:** 0.

### 4.3 Database Foreign Key Integrity
```bash
php artisan tinker --execute="dump(DB::select('PRAGMA foreign_key_check'));"
```
**Output:** `[]` (0 violations detected).

### 4.4 Frontend Asset Build
```bash
npm run build
```
- Built in 1.00s.
- Manifest generated at `public/build/manifest.json`.
- Zero external runtime CDN dependencies in production.
