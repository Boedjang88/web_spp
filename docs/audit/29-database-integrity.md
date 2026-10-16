# AUDIT 29: DATABASE INTEGRITY, CONSTRAINTS & RELATIONAL AUDIT
**SIAKAD Enterprise & SPP — Schema Architecture, Foreign Keys, Cascades & Integrity Invariants**  
**Execution Date:** 2026-10-13  
**Status:** VERIFIED & HARDENED  
**Lead Engineer / Architect:** Antigravity Autonomous Lead Architect

---

## 1. Executive Summary & Schema Topology

The **SIAKAD Enterprise & SPP** database architecture serves two unified domains:
1. **Legacy Primary/Secondary School Domain:**
   - Tables: `siswas`, `gurus`, `mapels`, `spps`, `pembayarans`.
   - Historical ledger and administrative baseline.
2. **Modern Higher Education / University Enterprise Domain:**
   - Academic: `mahasiswas`, `dosens`, `program_studis`, `fakultas`, `kurikulums`, `mata_kuliahs`, `kelas_kuliahs`, `krs`, `krs_details`, `nilais`, `tugas_akhirs`, `bimbingan_tas`, `surat_akademiks`.
   - Financial: `ukts`, `tagihan_ukts`, `pembayaran_ukts`, `bank_mitras`, `h2h_transactions`.
   - Identity & Authentication: `users`, `model_has_roles`, `sessions`, `personal_access_tokens`.

The dual domain is bridged polymorphicly and bi-directionally through `users.id_siswa`, `users.id_dosen`, and trait [`ResolvesStudentUser`](file:///home/masgansss/Projects/Dev/web_spp/app/Traits/ResolvesStudentUser.php).

---

## 2. Foreign Key Constraint Matrix & Cascade Policies

A strict foreign key audit was conducted across all 40+ migration files to evaluate referential integrity and prevent accidental orphaned records or destructive cascading deletes on financial records.

### 2.1 Critical Cascade Rules Catalog

| Parent Table | Child Table | Foreign Key Column | On Delete Action | Safety Assessment & Business Invariant |
| :--- | :--- | :--- | :--- | :--- |
| `mahasiswas` | `krs` | `id_mahasiswa` | `RESTRICT` / `NO ACTION` | Safe. Academic record cannot be accidentally purged. |
| `krs` | `krs_details` | `id_krs` | `CASCADE` | Safe. Line items deleted if and only if draft KRS is deleted. |
| `kelas_kuliahs`| `krs_details` | `id_kelas` | `RESTRICT` | Safe. Scheduled class offering cannot be deleted while students are enrolled. |
| `mahasiswas` | `tagihan_ukts` | `id_mahasiswa` | `RESTRICT` | **CRITICAL:** Financial invoice ledger is immutable; cannot be orphaned. |
| `tagihan_ukts` | `pembayaran_ukts`| `id_tagihan` | `RESTRICT` | **CRITICAL:** Payment receipts cannot be deleted or disconnected from invoice. |
| `siswas` | `pembayarans` | `id_siswa` | `RESTRICT` | **CRITICAL:** Legacy school SPP ledger cannot cascade delete payments. |
| `spps` | `siswas` | `id_spp` | `RESTRICT` | Safe. Tuition tier cannot be removed while assigned to active students. |
| `users` | `siswas` | `user_id` / linked | `SET NULL` / `RESTRICT` | Protects student academic profile if login user account is modified. |

---

## 3. SQLite Foreign Key Integrity Verification

### 3.1 Verification Execution
Database referential integrity is continuously asserted via SQLite `PRAGMA foreign_key_check` and dedicated automated regression suites:

```bash
php artisan tinker --execute="dump(DB::select('PRAGMA foreign_key_check'));"
```

### 3.2 Output & Evidence
```text
[]
```
- **Result:** Exactly zero referential violations found across all active tables and seeded production-like datasets.
- **Automated Regression Suite:** Test case `DatabaseIntegrityAndPerformanceTest::test_database_has_zero_foreign_key_violations` verifies this invariant deterministically.

---

## 4. Compound Uniqueness Constraints & Duplicate Prevention

To eliminate race conditions and double-entry corruption, compound uniqueness constraints and index protections are enforced:

1. **Course Enrollment Deduplication:**
   - `krs_details(id_krs, id_kelas)` is indexed with compound uniqueness, preventing a student from enrolling in the same class section multiple times within the same semester.
2. **National & University Identification Numbers:**
   - `siswas.nisn` and `siswas.nis`: Strict `UNIQUE` constraint.
   - `mahasiswas.nim`: Strict `UNIQUE` constraint.
3. **Student Identity Document Numbers (UU PDP):**
   - `siswas.nik`: Encrypted at rest via Laravel model casting with hashed lookup tokens.
4. **Financial Billing Identifiers:**
   - `tagihan_ukts.nomor_invoice`: Strict `UNIQUE` constraint.
   - `tagihan_ukts.nomor_va`: Strict `UNIQUE` constraint.
   - `h2h_transactions.transaction_ref`: Strict `UNIQUE` index preventing replay attacks.

---

## 5. Concurrency & Transactional Invariants

State mutations modifying class capacities and financial balances enforce pessimistic locking and atomic transactions:
- **Pessimistic Locking:** `DB::transaction()` wrapping `KelasKuliah::where(...)->lockForUpdate()->first()`.
- **Atomic Rollback Guarantee:** Verified by `DatabaseIntegrityAndPerformanceTest::test_transaction_rollback_reverts_class_capacity_on_failure`. If any downstream validation or persistence fails, class capacity reservations are restored instantly without partial commit anomalies.

---

## 6. Conclusion
The database schema exhibits zero orphaned records, zero foreign key violations, strict protection of historical financial ledgers, and robust concurrency control.
