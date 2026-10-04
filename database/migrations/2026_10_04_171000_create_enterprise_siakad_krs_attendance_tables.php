<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Smart KRS, Grading, EDOM, BAP & Geo-fenced QR Attendance
     */
    public function up(): void
    {
        // 1. Rencana Studi (KRS) Header
        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('id_tahun_akademik')->constrained('tahun_akademiks')->cascadeOnDelete();
            $table->foreignId('id_dosen_wali')->nullable()->constrained('gurus')->nullOnDelete();
            $table->integer('max_sks_diizinkan')->default(24);
            $table->integer('total_sks_diambil')->default(0);
            $table->decimal('ips_lalu', 4, 2)->default(0.00);
            $table->enum('status_krs', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak'])->default('Draft')->index();
            $table->text('catatan_pembimbing')->nullable();
            $table->timestamp('tgl_pengajuan')->nullable();
            $table->timestamp('tgl_persetujuan')->nullable();
            $table->timestamps();

            $table->unique(['id_siswa', 'id_tahun_akademik']);
        });

        // 2. Rencana Studi (KRS) Details & Enrollments
        Schema::create('krs_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_krs')->constrained('krs')->cascadeOnDelete();
            $table->foreignId('id_kelas_kuliah')->constrained('kelas_kuliahs')->cascadeOnDelete();
            $table->enum('status_ambil', ['Baru', 'Mengulang', 'Perbaikan'])->default('Baru');
            
            // Multi-Component Numeric Grades
            $table->decimal('nilai_kehadiran', 5, 2)->nullable();
            $table->decimal('nilai_tugas', 5, 2)->nullable();
            $table->decimal('nilai_quiz', 5, 2)->nullable();
            $table->decimal('nilai_uts', 5, 2)->nullable();
            $table->decimal('nilai_uas', 5, 2)->nullable();
            $table->decimal('nilai_praktikum', 5, 2)->nullable();
            
            // Final Aggregate Grade & GPA Point
            $table->decimal('nilai_akhir_angka', 5, 2)->nullable();
            $table->string('nilai_akhir_huruf', 2)->nullable(); // A, AB, B, BC, C, D, E
            $table->decimal('bobot_mutu', 3, 2)->nullable(); // 4.00, 3.50, 3.00, ...
            $table->boolean('is_lulus')->default(false)->index();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();

            $table->unique(['id_krs', 'id_kelas_kuliah']);
        });

        // 3. Bobot Penilaian Multi-Komponen Per Kelas Kuliah
        Schema::create('bobot_penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kelas_kuliah')->unique()->constrained('kelas_kuliahs')->cascadeOnDelete();
            $table->integer('bobot_kehadiran')->default(10);
            $table->integer('bobot_tugas')->default(20);
            $table->integer('bobot_quiz')->default(10);
            $table->integer('bobot_uts')->default(30);
            $table->integer('bobot_uas')->default(30);
            $table->integer('bobot_praktikum')->default(0);
            $table->timestamps();
        });

        // 4. Berita Acara Perkuliahan (Digital BAP)
        Schema::create('bap_perkuliahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kelas_kuliah')->constrained('kelas_kuliahs')->cascadeOnDelete();
            $table->foreignId('id_guru')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('id_ruangan')->nullable()->constrained('ruangans')->nullOnDelete();
            $table->integer('pertemuan_ke');
            $table->date('tanggal_pelaksanaan');
            $table->time('jam_mulai_real');
            $table->time('jam_selesai_real');
            $table->string('materi_pembahasan', 255);
            $table->text('catatan_dosen')->nullable();
            $table->integer('total_mahasiswa_hadir')->default(0);
            $table->integer('total_mahasiswa_absen')->default(0);
            $table->enum('status_verifikasi', ['Draft', 'Tersubmit', 'Diverifikasi BAAK'])->default('Tersubmit')->index();
            $table->string('digital_signature_hash', 64)->nullable();
            $table->timestamps();

            $table->unique(['id_kelas_kuliah', 'pertemuan_ke']);
        });

        // 5. Presensi Mahasiswa Berbasis QR & Geo-fence
        Schema::create('presensi_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bap')->constrained('bap_perkuliahans')->cascadeOnDelete();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete();
            $table->enum('status_kehadiran', ['Hadir', 'Izin', 'Sakit', 'Alpa'])->default('Alpa')->index();
            $table->decimal('submit_lat', 10, 7)->nullable();
            $table->decimal('submit_long', 10, 7)->nullable();
            $table->integer('jarak_meter_dari_ruangan')->nullable();
            $table->string('device_fingerprint', 100)->nullable();
            $table->timestamp('waktu_scan')->nullable();
            $table->timestamps();

            $table->unique(['id_bap', 'id_siswa']);
        });

        // 6. Evaluasi Dosen Oleh Mahasiswa (EDOM) Core
        Schema::create('edom_pertanyaans', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['Pedagogik', 'Profesional', 'Kepribadian', 'Sosial'])->default('Pedagogik');
            $table->text('teks_pertanyaan');
            $table->integer('urutan')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('edom_evaluasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_krs_detail')->constrained('krs_details')->cascadeOnDelete();
            $table->foreignId('id_guru')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete();
            $table->decimal('skor_rata_rata', 4, 2)->default(0.00);
            $table->text('kritik_saran')->nullable();
            $table->timestamps();

            $table->unique(['id_krs_detail', 'id_guru']);
        });

        Schema::create('edom_evaluasi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_edom_evaluasi')->constrained('edom_evaluasis')->cascadeOnDelete();
            $table->foreignId('id_edom_pertanyaan')->constrained('edom_pertanyaans')->cascadeOnDelete();
            $table->integer('skor_nilai'); // 1 (Sangat Buruk) s.d. 5 (Sangat Baik)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edom_evaluasi_items');
        Schema::dropIfExists('edom_evaluasis');
        Schema::dropIfExists('edom_pertanyaans');
        Schema::dropIfExists('presensi_kuliahs');
        Schema::dropIfExists('bap_perkuliahans');
        Schema::dropIfExists('bobot_penilaians');
        Schema::dropIfExists('krs_details');
        Schema::dropIfExists('krs');
    }
};
