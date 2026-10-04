<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Schema for Sarpras Facility Booking & Academic Schedule Collision Detection.
     */
    public function up(): void
    {
        if (!Schema::hasTable('facilities')) {
            Schema::create('facilities', function (Blueprint $table) {
                $table->id();
                $table->string('nama_fasilitas');
                $table->string('kode_fasilitas')->unique();
                $table->string('kategori')->default('Ruang Lab'); // Ruang Lab, Aula, Lapangan, Peralatan
                $table->foreignId('id_ruangan')->nullable()->constrained('ruangans')->onDelete('set null');
                $table->integer('kapasitas')->default(40);
                $table->enum('status_fasilitas', ['Tersedia', 'Perbaikan', 'Terkunci'])->default('Tersedia');
                $table->text('deskripsi')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('facility_bookings')) {
            Schema::create('facility_bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_facility')->constrained('facilities')->onDelete('cascade');
                $table->foreignId('id_pemohon')->constrained('users')->onDelete('cascade');
                $table->string('tujuan_penggunaan');
                $table->date('tanggal_pinjam');
                $table->time('jam_mulai');
                $table->time('jam_selesai');
                $table->enum('status_booking', ['PENDING', 'APPROVED', 'REJECTED', 'COLLISION_DETECTED'])->default('PENDING');
                $table->text('catatan_persetujuan')->nullable();
                $table->timestamps();

                $table->index(['id_facility', 'tanggal_pinjam', 'status_booking'], 'idx_facility_booking_perf');
            });
        }

        if (!Schema::hasTable('schedule_collisions')) {
            Schema::create('schedule_collisions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_facility_booking')->constrained('facility_bookings')->onDelete('cascade');
                $table->string('jenis_tabrakan'); // AKADEMIK_KULIAH, BOOKING_PARALEL
                $table->string('id_referensi_bentrok');
                $table->text('deskripsi_bentrok');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_collisions');
        Schema::dropIfExists('facility_bookings');
        Schema::dropIfExists('facilities');
    }
};
