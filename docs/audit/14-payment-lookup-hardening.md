# Audit Report 14: Payment Receipt Lookup Hardening (`/cek-tagihan`)

## 1. Executive Summary
The `/cek-tagihan` endpoint provides a self-service billing lookup interface allowing students and guardians to check UKT/SPP payment status, arrears, and Virtual Account numbers.

An architectural investigation identified three critical vulnerabilities prior to Batch G:
1. **Route Method Gap & 405 Method Not Allowed:** Only `POST /cek-tagihan` was declared in `routes/web.php`. Directly visiting `/cek-tagihan` in a web browser returned a `405 Method Not Allowed` exception.
2. **Redirect Loop on Invalid Lookups:** When a search yielded no results, `CekTagihanController` redirected to `route('cek.index')`, which was bound to `'/'` (a redirect to `/login`). This redirected students to the authentication screen and dropped their error flash messages.
3. **Absence of Invalid Search Throttling & Raw Identifier Logs:** The endpoint was protected only by coarse IP rate-limiting (`strict.rate_limit:10,1`). An attacker could scan sequential NIMs without triggering repeated-failure locks, and raw query terms were logged in plaintext.

---

## 2. Request Flow & Vulnerability Analysis

### 2.1 Complete Flow Diagram
```
[User Browser]
      │
      ├── GET /cek-tagihan ➔ CekTagihanController::index() ➔ view('cek-tagihan') [HTTP 200]
      │
      └── POST /cek-tagihan [Input: 'nisn' = '0011223344']
            │
            ├── Validation: 'nisn' required|string|max:50
            │
            ├── Rate Limiter 1: StrictRateLimitingMiddleware (10 req / 1 min per IP)
            │      └── If exceeded ➔ 429 Too Many Requests
            │
            ├── Rate Limiter 2: Repeated Invalid Lookup Guard ('failed_cek_tagihan_{sha1(ip)}')
            │      └── If > 5 failures in 10 min ➔ 429 / Redirect back + Retry-After
            │
            ├── Database Lookup: Siswa::with(['kelas', 'spp', 'pembayarans.petugas'])
            │      └── Query bounded by indexed columns (nisn, nis)
            │
            ├── Privacy-Preserving Audit Trail:
            │      ├── search_key_hash: HMAC-SHA256(searchKey, app.key)
            │      ├── search_key_preview: '001***' (masked, never plaintext in logs)
            │      └── AuditTrailService::record('PUBLIC_PORTAL_CEK_TAGIHAN')
            │
            └── Result Handling:
                   ├── Found ➔ Reset failed limiter counter, render view('cek-tagihan', compact('siswa'))
                   └── Not Found ➔ Hit failed limiter, redirect back to route('cek.index') with flash error
```

### 2.2 Root Cause & Resolution
| Identified Vulnerability | Root Cause | Implemented Resolution | Verification Status |
| :--- | :--- | :--- | :--- |
| **GET /cek-tagihan 405 Error** | Missing `Route::get('/cek-tagihan')` in `routes/web.php` | Registered `Route::get('/cek-tagihan', [CekTagihanController::class, 'index'])->name('cek.index');` | Verified (HTTP 200 OK) |
| **Invalid Search Redirect to Login** | `route('cek.index')` was mapped to `'/'` which redirected to `login` | Mapped `cek.index` directly to `/cek-tagihan`, re-aliasing `'/'` to `home` | Verified (Redirects to `/cek-tagihan` with error) |
| **Automated NIM Enumeration** | No threshold on consecutive failed lookups | Added 5-attempt threshold in `RateLimiter` with 10-minute exponential decay | Verified (Throttled with Retry-After header) |
| **Plaintext PII in Audit Logs** | Raw search term stored in `audit_trail_logs.request_payload` | Keyed HMAC-SHA256 digest + 3-char masked preview (`001***`) | Verified (UU PDP Compliant) |

---

## 3. Rate-Limiting & Anti-Abuse Architecture

### 3.1 Defense-in-Depth Control Matrix
```
Layer 1: IP Source Rate Limiter (StrictRateLimitingMiddleware)
   └── Max 10 requests / 60 seconds
   └── Purpose: Prevents high-frequency HTTP traffic flooding & DoS

Layer 2: Enumeration Detection Rate Limiter (CekTagihanController)
   └── Max 5 failed lookups / 600 seconds (10 minutes) per IP
   └── Purpose: Detects and stops automated dictionary attacks scanning sequential NIMs

Layer 3: Privacy-Preserving Keyed Hashing
   └── Key: HMAC-SHA256(student_id, APP_KEY)
   └── Purpose: Prevents logging sensitive student identifiers in application traces
```

### 3.2 Resilience & Fail-Safe Invariants
- **Database Query Bounding:** Queries strictly use indexed unique attributes (`siswas.nisn` and `siswas.nis`), preventing table scans.
- **Fail-Open vs Fail-Closed Boundaries:** If cache or RateLimiter is degraded, lookup proceeds but limits log labels to keyed hashes.
- **Preservation of Legitimate Users:** Successful searches immediately execute `RateLimiter::clear($failedKey)`, ensuring legitimate users who make a single typo are not penalized.

---

## 4. Test Evidence & Regression Coverage
Tested in `tests/Feature/Security/PublicVerificationSecurityTest.php`:
1. `test_get_cek_tagihan_renders_public_portal`: Asserts `GET /cek-tagihan` returns HTTP 200 without throwing 405.
2. `test_cek_tagihan_masks_student_name_for_guest`: Asserts student name is masked (`B*** P*** L***`) for guests.
3. `test_cek_tagihan_invalid_nisn_redirects_to_search_portal`: Asserts failed lookups redirect back to the billing portal, not the login screen.
4. `test_cek_tagihan_records_keyed_hash_in_audit_trail`: Asserts audit trail does not store raw student identifiers in log labels.
5. `test_cek_tagihan_throttles_repeated_invalid_lookups`: Asserts 5 invalid attempts trigger rate-limiting with `Retry-After`.
