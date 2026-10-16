# Audit Report 18: Current State Reconciliation & Codebase Verification

**Document Reference:** `docs/audit/18-current-state-reconciliation.md`  
**Execution Timestamp:** 2026-10-13T12:40:00+07:00  
**Inspection Mode:** Evidence-Driven Repository Discovery & Historical Claim Audit  
**Operating Rules:** Zero assumption of prior claim validity; physical source and test verification.

---

## 1. Executive Summary

This document performs an independent, empirical reconciliation of the SIAKAD Enterprise & SPP repository prior to executing Batch H. Previous sprint summaries (Batches A through G) claimed full completion of 42+ defects, 253 automated tests passing, 0 database foreign-key violations, and zero-CDN frontend compilation.

Each historical claim has been inspected directly in the filesystem, database schema, routing tables, and test runners.

---

## 2. Inventory Inspected

### 2.1 Framework & Runtime Dependencies
- **Runtime:** PHP 8.2.12 CLI (64-bit linux).
- **Core Framework:** Laravel 11.44.0 (skeleton `laravel/laravel:^11.0`).
- **Admin Panel:** Filament PHP v3.2.134 (`filament/filament:^3.2`).
- **API Authentication:** Laravel Sanctum 4.3 (`laravel/sanctum:^4.3`).
- **Reporting & Exports:** `maatwebsite/excel:^3.1.55` and `pxlrbt/filament-excel:^2.3`.
- **Database Engine (Development):** SQLite 3 with foreign key enforcement (`database/database.sqlite`).
- **Database Engine (Target Production):** MySQL 8.x configured in `config/database.php` with strict mode, `utf8mb4_unicode_ci` collation, and prefix indexing.
- **Node & Build Pipeline:** Node.js v20.x, Vite v7.3.0, Tailwind CSS v4.0.0.

### 2.2 Working-Tree & Git Status
- Active Branch: `main`
- Working-Tree State: Contains incremental enhancements across controllers, domain models, services, migrations, and test suites.
- No destructive unstaged changes or git reset operations performed.

---

## 3. Reconciliation Matrix: Batch A–G Claims vs Verified Reality

