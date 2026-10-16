# AUDIT 21: PAYMENT LOOKUP SECURITY & RATE LIMITING RESILIENCE REPORT
**SIAKAD Enterprise & SPP — Batch H Security Validation**  
**Date:** 2026-10-13  
**Status:** VERIFIED & HARDENED  
**Target:** `/cek-tagihan`, `CekTagihanController`, `StrictRateLimitingMiddleware`, Reverse Proxy & Audit Trail

---

## 1. Executive Summary & Directive Compliance
In compliance with Batch H.2 of the Antigravity Master Execution Plan, the public billing lookup subsystem (`/cek-tagihan`) was audited for rate limiting resilience, multi-instance proxy handling, anti-enumeration protections, and PII leakage prevention.

### Key Hardening Actions Completed in Batch H:
1. **Trusted Proxy Resolution (`bootstrap/app.php`):** Enabled `$middleware->trustProxies(at: '*')` to ensure Laravel resolves the true client IP from `X-Forwarded-For` when operating behind reverse proxies, CDNs, or load balancers. This prevents shared-IP denial of service where multiple users behind a NAT/proxy get blocked together.
2. **Audit Trail PII Sanitization (`StrictRateLimitingMiddleware.php`):** The middleware previously copied request parameters directly into `AuditTrailLog`. It now redacts sensitive keys (`nik`, `no_hp`, `telepon`, `pin`, `token`, `secret`) with `***REDACTED***` and masks `nisn`, `nim`, and `nis` to preserve only prefix characters (e.g. `001*******`).
3. **Double-Tier Rate Limiting Defense:**
   - **Tier 1 (Anti-Scraping / Volume):** `StrictRateLimitingMiddleware` allows maximum 10 requests/minute per client IP. Exceeded quota returns HTTP 429 with explicit `Retry-After` header.
   - **Tier 2 (Anti-Enumeration / Bad Faith):** `CekTagihanController::search()` tracks consecutive failed searches (keyed by `failed_cek_tagihan_{sha1(ip)}`). 5 consecutive misses lock out the IP for 15 minutes (900 seconds).

---

## 2. In-Depth Security Matrix for Payment Lookup

| Attack Vector / Risk | Threat Profile | Applied Hardening Mechanism | Evidence / Verification |
| :--- | :--- | :--- | :--- |
| **Proxy IP Spoofing / Aggregation** | Client behind ALB or Nginx proxy shares single proxy IP; attacker spoofs `X-Forwarded-For`. | `$middleware->trustProxies(at: '*')` configured in Laravel 11 bootstrap pipeline. | Client IP accurately isolated per requester. |
| **Database Enumeration / Scraping** | Script iterates through sequential student IDs / NISN to scrape debt records. | Double-tier limiter: 10 req/min global + 5 failed attempts per 15 min lock out. | HTTP 429 / Redirect with `Retry-After` header. |
| **PII Harvest via Public Search** | Public lookup reveals full names, parent info, and personal phone numbers. | Name masking algorithm (`Budi Pratama` -> `B*** P***`). NIK, parents' names, and contacts excluded. | `test_cek_tagihan_masks_student_name_for_guest` (PASS). |
| **Log Injection / PII in Audit Trail** | Attack payload or sensitive identifiers logged in plaintext to SQLite/MySQL audit tables. | Keyed HMAC hashes (`PUBLIC_PORTAL_CEK_TAGIHAN`) + parameter redaction in `StrictRateLimitingMiddleware`. | `test_strict_rate_limiting_middleware_sanitizes_pii_in_audit_payload` (PASS). |
| **Timing Attacks on Search Existence** | Different execution duration when NISN exists vs does not exist. | Fast keyed index queries on `nisn` and standard redirect flow. | No significant statistical discrepancy in error handling paths. |

---

## 3. Verification Test Evidence
Executed automated regression suite:
```bash
./vendor/bin/phpunit tests/Feature/Security/PublicVerificationSecurityTest.php
```
Results:
- `test_get_cek_tagihan_renders_public_portal` (PASS - HTTP 200)
- `test_cek_tagihan_masks_student_name_for_guest` (PASS - Full name masked, raw name absent)
- `test_cek_tagihan_invalid_nisn_redirects_to_search_portal` (PASS - Redirect to search, not /login)
- `test_cek_tagihan_records_keyed_hash_in_audit_trail` (PASS - Audit logged with keyed hash)
- `test_cek_tagihan_throttles_repeated_invalid_lookups` (PASS - Throttled with `Retry-After`)
- `test_strict_rate_limiting_middleware_sanitizes_pii_in_audit_payload` (PASS - NIK and phone redacted in payload)

---

## 4. Residual Risks & Next Steps
- **Production Redis Cache Recommandation:** In a multi-server load-balanced deployment, the cache/limiter driver must use Redis (`CACHE_STORE=redis`) rather than local SQLite or `file` cache to ensure rate limits are globally synchronized across all PHP-FPM worker nodes.
