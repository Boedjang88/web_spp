<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
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

    public function down(): void
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
                status varchar check (status in ('DRAFT', 'DISETUJUI', 'DITOLAK')) not null default 'DRAFT',
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
            DB::statement("ALTER TABLE surat_akademiks MODIFY COLUMN status ENUM('DRAFT', 'DISETUJUI', 'DITOLAK') NOT NULL DEFAULT 'DRAFT';");
        }
    }
};
