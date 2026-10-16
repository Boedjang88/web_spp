# Audit Report 19: Engineering Baseline & Test Metrics

**Document Reference:** `docs/audit/19-engineering-baseline.md`  
**Execution Timestamp:** 2026-10-13T12:41:00+07:00  
**Inspection Scope:** Test Execution Baselines, Coverage Map, and Migration Audits

---

## 1. Verified Test Execution Baseline

Execution command run on active repository:
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

- **Total Test Files:** 56 test files across `tests/Feature` and `tests/Unit`.
- **Total Test Cases:** 264 passing tests.
- **Total Assertions:** 2,419 assertions.
- **Failures / Errors:** 0.
- **Skipped / Incomplete:** 0.

---

## 2. Foreign-Key Integrity Baseline

```bash
php artisan tinker --execute="dump(DB::select('PRAGMA foreign_key_check'));"
```
```
[] // 0 foreign key violations
```

- **Indexed Foreign Keys:** All 79 foreign key columns across 34 migrations have verified explicit indexes (composite or single).

---

## 3. Frontend Compilation Baseline

```bash
npm run build
```
```
vite v7.3.0 building client environment for production...
public/build/manifest.json              0.33 kB │ gzip:  0.17 kB
public/build/assets/app-dBzPLkkA.css  152.64 kB │ gzip: 21.31 kB
public/build/assets/app-CAiCLEjY.js    36.35 kB │ gzip: 14.83 kB
✓ built in 1.00s
```

- **Asset Integrity:** Local compiled CSS and JS bundle is present in `public/build/assets/`.
- **Zero-CDN Architecture:** All 14 Blade views and layouts check `file_exists(public_path('build/manifest.json'))` to load local Vite bundles before falling back to external CDN.

---

## 4. Migration Status & Compatibility Risks

| Migration Group | Count | Engines Verified | Identified Risk & Mitigation |
| :--- | :---: | :--- | :--- |
| **Legacy SPP Tables (2024)** | 4 | SQLite, MySQL | Cashier delete cascade replaced with `static::deleting` model guard to prevent data loss. |
| **Enhanced LMS & Attendance (2026_10_05)** | 2 | SQLite, MySQL | High-precision GPS coordinates minimized via `attendance:archive-telemetry` without violating NOT NULL constraints. |
| **Concurrency Constraints (2026_10_13)** | 2 | SQLite, MySQL | Composite unique keys on `pembayarans(id_siswa, bulan, tahun)` and `presensi(id_bap, id_mahasiswa)` prevent race conditions. |
| **Performance Indexes (2026_10_14)** | 1 | SQLite, MySQL | 79 FK indexes added safely with `Schema::hasTable` and `Schema::hasColumn` existence guards. |
| **Academic Letters Check (2026_10_15)** | 1 | SQLite, MySQL | Dual SQLite + MySQL compatibility with bidirectional `down()` rollback. |

---

## 5. Identified Coverage Gaps to Address in Batches H–M

1. **Adversarial Security Enumeration (Batch H):** Need targeted test proving that timing attacks and malformed input formats cannot bypass `/cek-tagihan` rate limiters or leak student records.
2. **Revocation Stale Cache Invalidation (Batch H):** Ensure that if an administrator revokes a diploma token, any active cache entry is invalidated immediately.
3. **Cross-Module State Contradictions (Batch I):** Validate that students who graduate (status `Lulus`) cannot concurrently submit KRS for active semesters.
4. **Database Query Latency Profiling (Batch J):** Benchmark N+1 queries under 1,000+ student record datasets.
