<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Core Academic Integration (BAAK) & Facility Schema
     */
    public function up(): void
    {
        // 1. Fakultas
        Schema::create('fakultas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_fakultas', 20)->unique();
            $table->string('nama_fakultas', 150);
            $table->string('nama_fakultas_en', 150)->nullable();
            $table->string('dekan', 150)->nullable();
            $table->string('nip_dekan', 30)->nullable();
            $table->timestamps();
        });

        // 2. Program Studi (Prodi)
        Schema::create('program_studis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_fakultas')->constrained('fakultas')->cascadeOnDelete();
            $table->string('kode_prodi', 20)->unique();
            $table->string('nama_prodi', 150);
            $table->string('nama_prodi_en', 150)->nullable();
            $table->string('jenjang', 10)->default('S1'); // D3, D4, S1, S2, S3
            $table->string('akreditasi', 10)->default('Unggul'); // A, B, Unggul, Baik Sekali
            $table->string('kaprodi', 150)->nullable();
            $table->string('nip_kaprodi', 30)->nullable();
            $table->timestamps();
        });

        // 3. Tahun Akademik & Periode Semester
        Schema::create('tahun_akademiks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tahun', 20)->unique(); // e.g. 20251 (2025/2026 Ganjil)
            $table->string('nama_tahun', 50); // 2025/2026
            $table->enum('semester', ['Ganjil', 'Genap', 'Pendek'])->default('Ganjil');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->date('tgl_krs_mulai')->nullable();
            $table->date('tgl_krs_selesai')->nullable();
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
        });

        // 4. Kurikulum
        Schema::create('kurikulums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_prodi')->constrained('program_studis')->cascadeOnDelete();
            $table->string('nama_kurikulum', 100);
            $table->integer('tahun_mulai')->index();
            $table->integer('total_sks_lulus')->default(144);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Mata Kuliah
        Schema::create('mata_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kurikulum')->constrained('kurikulums')->cascadeOnDelete();
            $table->string('kode_mk', 20)->index();
            $table->string('nama_mk', 150)->index();
            $table->string('nama_mk_en', 150)->nullable();
            $table->integer('sks_teori')->default(2);
            $table->integer('sks_praktik')->default(0);
            $table->integer('sks_total')->default(2);
            $table->integer('semester_rekomendasi')->default(1);
            $table->enum('jenis_mk', ['Wajib Program Studi', 'Wajib Nasional', 'Pilihan', 'MBKM'])->default('Wajib Program Studi');
            $table->timestamps();

            $table->unique(['id_kurikulum', 'kode_mk']);
        });

        // 6. Mata Kuliah Prasyarat
        Schema::create('mata_kuliah_prasyarats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_mk')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->foreignId('id_mk_prasyarat')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->enum('nilai_minimum', ['A', 'B', 'C', 'D'])->default('D');
            $table->timestamps();

            $table->unique(['id_mk', 'id_mk_prasyarat']);
        });

        // 7. Gedung & Ruangan Fasilitas
        Schema::create('gedungs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_gedung', 20)->unique();
            $table->string('nama_gedung', 100);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });

        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_gedung')->constrained('gedungs')->cascadeOnDelete();
            $table->string('kode_ruangan', 20)->unique();
            $table->string('nama_ruangan', 100);
            $table->integer('kapasitas')->default(40);
            $table->enum('jenis_ruangan', ['Teori', 'Laboratorium', 'Auditorium', 'Studio'])->default('Teori');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('radius_meter')->default(50); // Geo-fencing radius
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 8. Kelas Kuliah
        Schema::create('kelas_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_mk')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->foreignId('id_tahun_akademik')->constrained('tahun_akademiks')->cascadeOnDelete();
            $table->string('nama_kelas', 10); // A, B, C, IF-44-01
            $table->integer('kuota_maksimal')->default(40);
            $table->integer('total_terisi')->default(0);
            $table->timestamps();

            $table->unique(['id_mk', 'id_tahun_akademik', 'nama_kelas']);
        });

        // 9. Dosen Pengajar Kelas & Tim Teaching
        Schema::create('dosen_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kelas_kuliah')->constrained('kelas_kuliahs')->cascadeOnDelete();
            $table->foreignId('id_guru')->constrained('gurus')->cascadeOnDelete(); // Lecturer
            $table->boolean('is_koordinator')->default(false);
            $table->integer('persentase_mengajar')->default(100);
            $table->timestamps();
        });

        // 10. Jadwal Kuliah & Alokasi Ruangan
        Schema::create('jadwal_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kelas_kuliah')->constrained('kelas_kuliahs')->cascadeOnDelete();
            $table->foreignId('id_ruangan')->constrained('ruangans')->cascadeOnDelete();
            $table->foreignId('id_guru')->constrained('gurus')->cascadeOnDelete(); // Primary lecturer
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'])->index();
            $table->time('jam_mulai')->index();
            $table->time('jam_selesai')->index();
            $table->timestamps();

            $table->index(['hari', 'jam_mulai', 'jam_selesai']);
        });

        // 11. MBKM Credit Conversion Matrix
        Schema::create('mbkm_konversis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete(); // Student
            $table->foreignId('id_mk')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->string('nama_program_eksternal', 200); // e.g. Magang MSIB di PT Google Indonesia
            $table->string('mitra_mbkm', 150);
            $table->integer('sks_diakui')->default(20);
            $table->decimal('nilai_angka', 5, 2);
            $table->string('nilai_huruf', 2);
            $table->enum('status_verifikasi', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending')->index();
            $table->string('pejabat_pengesah', 150)->nullable();
            $table->timestamp('tgl_pengesahan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mbkm_konversis');
        Schema::dropIfExists('jadwal_kuliahs');
        Schema::dropIfExists('dosen_kelas');
        Schema::dropIfExists('kelas_kuliahs');
        Schema::dropIfExists('ruangans');
        Schema::dropIfExists('gedungs');
        Schema::dropIfExists('mata_kuliah_prasyarats');
        Schema::dropIfExists('mata_kuliahs');
        Schema::dropIfExists('kurikulums');
        Schema::dropIfExists('tahun_akademiks');
        Schema::dropIfExists('program_studis');
        Schema::dropIfExists('fakultas');
    }
};
