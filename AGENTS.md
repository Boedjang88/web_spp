# AGENTS.md — University SIAKAD Enterprise Engineering Guidelines

## 1. System Overview
**Sistem Informasi Akademik & Keuangan Terpadu (SIAKAD Enterprise & SPP)** is a mission-critical higher education university management system built on:
- **Backend:** PHP 8.2+ / 8.4 runtime with Laravel 11.x
- **Administration & Panels:** Filament PHP v3.2 (`/admin` Admin Panel and `/parent` Parent Portal)
- **Portals (Student, Lecturer, Public):** Laravel Blade Monolith with Vite-compiled Tailwind CSS & Alpine.js micro-interactions
- **API & Integrations:** Laravel Sanctum 4.3 Bearer Token REST API, Bank Host-to-Host (H2H) Webhooks with HMAC SHA256 signatures, Midtrans/Xendit payment gateways, PDDIKTI Feeder sync adapter
- **Database:** SQLite (local development: `database/database.sqlite`) / MySQL (production configured in `config/database.php`)
- **Queue & Async Jobs:** Database/Sync driver processing early warning scans, report exports, notifications, and batch invoices

---

## 2. Operating & Safety Boundaries
1. **Zero Destructive Operations:** Never drop tables, truncate financial ledgers (`pembayarans`, `pembayaran_ukts`), or wipe student transcripts (`nilais`, `krs`).
2. **Preserve Business Logic & Domain Bridging:**
   - The application bridges a legacy school SPP domain (`siswas`, `gurus`, `mapels`, `spps`, `pembayarans`) with a modern university SIAKAD domain (`mahasiswas`, `dosens`, `mata_kuliahs`, `ukts`, `pembayaran_ukts`).
   - Use [`App\Traits\ResolvesStudentUser`](file:///home/masgansss/Projects/Dev/web_spp/app/Traits/ResolvesStudentUser.php) and [`App\Http\Middleware\RoleMiddleware`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Middleware/RoleMiddleware.php) to resolve polymorphic identity. Never hardcode fallback students (e.g. `Siswa::first()`).
3. **Pessimistic Locking & Atomic Transactions:**
   - Always wrap state mutations in `DB::transaction(...)`.
   - Use `lockForUpdate()` for KRS class capacity reservations and invoice reconciliation to prevent race conditions.
4. **Anti-IDOR & Multi-Tenant Authorization:**
   - Always verify that the authenticated user owns the requested record (`$user->siswa?->id === $record->id_siswa` or admin/dosen override).
5. **UU PDP No. 27/2022 Compliance:**
   - Sensitive student PII (`nik`, `nama_ibu_kandung`, `no_hp_wali`) must use encrypted model casts.
   - Public invoice lookup (`/cek-tagihan`) requires exact NIM + DOB and rate limiting.

---

## 3. Standard Verification & Test Commands
- **Run Full Automated Test Suite:**
  ```bash
  ./vendor/bin/phpunit
  ```
- **Run Specific Regression Suites:**
  ```bash
  ./vendor/bin/phpunit tests/Feature/Security/SecurityVulnerabilityRegressionTest.php
  ./vendor/bin/phpunit tests/Feature/Academic/SmartKrsInvariantsTest.php
  ./vendor/bin/phpunit tests/Feature/Finance/FinancialLedgerRegressionTest.php
  ./vendor/bin/phpunit tests/Feature/Performance/PerformanceAndHardeningRegressionTest.php
  ```
- **Verify Database Foreign Key Integrity:**
  ```bash
  php artisan tinker --execute="dump(DB::select('PRAGMA foreign_key_check'));"
  ```
- **Compile Frontend Assets:**
  ```bash
  npm run build
  ```

---

## 4. Key Architectural Paths
- Routes: [`routes/web.php`](file:///home/masgansss/Projects/Dev/web_spp/routes/web.php), [`routes/api.php`](file:///home/masgansss/Projects/Dev/web_spp/routes/api.php)
- Controllers: [`app/Http/Controllers/Web/`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Web/), [`app/Http/Controllers/Api/`](file:///home/masgansss/Projects/Dev/web_spp/app/Http/Controllers/Api/)
- Filament Resources: [`app/Filament/Resources/`](file:///home/masgansss/Projects/Dev/web_spp/app/Filament/Resources/)
- Core Services: [`app/Services/Academic/`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Academic/), [`app/Services/Finance/`](file:///home/masgansss/Projects/Dev/web_spp/app/Services/Finance/)
- Documentation & Audits: [`docs/audit/`](file:///home/masgansss/Projects/Dev/web_spp/docs/audit/), [`docs/architecture/`](file:///home/masgansss/Projects/Dev/web_spp/docs/architecture/), [`docs/design/DESIGN_SYSTEM.md`](file:///home/masgansss/Projects/Dev/web_spp/docs/design/DESIGN_SYSTEM.md)
