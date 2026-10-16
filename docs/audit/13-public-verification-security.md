# Audit Report 13: Public Diploma Verification Security (`/verifikasi-ijazah/{token}`)

## 1. Executive Summary
In higher education enterprise systems, public diploma verification is a legal and regulatory requirement governed by **Permendikbud No. 59 Tahun 2018** (Penomoran Ijazah Nasional / PIN & SIVIL) and **UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)**. It enables prospective employers, civil service examiners (BKN), graduate admissions offices, and embassies to verify the authenticity of a degree without logging into the intranet portal.

Prior to Batch G, no public route existed for `/verifikasi-ijazah/{token}`, creating an architectural gap between graduation clearance (`GraduationClearanceService` & `WisudaController`) and public document verification. This audit documents the end-to-end security architecture, token generation mechanisms, authorized disclosure boundaries, and abuse mitigation controls.

---

## 2. Request Flow & Architectural Trace

### 2.1 Complete Request Lifecycle
```
[Client / Employer QR Scan]
            │
            ▼
    GET /verifikasi-ijazah/{token}
            │
            ├── Middleware: StrictRateLimitingMiddleware (30 req / 1 min)
            │      └── If exceeded ➔ 429 Too Many Requests + Retry-After
            │
            ▼
    DiplomaVerificationController::verify($token)
            │
            ├── Input Sanitization: trim, max 128 chars, alphanumeric check
            │
            ├── Token Resolution (DiplomaVerificationService):
            │      ├── Cache check: "diploma_v2_{hash(token|app.key)}" (1800s TTL)
            │      ├── Strategy A: Normalized PIN (PIN-YYYY-XXXXXX or PIN/YYYY/XXXXXX)
            │      ├── Strategy B: Cryptographic Token Hash: SHA-256("DIPLOMA|id|PIN|key")
            │      └── Strategy C: Linked Mahasiswa / Prodi entity resolution
            │
            ├── State Invariant Validation:
            │      └── Student status_kelulusan MUST BE in ['Lulus', 'Lulus Wisuda', 'LULUS']
            │          and nomor_ijazah must NOT be null.
            │          (Active, dropped out, or unregistered students return 404).
            │
            ├── Audit Trail Recording:
            │      └── AuditTrailService::record('PUBLIC_DIPLOMA_VERIFICATION', [
            │             'token_hash' => SHA256(token), 'is_valid' => bool
            │          ])
            │
            └── Response Dispatcher:
                   ├── Header Accept: application/json ➔ 200 OK sanitized JSON
                   ├── Browser Navigation ➔ 200 OK /view('siakad.diploma.verify')
                   └── Invalid / Non-existent ➔ 404 Not Found (generic, anti-enumeration)
```

### 2.2 Affected Files & Data Entities
- **Route:** `routes/web.php` (`Route::get('/verifikasi-ijazah/{token}', ...)`)
- **Controller:** `app/Http/Controllers/Web/DiplomaVerificationController.php`
- **Service:** `app/Services/Academic/DiplomaVerificationService.php`
- **Cryptographic Engine:** `app/Services/Document/DigitalSignatureService.php`
- **Audit System:** `app/Services/Audit/AuditTrailService.php`
- **View Template:** `resources/views/siakad/diploma/verify.blade.php`
- **Models & Tables:** `siswas`, `mahasiswas`, `kelas`, `prodis`, `audit_trail_logs`
- **Automated Regression Suite:** `tests/Feature/Security/PublicVerificationSecurityTest.php`

---

## 3. Token Security & Cryptographic Invariants

### 3.1 Token Generation & Unpredictability
Tokens are derived through keyed cryptographic digests:
$$\text{Token} = \text{HMAC-SHA256}(\text{"DIPLOMA|" } \parallel \text{id\_siswa} \parallel \text{"|" } \parallel \text{nomor\_ijazah}, \text{APP\_KEY})$$

| Metric | Property | Assessment |
| :--- | :--- | :--- |
| **Entropy** | 256 bits (64 hex characters) | Cryptographically secure against brute-force guessing |
| **Uniqueness** | Deterministic 1-to-1 mapping per student record | Collision probability negligible ($< 10^{-77}$) |
| **Storage Form** | Ephemeral computation backed by application key | No plaintext token stored in public tables; secret key bound |
| **URL Safety** | Alphanumeric hex string or normalized PIN | Supports QR encoding without URL-escaping defects |

