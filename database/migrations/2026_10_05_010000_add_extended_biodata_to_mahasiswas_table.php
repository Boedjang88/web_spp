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
        Schema::table('mahasiswas', function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswas', 'tempat_lahir')) {
                $table->string('tempat_lahir', 100)->nullable()->after('nama');
            }
            if (!Schema::hasColumn('mahasiswas', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }
            if (!Schema::hasColumn('mahasiswas', 'jenis_kelamin')) {
                $table->enum('jenis_kelamin', ['L', 'P'])->default('L')->after('tanggal_lahir');
            }
            if (!Schema::hasColumn('mahasiswas', 'agama')) {
                $table->string('agama', 50)->default('Islam')->after('jenis_kelamin');
            }
            if (!Schema::hasColumn('mahasiswas', 'email_pribadi')) {
                $table->string('email_pribadi', 100)->nullable()->after('no_telp');
            }
            if (!Schema::hasColumn('mahasiswas', 'rt')) {
                $table->string('rt', 10)->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('mahasiswas', 'rw')) {
                $table->string('rw', 10)->nullable()->after('rt');
            }
            if (!Schema::hasColumn('mahasiswas', 'kelurahan')) {
                $table->string('kelurahan', 100)->nullable()->after('rw');
            }
            if (!Schema::hasColumn('mahasiswas', 'kecamatan')) {
                $table->string('kecamatan', 100)->nullable()->after('kelurahan');
            }
            if (!Schema::hasColumn('mahasiswas', 'kota')) {
                $table->string('kota', 100)->nullable()->after('kecamatan');
            }
            if (!Schema::hasColumn('mahasiswas', 'kode_pos')) {
                $table->string('kode_pos', 10)->nullable()->after('kota');
            }
            if (!Schema::hasColumn('mahasiswas', 'nama_ayah')) {
                $table->string('nama_ayah', 150)->nullable()->after('nama_ibu_kandung');
            }
            if (!Schema::hasColumn('mahasiswas', 'pekerjaan_ayah')) {
                $table->string('pekerjaan_ayah', 100)->nullable()->after('nama_ayah');
            }
            if (!Schema::hasColumn('mahasiswas', 'pekerjaan_ibu')) {
                $table->string('pekerjaan_ibu', 100)->nullable()->after('pekerjaan_ayah');
            }
            if (!Schema::hasColumn('mahasiswas', 'penghasilan_ortu')) {
                $table->string('penghasilan_ortu', 100)->nullable()->after('pekerjaan_ibu');
            }
            if (!Schema::hasColumn('mahasiswas', 'asal_sekolah')) {
                $table->string('asal_sekolah', 150)->nullable()->after('penghasilan_ortu');
            }
            if (!Schema::hasColumn('mahasiswas', 'tahun_lulus_sekolah')) {
                $table->string('tahun_lulus_sekolah', 10)->nullable()->after('asal_sekolah');
            }
            if (!Schema::hasColumn('mahasiswas', 'nomor_ijazah_sekolah')) {
                $table->string('nomor_ijazah_sekolah', 100)->nullable()->after('tahun_lulus_sekolah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropColumn([
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'agama',
                'email_pribadi',
                'rt',
                'rw',
                'kelurahan',
                'kecamatan',
                'kota',
                'kode_pos',
                'nama_ayah',
                'pekerjaan_ayah',
                'pekerjaan_ibu',
                'penghasilan_ortu',
                'asal_sekolah',
                'tahun_lulus_sekolah',
                'nomor_ijazah_sekolah',
            ]);
        });
    }
};
