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
        // 1. Data Guru / Tenaga Pendidik
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 20)->unique()->nullable();
            $table->string('nama_guru', 100);
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->string('no_telp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        // 2. Data Mata Pelajaran (Mapel)
        Schema::create('mapels', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mapel', 20)->unique();
            $table->string('nama_mapel', 100);
            $table->string('kelompok', 50)->default('Umum'); // Umum, Kejuruan, Muatan Lokal
            $table->integer('kkm')->default(75);
            $table->integer('semester')->default(1);
            $table->integer('semester_rekomendasi')->default(1);
            $table->timestamps();
        });

        // 3. Jadwal Pelajaran (Timetable)
        Schema::create('jadwal_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kelas')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('id_mapel')->constrained('mapels')->cascadeOnDelete();
            $table->foreignId('id_guru')->constrained('gurus')->cascadeOnDelete();
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('ruangan', 30)->default('Ruang Kelas');
            $table->timestamps();

            $table->index(['id_kelas', 'hari']);
        });

        // 4. Nilai Akademik & Rapor Siswa
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('id_mapel')->constrained('mapels')->cascadeOnDelete();
            $table->foreignId('id_guru')->nullable()->constrained('gurus')->nullOnDelete();
            $table->enum('semester', ['Ganjil', 'Genap'])->default('Ganjil');
            $table->string('tahun_ajaran', 10)->default('2025/2026');
            $table->decimal('nilai_tugas', 5, 2)->default(0);
            $table->decimal('nilai_uts', 5, 2)->default(0);
            $table->decimal('nilai_uas', 5, 2)->default(0);
            $table->decimal('nilai_akhir', 5, 2)->default(0);
            $table->string('predikat', 2)->default('C'); // A, B, C, D
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['id_siswa', 'id_mapel', 'semester', 'tahun_ajaran']);
        });

        // 5. Presensi / Kehadiran Siswa
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('id_kelas')->constrained('kelas')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpa'])->default('Hadir');
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->unique(['id_siswa', 'tanggal']);
            $table->index(['id_kelas', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
        Schema::dropIfExists('nilais');
        Schema::dropIfExists('jadwal_pelajarans');
        Schema::dropIfExists('mapels');
        Schema::dropIfExists('gurus');
    }
};