### 3.2 Mitigation Against Token Enumeration
1. **Rate Limiting:** Guarded by `StrictRateLimitingMiddleware` with a strict limit of 30 requests per minute per source IP.
2. **Generic 404 Responses:** Both invalid tokens and active non-graduated student queries return identical generic 404 responses ("Data dokumen tidak ditemukan dalam basis data nasional").
3. **No Database ID Leakage:** Internal primary keys (`siswas.id`, `mahasiswas.id`) are never exposed in the URL, payload, or audit logs.
4. **Keyed Hash Log Labels:** Raw tokens are never stored in log labels; only SHA-256 token digests are recorded in `audit_trail_logs`.

---

## 4. UU PDP No. 27/2022 & Permendikbud Information Disclosure Boundaries

Under Indonesian Data Protection Law (**UU PDP No. 27/2022**) and Higher Education Standards, publishing sensitive personal identifiers to public endpoints without express consent constitutes an administrative and criminal violation.

### 4.1 Field Disclosure Matrix
| Field Name | Category | Public Disclosure Status | Rationale / Compliance Reference |
| :--- | :--- | :--- | :--- |
| **Nomor Ijazah (PIN)** | Academic Credential | **ALLOWED** | SIVIL Kemendikbudristek public verification standard |
| **Nama Lengkap** | Public Identity | **ALLOWED** | Name as printed on official degree certificate |
| **NIM / Student ID** | Institutional Identifier | **ALLOWED** | Official matriculation identifier |
| **Perguruan Tinggi** | Institutional Entity | **ALLOWED** | University name and accreditation scope |
| **Program Studi & Jenjang** | Academic Program | **ALLOWED** | Degree major and qualification level (KKNI Level 6) |
| **Tanggal Kelulusan** | Academic Milestone | **ALLOWED** | Official graduation date as approved in Yudisium |
| **TTE Digest Hash** | Cryptographic Seal | **ALLOWED** | BSrE BSSN electronic certification digest |
| **NIK (KTP)** | Sensitive PII | **STRICTLY FORBIDDEN** | UU PDP Art. 4(2) Specific Personal Data |
| **Nama Ibu Kandung** | Banking / Auth PII | **STRICTLY FORBIDDEN** | High financial fraud risk |
| **No. Telepon / HP** | Contact PII | **STRICTLY FORBIDDEN** | Anti-doxxing & spam protection |
| **Alamat / Domisili** | Location PII | **STRICTLY FORBIDDEN** | Personal privacy protection |
| **Nilai / IPK / Transkrip**| Confidential Academic | **STRICTLY FORBIDDEN** | Confidential student academic performance record |
| **Internal Server Paths**| Architecture Leak | **STRICTLY FORBIDDEN** | Anti-reconnaissance |

---

## 5. Abuse Resistance & Cache Safety

### 5.1 Multi-Layered Abuse Resistance
- **Source-Based Rate Limiting:** 30 requests per minute per IP via `StrictRateLimitingMiddleware`. Returns HTTP 429 with `Retry-After` header.
- **Bot Mitigation:** Generic responses prevent bot scrapers from inferring student roster sizes or serial gaps.
- **Fail-Closed Availability:** If the student record has missing clearance or status is revoked, verification immediately fails closed.

### 5.2 Cache Invariant Architecture
- **Cache Key:** `diploma_v2_{hash(token | app.key)}` prevents cross-tenant or cross-token cache contamination.
- **Cache TTL:** 1,800 seconds (30 minutes).
- **Sanitized Cache Content:** Only the sanitized presentation DTO is cached, completely isolated from internal Eloquent model state.

---

## 6. Verification Status & Test Evidence
Implemented in Batch G and verified via `tests/Feature/Security/PublicVerificationSecurityTest.php`:
- `test_diploma_verification_succeeds_for_graduated_student` (PASS)
- `test_diploma_verification_succeeds_via_normalized_pin` (PASS)
- `test_diploma_verification_json_excludes_sensitive_pii` (PASS)
- `test_diploma_verification_rejects_active_non_graduated_student` (PASS)
- `test_diploma_verification_returns_404_for_unknown_token` (PASS)
- `test_diploma_verification_enforces_rate_limiting` (PASS)
