<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Prevent duplicate payments for identical student, month, and year
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->unique(['id_siswa', 'bulan_dibayar', 'tahun_dibayar'], 'unique_pembayaran_periode');
        });

        // 2. Prevent duplicate student attendance records for the exact same BAP lecture meeting
        Schema::table('presensi_mahasiswas', function (Blueprint $table) {
            $table->unique(['id_bap', 'id_mahasiswa'], 'unique_presensi_bap_mhs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropUnique('unique_pembayaran_periode');
        });

        Schema::table('presensi_mahasiswas', function (Blueprint $table) {
            $table->dropUnique('unique_presensi_bap_mhs');
        });
    }
};
