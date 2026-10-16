# Audit Report 17: Batch G Verification & Implementation Report

## 1. Problems Addressed
1. **Public Diploma Verification Security Gap:**
   - Absence of dedicated public verification route for diplomas / PIN SIVIL Kemendikbudristek (`/verifikasi-ijazah/{token}`).
   - Need for cryptographic token validation, graduation state invariant verification, and strict UU PDP No. 27/2022 PII sanitization.
2. **Public Payment Lookup Hardening (`/cek-tagihan`):**
   - Direct browser visits to `GET /cek-tagihan` failed with `405 Method Not Allowed`.
   - Invalid search queries redirected to `route('cek.index')` (which was `'/'` -> redirecting to `/login`), dropping user error feedback.
   - Missing invalid lookup enumeration defense; plaintext identifiers logged in audit trail labels.
3. **Migration Chronology & Engine Compatibility Review:**
   - Date discrepancy between local execution time (2026-10-13) and migration timestamp (`2026_10_15_000001_update_surat_akademiks_status_check.php`).
   - SQLite vs MySQL compatibility gap and missing `down()` rollback method.
4. **Legacy SPP Financial Data Archival Review:**
   - Comprehensive read-only audit of `pembayarans` dependencies across academic, financial, and reporting modules.
   - Zero destructive operations performed.

---

## 2. Root Cause Analysis & Behavioral Impact

| Component | Behavior Before Batch G | Behavior After Batch G | Root Cause Fixed |
| :--- | :--- | :--- | :--- |
| **Diploma Verification** | No endpoint existed; employers could not verify degrees via QR token. | Public endpoint `GET /verifikasi-ijazah/{token}` securely verifies authentic graduated degrees. | Registered route, controller, service, and Permendikbud/UU PDP compliant view. |
| **GET /cek-tagihan** | 405 Method Not Allowed error on direct browser navigation. | Loads self-service student billing search portal with 200 OK. | Added `Route::get('/cek-tagihan', ...)` in `routes/web.php`. |
| **Invalid Search on /cek-tagihan** | Redirected to `/login`, losing flash error message. | Redirects back to `/cek-tagihan` displaying clear error alert and toast. | Fixed `route('cek.index')` route resolution target. |
| **Audit Logs on /cek-tagihan** | Stored raw search strings in `request_payload`. | Uses HMAC-SHA256 keyed hash and 3-char masked preview (`001***`). | Implemented privacy-preserving hashing in `CekTagihanController`. |
| **Brute-Force Enumeration** | Unrestricted scanning allowed across sequential NIMs. | Repeated invalid lookups (>5 failed in 10m) throttled with 429 & Retry-After. | Added `RateLimiter` failed lookup tracking. |
| **Migration 2026_10_15** | SQLite-only logic; no MySQL support; empty `down()`. | Dual SQLite + MySQL engine support; bidirectional `down()` rollback. | Added MySQL `ALTER TABLE` statement and safe table recreation rollback. |

---

## 3. Files Created and Modified

### Created Files
- `app/Services/Academic/DiplomaVerificationService.php` (Cryptographic verification engine, caching, and DTO builder)
- `app/Http/Controllers/Web/DiplomaVerificationController.php` (Public verification controller with dual JSON/Blade support)
- `resources/views/siakad/diploma/verify.blade.php` (Anti-Slop Enterprise diploma verification interface)
- `tests/Feature/Security/PublicVerificationSecurityTest.php` (11 regression tests covering verification, rate-limiting, and privacy)
- `docs/audit/13-public-verification-security.md`
- `docs/audit/14-payment-lookup-hardening.md`
- `docs/audit/15-legacy-spp-archival-assessment.md`
- `docs/audit/16-migration-safety-review.md`
- `docs/audit/17-batch-g-verification.md`

### Modified Files
- `routes/web.php` (Registered `GET /cek-tagihan` and `GET /verifikasi-ijazah/{token}`)
- `app/Http/Controllers/CekTagihanController.php` (Added invalid lookup rate limiting, privacy-preserving audit logs, and corrected redirect target)
- `database/migrations/2026_10_15_000001_update_surat_akademiks_status_check.php` (Added MySQL support branch and `down()` rollback)

---

## 4. Test Execution & Verification Results

### 4.1 Targeted Security Suite
```bash
./vendor/bin/phpunit tests/Feature/Security/PublicVerificationSecurityTest.php
```
**Result:** 11 tests, 39 assertions passed (100% OK, 0 errors, 0 failures, execution time: 1.27s).

### 4.2 Full Automated Regression Suite
```bash
./vendor/bin/phpunit
```
**Result:** **253 tests, 2,370 assertions passed** (100% OK, 0 errors, 0 failures, execution time: 19.05s).

### 4.3 Database Foreign Key Integrity
```bash
php artisan tinker --execute="dump(DB::select('PRAGMA foreign_key_check'));"
```
**Result:** `[]` (0 foreign-key violations).

### 4.4 Fresh Database Migration Test
Ran on isolated scratch database (`database.connections.scratch`):
**Result:** All 38 migrations executed cleanly; 0 foreign-key violations on fresh schema.

---

## 5. Remaining Limitations & Open Business Decisions
1. **Physical Diploma QR Code Standardization:** University administration must specify whether physical paper diplomas will print the full URL (`https://siakad.domain/verifikasi-ijazah/{token}`) or short PIN (`PIN-2026-000101`). Both formats are supported by `DiplomaVerificationService`.
2. **Legacy Financial Archival Timing:** Formal adoption of the 10-year in-place retention policy recommended in `docs/audit/15-legacy-spp-archival-assessment.md` requires University Finance Directorate sign-off before executing any cold-storage pipeline.

---

## 6. Next Recommended Batch (Batch H)
- **Scope:** Edge Cases, Final Production Hardening & Staging Readiness Review.
- **Components:**
  1. UKT late payment grace period policy lock.
  2. Cold-storage archival strategy for high-frequency GPS attendance logs (`presensi_mahasiswas`).
  3. Pre-flight production staging verification with MySQL driver configuration.
