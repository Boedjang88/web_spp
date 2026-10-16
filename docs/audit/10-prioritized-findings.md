# PHASE 9 AUDIT REPORT: CONSOLIDATED PRIORITIZED FINDINGS & DEFECT BACKLOG
**Sistem Informasi Akademik & Keuangan Terpadu (SIAKAD Enterprise & SPP)**
**Document Reference:** `docs/audit/10-prioritized-findings.md`  
**Execution Timestamp:** 2026-10-13T07:10:00+07:00  
**Audit Team:** Senior Software Architect, Security Engineer, Database Architect, Full-Stack Lead  
**Audit Source Range:** Synthesis of Phases 0 through 8 (Repository Inventory, Business Logic, Workflows, Security, Database, UI/UX, APIs, Performance, Testing)

---

## 1. Executive Summary of Consolidated Findings

Across the 8 investigative audit phases, **38 distinct defects and architectural risks** were identified and categorized using the standard 4-tier severity matrix:
- **P0 (Critical Blockers): 7 Defects** — Security bypasses, financial ledger corruption, unauthenticated account takeover, public PII leakage, and cascading transaction deletions.
- **P1 (High Severity): 11 Defects** — Broken business invariants, missing database constraints, race conditions under concurrency, dual-domain FK divergence, and universal student identity spoofing.
- **P2 (Medium Severity): 13 Defects** — Desktop vs mobile navigation asymmetry, unbounded memory loads (`Siswa::all()`), unindexed foreign keys, alert spamming, and missing circuit breakers.
- **P3 (Low Polish / Debt): 7 Defects** — CDN runtime overhead, inconsistent button tokens, dead CSS classes, and missing automated regression suites.

```
Total Defects Identified: 38
[P0: 7 Critical]  ███████
[P1: 11 High]     ███████████
[P2: 13 Medium]   █████████████
[P3: 7 Low]       ███████
```

---

## 2. P0: Critical Security, Integrity & Financial Blockers

These items pose immediate risks of data corruption, system takeover, financial discrepancy, or severe regulatory non-compliance (Indonesian UU PDP No. 27/2022). **They must be remediated in Phase A before any new features are deployed.**

| Finding ID | Domain & Component | File & Code Reference | Root Cause & Failure Mode | Impact & Severity |
| :--- | :--- | :--- | :--- | :--- |
| **P0-01** | **Security / Auth**<br>API Documentation | `resources/views/api-docs/index.blade.php#L371`<br>`routes/web.php#L53` | Public unauthenticated `/api/docs` route embeds hardcoded Superadmin credentials (`admin@siakad.ac.id` / `password123`) with a 1-click JavaScript auto-login that issues a real Sanctum token. | **CRITICAL (P0):** Any anonymous visitor can gain permanent Superadmin API access to the university database. |
| **P0-02** | **Security / Auth**<br>Google OAuth SSO | `app/Http/Controllers/Auth/GoogleSsoController.php#L33-L64`<br>`tests/Feature/GoogleSsoTest.php#L20-L23` | If Google OAuth credentials are unconfigured or fail, the controller executes a fallback that authenticates the user as `User::where('role', 'mahasiswa')->first() ?? User::first()`. | **CRITICAL (P0):** Visiting `/auth/google` logs in as Superadmin if no student exists, or as Student #1. Enshrined in tests. |
| **P0-03** | **Security / Finance**<br>H2H Bank Webhook | `app/Services/Finance/H2hBillingService.php#L137, L189, L283` | Webhook HMAC verification is conditional: `if ($request->has('signature'))`. If the attacker omits the signature field entirely, signature verification is bypassed. | **CRITICAL (P0):** Malicious actors can forge fake payment callbacks and mark tuition bills as paid without transferring funds. |
| **P0-04** | **Data Integrity**<br>Cashier Cascade Delete | `database/migrations/2024_10_04_000003_create_pembayarans_table.php#L19` | Foreign key definition: `$table->foreignId('id_petugas')->constrained('users')->onDelete('cascade')`. Deleting a cashier user silently deletes all financial receipts issued by them. | **CRITICAL (P0):** Permanent destruction of university audit trail and financial ledger upon staff offboarding. |
| **P0-05** | **Security / Privacy**<br>Public PII Leakage | `app/Http/Controllers/CekTagihanController.php#L38-L43`<br>`app/Http/Controllers/PortalController.php#L13` | Public unauthenticated route `/cek-tagihan` accepts partial name substrings and returns complete student NIK, phone number, address, unpaid tuition, and grades without authentication or consent. | **CRITICAL (P0):** Direct violation of Indonesian Personal Data Protection Law (UU PDP No. 27/2022). Substantial legal exposure. |
| **P0-06** | **Security / IDOR**<br>Document Printing | `app/Http/Controllers/NilaiController.php#L159`<br>`app/Http/Controllers/PembayaranController.php#L182` | `GET /web/nilai/khs/{id}` and `GET /web/pembayaran/{id}/cetak` take an arbitrary student/payment ID with zero ownership checks, allowing any student to print any peer's official KHS and receipts. | **CRITICAL (P0):** Cross-student grade and financial privacy breach via simple URL tampering. |
| **P0-07** | **Security / Auth**<br>Perpetual API Tokens | `app/Http/Controllers/Api/AuthApiController.php#L48` | Sanctum tokens are issued via `createToken('api-token')` with `expires_at = null`. No revocation on password change, session termination, or role change. | **CRITICAL (P0):** Stolen or compromised mobile API tokens remain active indefinitely. |

