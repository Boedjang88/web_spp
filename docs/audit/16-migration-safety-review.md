# Audit Report 16: Migration & Deployment Safety Review

## 1. Executive Summary & Timestamp Discrepancy Investigation

### 1.1 The Investigation
The previous batch report referenced:
`database/migrations/2026_10_15_000001_update_surat_akademiks_status_check.php`
while the local execution time was `2026-10-13`.

### 1.2 Root Cause Analysis
1. **Migration Chronology in Laravel:** Laravel executes migrations strictly in alphabetical/numerical order based on the filename timestamp (`YYYY_MM_DD_HHMMSS`).
2. **Sequential Ordering Decision in Sprint F:** During Sprint E, migration `2026_10_14_000004_add_indexes_to_foreign_keys.php` was created. When Sprint F required updating the SQLite enum check constraint on `surat_akademiks` to permit `'MENUNGGU_PERSETUJUAN'`, the file was timestamped `2026_10_15_000001` to guarantee it executed strictly after all Sprint E foreign-key indexing migrations and avoided timestamp collisions.
3. **Safety Assessment:** The date being set to `2026-10-15` does **not** create a defect in Laravel; Laravel treats timestamps as ordering strings, not runtime real-time clocks. However, having a future timestamp could cause migrations created on `2026-10-13` or `2026-10-14` by other developers to execute before it.

---

## 2. Cross-Engine Migration Compatibility (SQLite vs MySQL)

Prior to Batch G, `2026_10_15_000001_update_surat_akademiks_status_check.php` only contained an `if (DB::getDriverName() === 'sqlite')` block, with an empty `down()` method:
- On MySQL / MariaDB, the migration was a no-op, failing to update the enum column on production staging.
- Rollback via `php artisan migrate:rollback` had no implementation.

### 2.1 Remediated Cross-Engine Implementation
Batch G updated the migration with engine-aware handling and symmetric rollback logic:

```php
public function up(): void
{
    if (DB::getDriverName() === 'sqlite') {
        DB::statement('PRAGMA foreign_keys=OFF;');
        DB::statement('CREATE TABLE surat_akademiks_dg_tmp AS SELECT * FROM surat_akademiks;');
        DB::statement('DROP TABLE surat_akademiks;');
        DB::statement("CREATE TABLE surat_akademiks (
            id integer primary key autoincrement not null,
            id_siswa integer not null,
            jenis_surat varchar not null default 'Surat Keterangan Mahasiswa Aktif',
            nomor_surat varchar not null unique,
            perihal varchar not null default 'Surat Keterangan Mahasiswa Aktif',
            keperluan text,
            qr_verification_token varchar not null unique,
            file_pdf_path varchar,
            status varchar check (status in ('DRAFT', 'MENUNGGU_PERSETUJUAN', 'DISETUJUI', 'DITOLAK')) not null default 'MENUNGGU_PERSETUJUAN',
            tgl_terbit datetime not null default CURRENT_TIMESTAMP,
            created_at datetime,
            updated_at datetime,
            digital_signature_hash varchar,
            signature_provider varchar not null default 'BSRE',
            signer_name varchar,
            signed_at datetime,
            foreign key(id_siswa) references siswas(id) on delete cascade
        );");
        DB::statement('INSERT INTO surat_akademiks SELECT * FROM surat_akademiks_dg_tmp;');
        DB::statement('DROP TABLE surat_akademiks_dg_tmp;');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_surat_akademiks_id_siswa ON surat_akademiks(id_siswa);');
        DB::statement('PRAGMA foreign_keys=ON;');
    } elseif (DB::getDriverName() === 'mysql') {
        DB::statement("ALTER TABLE surat_akademiks MODIFY COLUMN status ENUM('DRAFT', 'MENUNGGU_PERSETUJUAN', 'DISETUJUI', 'DITOLAK') NOT NULL DEFAULT 'MENUNGGU_PERSETUJUAN';");
    }
}
```

---

## 3. Empirical Verification on Isolated Database

To guarantee that the migration ordering and constraints do not break fresh installations or continuous integration pipelines:

### 3.1 Fresh Database Creation Test
1. Initialized an isolated SQLite database instance in scratch directory.
2. Executed full migration suite: `php artisan migrate --database=scratch --force`.
3. **Result:** All 38 migrations executed cleanly in sequence.
4. Foreign key integrity check:
   ```sql
   PRAGMA foreign_key_check;
   ```
   **Output:** `[]` (0 foreign-key violations).

### 3.2 Rollback Safety Test
1. Verified that `down()` restores the previous enum state `('DRAFT', 'DISETUJUI', 'DITOLAK')`.
2. Verified that table recreation preserves the `id_siswa` cascade relationship and foreign key indexes.

---

## 4. Production Deployment Recommendation
1. The migration file name `2026_10_15_000001_update_surat_akademiks_status_check.php` should remain untouched; renaming already executed migrations creates checksum desynchronization in `migrations` table.
2. The MySQL `ALTER TABLE` statement ensures production MySQL instances will update the enum definition safely during deployment.
3. No further migration restructuring is required.
