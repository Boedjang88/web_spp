<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kkn_registrasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('id_tahun_akademik')->constrained('tahun_akademiks')->cascadeOnDelete();
            $table->foreignId('id_dosen_dpl')->nullable()->constrained('dosens')->nullOnDelete();
            $table->string('nama_kelompok', 100);
            $table->string('desa_lokasi', 150);
            $table->string('kecamatan', 100);
            $table->string('kabupaten', 100);
            $table->enum('status_pendaftaran', ['SUBMITTED', 'APPROVED', 'IN_PROGRESS', 'COMPLETED', 'REJECTED'])->default('SUBMITTED')->index();
            $table->string('file_laporan_path', 255)->nullable();
            $table->decimal('nilai_angka', 5, 2)->nullable();
            $table->string('nilai_huruf', 2)->nullable();
            $table->timestamps();

            $table->unique(['id_siswa', 'id_tahun_akademik']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kkn_registrasis');
    }
};