---

## 3. P1: High Severity Architecture & Business Logic Defects

These defects result in broken academic invariants, invalid state transitions, race conditions under concurrent operations, or silent identity spoofing.

| Finding ID | Domain & Component | File & Code Reference | Root Cause & Failure Mode | Impact & Severity |
| :--- | :--- | :--- | :--- | :--- |
| **P1-01** | **Identity / Multi-Tenancy**<br>Universal Fallback | 14 Web Controllers (e.g. `PortalController#L13`, `DbptaController#L24`, `EsuratController#L19`) | Code pattern `$siswa = Auth::user()->siswa ?? Siswa::first()`. When an admin, lecturer, or unlinked student accesses student pages, they act as student ID 1. | **HIGH (P1):** Unlinked accounts silently create thesis submissions, graduation requests, and surat applications on behalf of Student #1. |
| **P1-02** | **Database / Architecture**<br>Dual-Domain FK Split | `siswas` vs `mahasiswas` table schemas (10 tables use `id_siswa`, 3 use `id_mahasiswa`) | Dual-domain architecture divergence: SPP module references `siswas`, higher-ed modules reference `mahasiswas`. 20 student accounts have NULL foreign keys in both tables. | **HIGH (P1):** Inconsistent student entity references; reports and queries fail to join student profiles across modules. |
| **P1-03** | **Academic / Smart KRS**<br>Prerequisite Bypass | `app/Services/Academic/SmartKrsService.php#L88` | Course prerequisite validation checks only if the student has taken the course, but fails to check if the student achieved a passing grade (e.g. minimum C). | **HIGH (P1):** Students failing introductory courses are allowed to enroll in advanced courses. |
| **P1-04** | **Academic / Smart KRS**<br>Unenforced SKS Cap | `app/Http/Controllers/PortalController.php#L72` | Hardcoded maximum SKS cap of 24 is applied to all students regardless of previous semester GPA (IPS). | **HIGH (P1):** Students with IPS < 2.00 take 24 SKS instead of the regulated maximum of 18 SKS, causing academic overload. |
| **P1-05** | **Academic / Smart KRS**<br>Schedule Clash Bypass | `app/Services/Academic/SmartKrsService.php#L142` | Class timetable conflict check only validates exact start-time equality (`jam_mulai = jam_mulai`) rather than overlapping intervals (`start_a < end_b && end_a > start_b`). | **HIGH (P1):** Students are successfully enrolled in overlapping classes (e.g., 08:00–10:30 and 09:00–11:30). |
| **P1-06** | **Finance / Concurrency**<br>Duplicate Payment Race | `database/migrations/2024_10_04_000003_create_pembayarans_table.php` | Missing composite unique index on `(id_siswa, bulan, tahun)`. Concurrent duplicate requests from teller or webhooks record multiple payments for the same month. | **HIGH (P1):** Double-charging and corrupt ledger balances. |
| **P1-07** | **Academic / Presensi**<br>Duplicate Attendance Race | `database/migrations/2026_10_04_000001_create_presensi_mahasiswas_table.php` | Missing composite unique constraint on `(id_bap, id_mahasiswa)`. Rapid QR scans or network retries insert multiple attendance rows for the same meeting. | **HIGH (P1):** Attendance percentages calculated above 100%, skewing exam clearance calculations. |
| **P1-08** | **Academic / Grading**<br>Unguarded Grade Edits | `app/Http/Controllers/NilaiController.php#L78` | Teachers can update grades at any time, even after the semester grading period has closed and KHS has been published, without an audit log or approval. | **HIGH (P1):** Retroactive unauthorized grade inflation without academic committee review. |
| **P1-09** | **Integration / Webhooks**<br>Missing Timestamp Replay | `app/Http/Controllers/Api/H2hWebhookController.php#L45` | Idempotency key is checked in cache, but no check is performed on webhook timestamp drift (e.g. rejecting requests older than 300 seconds). | **HIGH (P1):** Replay attacks possible if attacker captures previous payload with different idempotency header. |
| **P1-10** | **Database / Transactions**<br>Missing Atomic Rollback | `app/Http/Controllers/KrsController.php#L95` | KRS detail approval and classroom capacity decrement run across multiple SQL queries outside a `DB::transaction()` block. | **HIGH (P1):** Partial failures result in approved KRS records without capacity decrement or vice versa. |
| **P1-11** | **Testing / Quality**<br>False Positive Suites | `tests/Feature/GoogleSsoTest.php` | Existing tests assert the bypass vulnerability as successful behavior instead of asserting that unconfigured SSO aborts safely. | **HIGH (P1):** CI passes green while critical security holes exist in production code. |

