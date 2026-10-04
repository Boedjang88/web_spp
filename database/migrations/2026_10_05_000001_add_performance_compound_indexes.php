<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add compound indexes for high-concurrency peak query optimization.
     */
    public function up(): void
    {
        if (Schema::hasTable('presensi_mahasiswas')) {
            Schema::table('presensi_mahasiswas', function (Blueprint $table) {
                $table->index(['id_kelas_kuliah', 'id_mahasiswa', 'waktu_hadir'], 'idx_presensi_perf_compound');
                $table->index(['id_mahasiswa', 'status'], 'idx_presensi_mahasiswa_status');
            });
        }

        if (Schema::hasTable('krs_details')) {
            Schema::table('krs_details', function (Blueprint $table) {
                $table->index(['id_krs', 'id_kelas_kuliah'], 'idx_krs_details_perf');
            });
        }

        if (Schema::hasTable('submissions')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->index(['id_assignment', 'id_mahasiswa'], 'idx_submission_assignment_mahasiswa');
            });
        }

        if (Schema::hasTable('pembayaran_ukts')) {
            Schema::table('pembayaran_ukts', function (Blueprint $table) {
                $table->index(['id_mahasiswa', 'tanggal_pembayaran'], 'idx_pembayaran_ukt_perf');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('presensi_mahasiswas')) {
            Schema::table('presensi_mahasiswas', function (Blueprint $table) {
                $table->dropIndex('idx_presensi_perf_compound');
                $table->dropIndex('idx_presensi_mahasiswa_status');
            });
        }

        if (Schema::hasTable('krs_details')) {
            Schema::table('krs_details', function (Blueprint $table) {
                $table->dropIndex('idx_krs_details_perf');
            });
        }

        if (Schema::hasTable('submissions')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropIndex('idx_submission_assignment_mahasiswa');
            });
        }

        if (Schema::hasTable('pembayaran_ukts')) {
            Schema::table('pembayaran_ukts', function (Blueprint $table) {
                $table->dropIndex('idx_pembayaran_ukt_perf');
            });
        }
    }
};
