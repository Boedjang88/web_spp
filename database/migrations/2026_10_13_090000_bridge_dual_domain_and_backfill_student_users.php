<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add grading window dates to tahun_akademiks if not already present
        if (Schema::hasTable('tahun_akademiks')) {
            Schema::table('tahun_akademiks', function (Blueprint $table) {
                if (!Schema::hasColumn('tahun_akademiks', 'tgl_nilai_mulai')) {
                    $table->date('tgl_nilai_mulai')->nullable()->after('tgl_krs_selesai');
                }
                if (!Schema::hasColumn('tahun_akademiks', 'tgl_nilai_selesai')) {
                    $table->date('tgl_nilai_selesai')->nullable()->after('tgl_nilai_mulai');
                }
            });
        }

        // 2. Deterministically backfill unlinked student user accounts
        if (Schema::hasTable('users') && Schema::hasTable('siswas') && Schema::hasTable('mahasiswas')) {
            $studentUsers = DB::table('users')
                ->whereIn('role', ['mahasiswa', 'siswa', 'student'])
                ->get();

            foreach ($studentUsers as $user) {
                $siswaId = $user->id_siswa;
                $mahasiswaId = $user->id_mahasiswa;

                // Attempt to match by name
                if (!$siswaId) {
                    $matchedSiswa = DB::table('siswas')->where('nama', $user->name)->first();
                    if ($matchedSiswa) {
                        $siswaId = $matchedSiswa->id;
                    }
                }

                if (!$mahasiswaId) {
                    $matchedMahasiswa = DB::table('mahasiswas')->where('nama', $user->name)->first();
                    if ($matchedMahasiswa) {
                        $mahasiswaId = $matchedMahasiswa->id;
                    }
                }

                // If still missing, cross-reference between Siswa and Mahasiswa by NIM / NISN
                if ($siswaId && !$mahasiswaId) {
                    $s = DB::table('siswas')->where('id', $siswaId)->first();
                    if ($s) {
                        $m = DB::table('mahasiswas')
                            ->where('nim', $s->nisn)
                            ->orWhere('nisn', $s->nisn)
                            ->orWhere('nama', $s->nama)
                            ->first();
                        if ($m) {
                            $mahasiswaId = $m->id;
                        }
                    }
                }

                if ($mahasiswaId && !$siswaId) {
                    $m = DB::table('mahasiswas')->where('id', $mahasiswaId)->first();
                    if ($m) {
                        $s = DB::table('siswas')
                            ->where('nisn', $m->nim)
                            ->orWhere('nisn', $m->nisn)
                            ->orWhere('nama', $m->nama)
                            ->first();
                        if ($s) {
                            $siswaId = $s->id;
                        }
                    }
                }

                // Update user if new linkages were resolved
                if ($siswaId !== $user->id_siswa || $mahasiswaId !== $user->id_mahasiswa) {
                    DB::table('users')->where('id', $user->id)->update([
                        'id_siswa' => $siswaId,
                        'id_mahasiswa' => $mahasiswaId,
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tahun_akademiks')) {
            Schema::table('tahun_akademiks', function (Blueprint $table) {
                if (Schema::hasColumn('tahun_akademiks', 'tgl_nilai_selesai')) {
                    $table->dropColumn('tgl_nilai_selesai');
                }
                if (Schema::hasColumn('tahun_akademiks', 'tgl_nilai_mulai')) {
                    $table->dropColumn('tgl_nilai_mulai');
                }
            });
        }
    }
};
