# AUDIT 31: MIGRATION COMPATIBILITY & DIALECT AUDIT
**SIAKAD Enterprise & SPP — Schema Evolution, Dialect Compatibility (SQLite/MySQL) & Reversibility**  
**Execution Date:** 2026-10-13  
**Status:** FULLY COMPATIBLE & AUDITED  
**Lead Engineer / Architect:** Antigravity Autonomous Lead Architect

---

## 1. Executive Summary

The database migration subsystem across **SIAKAD Enterprise & SPP** comprises over 40 structured migrations spanning initial legacy tables, enterprise university expansions, payment gateway tables, and concurrency/indexing enhancements.

This audit evaluates:
1. Chronological order and dependency resolution.
2. Cross-dialect compatibility between **SQLite** (local development and automated testing) and **MySQL / MariaDB** (production staging and live deployments).
3. Reversibility and rollback safety (`down()` methods).
4. Zero-destructive principles for existing production tables.

---

## 2. Chronological Pipeline & Dependency Ordering

Migrations follow strict temporal dependency ordering:
1. **Core Identity & Base Legacy:**
   - `0001_01_01_000000_create_users_table.php`
   - `2024_03_01_000001_create_kelas_table.php`
   - `2024_03_01_000002_create_spps_table.php`
   - `2024_03_01_000003_create_siswas_table.php`
   - `2024_03_01_000004_create_gurus_table.php`
   - `2024_03_01_000005_create_mapels_table.php`
   - `2024_03_01_000006_create_jadwals_table.php`
   - `2024_03_01_000007_create_pembayarans_table.php`
2. **University Academic Expansion:**
   - `2026_10_06_210000_create_enterprise_expansion_tables.php` (created `mahasiswas`, `dosens`, `fakultas`, `program_studis`, `mata_kuliahs`, `kurikulums`, `kelas_kuliahs`, `krs`, `nilais`, `tugas_akhirs`, `ukts`, `tagihan_ukts`, `pembayaran_ukts`).
3. **Enterprise Hardening & Integrations:**
   - `2026_10_11_160000_create_notifications_table.php`
   - `2026_10_11_170000_add_payment_gateway_fields_to_bank_mitras_table.php`
   - `2026_10_11_180000_add_digital_signature_and_telegram_fields.php`
   - `2026_10_11_190000_add_semester_and_sks_to_mapels_table.php`
   - `2026_10_13_080000_add_concurrency_and_integrity_constraints.php`
   - `2026_10_13_090000_bridge_dual_domain_and_backfill_student_users.php`
   - `2026_10_14_000004_add_indexes_to_foreign_keys.php`

**Dependency Invariant:** No migration attempts to alter or reference a table before its creation migration has executed.

---

## 3. SQLite vs MySQL Cross-Dialect Compatibility Review

| Architectural Feature | SQLite Behavior | MySQL 8.x Behavior | Compatibility Strategy Implemented |
| :--- | :--- | :--- | :--- |
| **Enum Types** | Emulated as `VARCHAR` with `CHECK` constraints | Native `ENUM(...)` | Standard Laravel `$table->string(...)` with PHP-backed enums and validation rules used for cross-engine stability. |
| **Boolean Types** | Stored as integer `0` or `1` | Stored as `TINYINT(1)` | Transparently cast via Eloquent `'boolean'` cast attributes. |
| **Foreign Key Modifiers** | Requires table recreation or modern SQLite 3.35+ `ALTER TABLE` | Native `ALTER TABLE ADD CONSTRAINT` | Migrations wrap column additions safely; testing suite executes with SQLite foreign key checks enabled. |
| **JSON Columns** | Stored as `TEXT` with JSON function support | Native binary `JSON` | Standard Laravel `$table->json(...)` and model `array`/`json` casts. |
| **UUIDs / ULIDs** | Stored as `CHAR(36)` | Stored as `CHAR(36)` | Standard Laravel `$table->uuid(...)` supported identically. |

---

## 4. Rollback & Reversibility Safety

All migration scripts maintain corresponding `down()` methods that reverse their mutations cleanly without leaving lingering schema artifacts:
- Index migrations drop indexes via `$table->dropIndex(...)`.
- Column additions drop columns via `$table->dropColumn(...)`.
- Table creations execute `Schema::dropIfExists(...)`.

### 4.1 Zero-Destructive Policy for Production
When altering existing production tables (e.g. `pembayarans`, `siswas`, `users`), migrations:
- **Never drop or truncate existing data.**
- Add nullable columns or provide sensible defaults.
- Backfill missing data asynchronously or in safe batch scripts (`2026_10_13_090000_bridge_dual_domain_and_backfill_student_users.php`).

---

## 5. Conclusion
The migration architecture is clean, deterministic, reversible, and fully compatible between SQLite development/testing and production MySQL deployment targets.
