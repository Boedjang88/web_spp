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
        // 1. Presensi Mahasiswa Table
        if (!Schema::hasTable('presensi_mahasiswas')) {
            Schema::create('presensi_mahasiswas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_mahasiswa')->constrained('mahasiswas')->cascadeOnDelete();
                $table->foreignId('id_kelas_kuliah')->constrained('kelas_kuliahs')->cascadeOnDelete();
                $table->foreignId('id_bap')->nullable()->constrained('bap_perkuliahans')->nullOnDelete();
                $table->dateTime('waktu_hadir');
                $table->decimal('latitude', 10, 7);
                $table->decimal('longitude', 10, 7);
                $table->enum('status', ['Hadir', 'Alpa', 'Izin', 'Sakit'])->default('Hadir')->index();
                $table->string('device_fingerprint', 100)->nullable();
                $table->dateTime('verified_at')->nullable();
                $table->timestamps();

                $table->index(['id_mahasiswa', 'id_kelas_kuliah']);
            });
        }

        // 2. Enhance Kelas Kuliah table
        Schema::table('kelas_kuliahs', function (Blueprint $table) {
            if (!Schema::hasColumn('kelas_kuliahs', 'id_dosen')) {
                $table->foreignId('id_dosen')->nullable()->constrained('dosens')->nullOnDelete()->after('id_mk');
            }
            if (!Schema::hasColumn('kelas_kuliahs', 'ruang')) {
                $table->string('ruang', 100)->nullable()->after('nama_kelas');
            }
            if (!Schema::hasColumn('kelas_kuliahs', 'hari')) {
                $table->string('hari', 20)->default('Senin')->after('ruang');
            }
            if (!Schema::hasColumn('kelas_kuliahs', 'jam_mulai')) {
                $table->time('jam_mulai')->default('08:00:00')->after('hari');
            }
            if (!Schema::hasColumn('kelas_kuliahs', 'jam_selesai')) {
                $table->time('jam_selesai')->default('10:30:00')->after('jam_mulai');
            }
        });

        // 3. Enhance Assignments table
        Schema::table('assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('assignments', 'file_path')) {
                $table->string('file_path')->nullable()->after('attachment_path');
            }
            if (!Schema::hasColumn('assignments', 'deadline')) {
                $table->dateTime('deadline')->nullable()->after('deadline_at');
            }
            if (!Schema::hasColumn('assignments', 'bobot_nilai_bap')) {
                $table->decimal('bobot_nilai_bap', 5, 2)->default(20.00)->after('bobot_persen');
            }
        });

        // 4. Enhance Submissions table
        Schema::table('submissions', function (Blueprint $table) {
            if (!Schema::hasColumn('submissions', 'id_mahasiswa')) {
                $table->foreignId('id_mahasiswa')->nullable()->constrained('mahasiswas')->cascadeOnDelete()->after('id_assignment');
            }
            if (!Schema::hasColumn('submissions', 'catatan_dosen')) {
                $table->text('catatan_dosen')->nullable()->after('feedback');
            }
            if (!Schema::hasColumn('submissions', 'hash_receipt')) {
                $table->string('hash_receipt', 64)->nullable()->after('submission_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'hash_receipt')) {
                $table->dropColumn('hash_receipt');
            }
            if (Schema::hasColumn('submissions', 'catatan_dosen')) {
                $table->dropColumn('catatan_dosen');
            }
            if (Schema::hasColumn('submissions', 'id_mahasiswa')) {
                $table->dropForeign(['id_mahasiswa']);
                $table->dropColumn('id_mahasiswa');
            }
        });

        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'bobot_nilai_bap')) {
                $table->dropColumn('bobot_nilai_bap');
            }
            if (Schema::hasColumn('assignments', 'deadline')) {
                $table->dropColumn('deadline');
            }
            if (Schema::hasColumn('assignments', 'file_path')) {
                $table->dropColumn('file_path');
            }
        });

        Schema::table('kelas_kuliahs', function (Blueprint $table) {
            if (Schema::hasColumn('kelas_kuliahs', 'id_dosen')) {
                $table->dropForeign(['id_dosen']);
                $table->dropColumn(['id_dosen', 'ruang', 'hari', 'jam_mulai', 'jam_selesai']);
            }
        });

        Schema::dropIfExists('presensi_mahasiswas');
    }
};
