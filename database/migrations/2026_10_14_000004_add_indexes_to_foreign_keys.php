<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * List of unindexed foreign key targets across the application schema.
     */
    protected array $foreignKeyTargets = [
        ['siswas', 'id_spp'],
        ['siswas', 'id_kelas'],
        ['pembayarans', 'id_spp'],
        ['pembayarans', 'id_petugas'],
        ['activity_logs', 'user_id'],
        ['jadwal_pelajarans', 'id_guru'],
        ['jadwal_pelajarans', 'id_mapel'],
        ['nilais', 'id_guru'],
        ['nilais', 'id_mapel'],
        ['program_studis', 'id_fakultas'],
        ['kurikulums', 'id_prodi'],
        ['mata_kuliah_prasyarats', 'id_mk_prasyarat'],
        ['ruangans', 'id_gedung'],
        ['dosen_kelas', 'id_guru'],
        ['dosen_kelas', 'id_kelas_kuliah'],
        ['jadwal_kuliahs', 'id_guru'],
        ['jadwal_kuliahs', 'id_ruangan'],
        ['jadwal_kuliahs', 'id_kelas_kuliah'],
        ['mbkm_konversis', 'id_mk'],
        ['mbkm_konversis', 'id_siswa'],
        ['krs', 'id_dosen_wali'],
        ['krs', 'id_tahun_akademik'],
        ['krs_details', 'id_kelas_kuliah'],
        ['bap_perkuliahans', 'id_ruangan'],
        ['bap_perkuliahans', 'id_guru'],
        ['presensi_kuliahs', 'id_siswa'],
        ['edom_evaluasis', 'id_siswa'],
        ['edom_evaluasis', 'id_guru'],
        ['edom_evaluasi_items', 'id_edom_pertanyaan'],
        ['edom_evaluasi_items', 'id_edom_evaluasi'],
        ['tagihan_vas', 'id_bank_mitra'],
        ['tagihan_vas', 'id_tahun_akademik'],
        ['tagihan_va_items', 'id_tagihan_va'],
        ['transaksi_h2hs', 'id_tagihan_va'],
        ['financial_clearances', 'id_tahun_akademik'],
        ['pembimbing_skripsis', 'id_guru'],
        ['logbook_bimbingans', 'id_guru'],
        ['logbook_bimbingans', 'id_tugas_akhir'],
        ['sidang_skripsis', 'id_ruangan'],
        ['sidang_skripsis', 'id_tugas_akhir'],
        ['penilaian_sidangs', 'id_guru'],
        ['skpi_aktivitas', 'id_siswa'],
        ['cpmks', 'id_cpl'],
        ['nilai_obes', 'id_cpmk'],
        ['lms_sync_queues', 'id_kelas_kuliah'],
        ['employer_feedbacks', 'id_tracer_study'],
        ['booking_fasilitas', 'id_user'],
        ['booking_fasilitas', 'id_ruangan'],
        ['audit_trail_logs', 'id_user'],
        ['early_warning_logs', 'id_dosen_pa'],
        ['financial_discrepancies', 'id_reconciliation_log'],
        ['dosens', 'id_prodi'],
        ['ukts', 'id_prodi'],
        ['mahasiswas', 'id_ukt'],
        ['mahasiswas', 'id_dosen_pa'],
        ['mahasiswas', 'id_prodi'],
        ['tagihan_ukts', 'id_bank_mitra'],
        ['tagihan_ukts', 'id_tahun_akademik'],
        ['pembayaran_ukts', 'id_ukt'],
        ['pembayaran_ukts', 'id_tagihan_ukt'],
        ['pembayaran_ukts', 'id_user'],
        ['users', 'id_dosen'],
        ['users', 'id_mahasiswa'],
        ['users', 'id_guru'],
        ['users', 'id_siswa'],
        ['kelas_kuliahs', 'id_dosen'],
        ['kelas_kuliahs', 'id_tahun_akademik'],
        ['submissions', 'id_mahasiswa'],
        ['submissions', 'id_siswa'],
        ['submissions', 'graded_by_dosen'],
        ['mbkm_konversi_details', 'id_matakuliah'],
        ['facilities', 'id_ruangan'],
        ['facility_bookings', 'id_pemohon'],
        ['schedule_collisions', 'id_facility_booking'],
        ['kkn_registrasis', 'id_dosen_dpl'],
        ['kkn_registrasis', 'id_tahun_akademik'],
        ['surat_akademiks', 'id_siswa'],
        ['pendaftaran_beasiswas', 'id_siswa'],
        ['pendaftaran_beasiswas', 'id_beasiswa'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->foreignKeyTargets as [$table, $column]) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
                continue;
            }

            $indexName = "idx_{$table}_{$column}";

            try {
                Schema::table($table, function (Blueprint $t) use ($column, $indexName) {
                    $t->index($column, $indexName);
                });
            } catch (\Throwable $e) {
                // If index already exists or database driver ignores, safely continue
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->foreignKeyTargets as [$table, $column]) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $indexName = "idx_{$table}_{$column}";

            try {
                Schema::table($table, function (Blueprint $t) use ($indexName) {
                    $t->dropIndex($indexName);
                });
            } catch (\Throwable $e) {
                // Safely ignore if index does not exist
            }
        }
    }
};