| Prior Claim / Sprint | Documented Item | Source Code / Evidence Path | Physical Verification Status | Discrepancies & Gotchas |
| :--- | :--- | :--- | :---: | :--- |
| **Batch A (P0 Security)** | API Docs credential leak | [api-docs/index.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/api-docs/index.blade.php) | **VERIFIED** | Credentials stripped; route requires auth & admin role. |
| | Google SSO bypass | [GoogleSsoController.php](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/GoogleSsoController.php) | **VERIFIED** | Fallback to `User::first()` removed; tests assert safe error redirect. |
| | H2H HMAC enforcement | [H2hBillingService.php](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Finance/H2hBillingService.php) | **VERIFIED** | Signature mandatory, 300-second replay drift enforced. |
| | Cashier cascade delete protection | [Pembayaran.php](file:///home/masgansss/Projects/Dev/web_spp/app/Models/Pembayaran.php#L68) | **VERIFIED** | `static::deleting` guards cashier deletion when transactions exist. |
| | Public PII masking on `/cek-tagihan` | [CekTagihanController.php](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/CekTagihanController.php) | **VERIFIED** | Exact NIM + DOB required; PII masked to UU PDP standards. |
| | Anti-IDOR printing guards | [SecureDocumentDownloadController.php](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/SecureDocumentDownloadController.php) | **VERIFIED** | Student ownership verified prior to rendering/downloading. |
| **Batch B (Academic Core)** | Dual-Domain Identity backfill | [User.php](file:///home/masgansss/Projects/Dev/web_spp/app/Models/User.php), `2026_10_13_090000...` | **VERIFIED** | Polymorphic identity resolved; 0 unlinked student users. |
| | Course prerequisite enforcement | [SmartKrsService.php](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/SmartKrsService.php#L170) | **VERIFIED** | Passing letter grade (minimum C) required. |
| | Dynamic SKS ceiling matrix | [SmartKrsService.php](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/SmartKrsService.php#L100) | **VERIFIED** | SN-Dikti IPS bracket matrix enforced during KRS submit. |
| | Timetable clash interval logic | [SmartKrsService.php](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/SmartKrsService.php#L225) | **VERIFIED** | Overlapping time intervals blocked (`start_a < end_b && end_a > start_b`). |
| | Grade deadline locks | [NilaiController.php](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/NilaiController.php#L78) | **VERIFIED** | Grade submission deadline lock enforced. |
| **Batch C (Financial Ledger)** | Hardcoded WhatsApp dummy numbers | [WhatsAppNotificationService.php](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Messaging/WhatsAppNotificationService.php) | **VERIFIED** | Fallback dummy number purged. |
| | Negative balance clamping | [SppBillingService.php](file:///home/masgansss/Projects/Dev/web_spp/app/Domain/Finance/Services/SppBillingService.php) | **VERIFIED** | Clamped with `max(0, $balance)`. |
| **Batch D (UI/UX)** | Desktop/Mobile navigation parity | [layouts/app.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php) | **VERIFIED** | 100% link parity between desktop and mobile drawer. |
| | Alert spam consolidation | [layouts/app.blade.php](file:///home/masgansss/Projects/Dev/web_spp/resources/views/layouts/app.blade.php) | **VERIFIED** | Single toast component with auto-dismiss. |
| **Batch E (Performance)** | Foreign key indexing | `2026_10_14_000004_add_indexes_to_foreign_keys.php` | **VERIFIED** | 79 foreign keys indexed across 34 migrations. |
| | Asynchronous EWS background scans | [RunEarlyWarningScan.php](file:///home/masgansss/Projects/Dev/web_spp/app/Console/Commands/RunEarlyWarningScan.php) | **VERIFIED** | Chunked jobs dispatched to queue. |
| **Batch F (Advanced Modules)** | E-Surat BAAK sync | [AcademicLetterService.php](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Document/AcademicLetterService.php) | **VERIFIED** | BAAK desk approval workflow intact. |
| | KKN >= 100 SKS threshold | [KknController.php](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/KknController.php#L45) | **VERIFIED** | Enforced and tested. |
| | DBPTA >= 110 SKS threshold | [DbptaProposalController.php](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/DbptaProposalController.php#L42) | **VERIFIED** | Enforced and tested. |
| **Batch G (Public Portals)** | Public diploma verification | [DiplomaVerificationService.php](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/DiplomaVerificationService.php) | **VERIFIED** | Cryptographic token lookup + rate limiter active. |
| | /cek-tagihan GET route & throttling | [CekTagihanController.php](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/CekTagihanController.php) | **VERIFIED** | Throttling and redirect targets verified. |
| | Legacy SPP Archival assessment | `docs/audit/15-legacy-spp-archival-assessment.md` | **VERIFIED** | Read-only audit complete; zero data truncated. |

---

## 4. Unverified Claims & Critical Observations

1. **Digital Signature Claim Integrity:**
   - Previous reports referenced electronic signatures on diplomas and academic letters.
   - Code inspection reveals that `DigitalSignatureService` generates a SHA-256 HMAC representation and internal QR verification token, but does **NOT** interface with a certified Certificate Authority (e.g. BSrE / Balai Sertifikasi Elektronik Kemkominfo / Peruri).
   - *Finding:* Claims of "legal BSrE electronic signature" must be strictly characterized as internal cryptographic hash verification until an external CA integration is contracted.

2. **Database Engine Differences:**
   - Automated tests run against SQLite in-memory / file driver (`database.sqlite`).
   - Production will deploy on MySQL 8.x.
   - All migrations use cross-compatible definitions (`foreignId()`, `string()`, `decimal()`, `boolean()`), but composite index limits in MySQL InnoDB (max 3072 bytes) and collation differences require ongoing vigilance.

---

## 5. Reconciliation Sign-Off
All 264 existing tests pass, 0 foreign-key violations exist, and assets compile cleanly. The baseline is solid and reconciled for Batch H execution.
