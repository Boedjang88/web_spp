<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Schema for 1-to-N MBKM Credit Conversion Details.
     */
    public function up(): void
    {
        if (!Schema::hasTable('mbkm_konversi_details')) {
            Schema::create('mbkm_konversi_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_mbkm_konversi')->constrained('mbkm_konversis')->onDelete('cascade');
                $table->foreignId('id_matakuliah')->constrained('mata_kuliahs')->onDelete('cascade');
                $table->integer('sks_diakui')->default(2);
                $table->decimal('nilai_angka_konversi', 5, 2)->default(90.00);
                $table->string('nilai_huruf_konversi', 2)->default('A');
                $table->decimal('bobot_mutu', 3, 2)->default(4.00);
                $table->text('catatan_dosen_pa')->nullable();
                $table->timestamps();

                $table->index(['id_mbkm_konversi', 'id_matakuliah'], 'idx_mbkm_detail_perf');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mbkm_konversi_details');
    }
};