---

## 4. P2: Medium Severity Usability, Performance & Reliability Issues

These items degrade user experience, cause memory spikes under load, create UI inconsistencies, or create maintenance headaches.

| Finding ID | Domain & Component | File & Code Reference | Root Cause & Failure Mode | Impact & Severity |
| :--- | :--- | :--- | :--- | :--- |
| **P2-01** | **Performance / DB**<br>Unbounded Memory Loads | `app/Http/Controllers/PembayaranController.php#L32`<br>`app/Http/Controllers/NilaiController.php#L45`<br>`app/Http/Controllers/UserController.php#L38` | Direct execution of `Siswa::get()` or `Siswa::all()` in controller index/create actions without pagination or chunking. | **MEDIUM (P2):** Memory exhaustion (`Allowed memory size exhausted`) when student count exceeds 1,000. |
| **P2-02** | **Performance / DB**<br>Unindexed Foreign Keys | 64 Foreign Key columns across 34 migrations | Foreign key columns (`id_siswa`, `id_kelas_kuliah`, `id_prodi`, `id_tahun_akademik`) lack explicit indexes in migration files. | **MEDIUM (P2):** Table scans during joins and foreign key cascade checks, degrading query latency. |
| **P2-03** | **Performance / EWS**<br>Synchronous Analysis Loop | `app/Console/Commands/RunEarlyWarningScan.php#L42` | Command loops through all students synchronously executing 4 queries per student (GPA, attendance, debt, credits) in a single thread. | **MEDIUM (P2):** Cron execution exceeds PHP timeout (300s) on production databases; blocking process. |
| **P2-04** | **Performance / Frontend**<br>Play CDN Runtime Overhead | `resources/views/layouts/app.blade.php#L12` | Layout loads Tailwind Play CDN (`<script src="https://cdn.tailwindcss.com"></script>`) instead of Vite compiled assets. | **MEDIUM (P2):** Heavy client-side JS runtime compilation (150KB+ script, 200ms+ FCP delay) on mobile networks. |
| **P2-05** | **UI/UX / Navigation**<br>Desktop vs Mobile Parity | `resources/views/layouts/app.blade.php#L85-L160`<br>`resources/views/layouts/navigation.blade.php` | Desktop sidebar contains LMS, EDOM, and Facility Booking but lacks Thesis, KKN, and Wisuda. Mobile drawer contains Thesis, KKN, and Wisuda but lacks LMS, EDOM, and Booking. | **MEDIUM (P2):** Students cannot access core features depending on whether they use a phone or a laptop. |
| **P2-06** | **UI/UX / Feedback**<br>Triple Alert Spamming | `resources/views/layouts/app.blade.php#L210-L245` | Success session flash message triggers 3 distinct UI elements simultaneously: a top banner, a centered flash box, and a bottom toast notification. | **MEDIUM (P2):** Visual clutter, screen real estate degradation, and disruptive user experience. |
| **P2-07** | **UI/UX / Accessibility**<br>WCAG Contrast Failures | `resources/views/portal/krs.blade.php#L54` | Table headers and secondary badges use `#94A3B8` on white background (contrast ratio 2.6:1, failing WCAG 2.1 AA 4.5:1 requirement). | **MEDIUM (P2):** Text unreadable for visually impaired students and in bright sunlight conditions. |
| **P2-08** | **Integration / WhatsApp**<br>Hardcoded Phone Fallback | `app/Services/Notification/WhatsAppService.php#L68` | If a student's phone number is missing, the service falls back to sending financial notifications to hardcoded dummy number `081234567890`. | **MEDIUM (P2):** Real tuition billing notifications sent to an unknown third party. |
| **P2-09** | **Integration / Health**<br>Server Diagnostic Leak | `app/Http/Controllers/Api/HealthCheckController.php#L35` | `/api/health` is unauthenticated and returns exact PHP version, Laravel version, internal disk paths, and server memory limits. | **MEDIUM (P2):** Reconnaissance information disclosure to potential attackers. |
| **P2-10** | **Academic / BAP**<br>Unenforced 16 Meetings | `app/Http/Controllers/Dosen/BapController.php#L60` | Lecturers can finalize classes and open final exams even if fewer than 16 meetings (minimum standard per Permendikbud) were held. | **MEDIUM (P2):** Academic compliance violation regarding contact hours. |
| **P2-11** | **Finance / Invoicing**<br>Negative Balance Calculation | `app/Services/Finance/SppBillingService.php#L112` | Overpayment or manual credit adjustments result in negative balance entries that break invoice PDF rendering. | **MEDIUM (P2):** Kwitansi PDF generation crashes on negative balance values. |
| **P2-12** | **Architecture / Cache**<br>Uncached Dashboard Aggregates | `app/Http/Controllers/DashboardController.php#L22` | Admin dashboard runs live `COUNT(*)` and `SUM(jumlah_bayar)` queries on every page load with 0 TTL caching. | **MEDIUM (P2):** Database contention and slow response times during peak morning hours. |
| **P2-13** | **API / Error Format**<br>Inconsistent Error Envelopes | `app/Http/Controllers/Api/*` | Web controllers return HTML redirects, some API controllers return `{error: string}`, others return `{success: false, message: string, errors: []}`. | **MEDIUM (P2):** Mobile app crashes due to unexpected response JSON structure on 422/500 errors. |

---

## 5. P3: Low Polish, Minor Debt & Formatting Issues

| Finding ID | Domain & Component | File Reference | Root Cause | Impact |
| :--- | :--- | :--- | :--- | :--- |
| **P3-01** | UI Polish | `resources/views/components/button.blade.php` | Mismatched button corner radius (`rounded-lg` vs `rounded-full` across views). | Visual inconsistency. |
| **P3-02** | CSS Optimization | `resources/css/app.css` | Unused legacy CSS rules from initial template. | Minor asset bloat. |
| **P3-03** | Documentation | `README.md` | Incomplete setup instructions for H2H webhook mock testing. | Developer onboarding friction. |
| **P3-04** | Error Logging | `app/Exceptions/Handler.php` | Missing structured context (user ID, route name) in default log format. | Slower triage in production. |
| **P3-05** | Form UX | `resources/views/mahasiswa/create.blade.php` | Missing autofocus on the first input field (`nisn`). | Minor ergonomic deficiency. |
| **P3-06** | Blade Components | `resources/views/components/badge.blade.php` | Hardcoded Indonesian color strings without dark mode support. | Visual glitch in dark mode. |
| **P3-07** | Test Boilerplate | `tests/Feature/ExampleTest.php` | Unused default Laravel example test file. | Code cleanliness. |

---

## 6. Defect-to-Remediation Traceability Matrix

Every defect above is mapped to the phased reengineering rollout defined in `docs/audit/12-implementation-roadmap.md`:
- **Phase A (Stabilization & Hotfixes):** P0-01, P0-02, P0-03, P0-04, P0-05, P0-06, P0-07, P1-01, P1-06, P1-07, P1-11.
- **Phase B (Academic Core):** P1-02, P1-03, P1-04, P1-05, P1-08, P1-10, P2-10.
- **Phase C (Financial Ledger):** P1-09, P2-08, P2-11.
- **Phase D (UI/UX Overhaul):** P2-04, P2-05, P2-06, P2-07, P3-01, P3-05, P3-06.
- **Phase E (Performance & Hardening):** P2-01, P2-02, P2-03, P2-09, P2-12, P2-13, P3-02, P3-04.
