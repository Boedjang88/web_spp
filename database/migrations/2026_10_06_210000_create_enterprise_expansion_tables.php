<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. E-Surat Akademik (Active Student Certificate & Official Docs)
        Schema::create('surat_akademiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete();
            $table->string('jenis_surat', 100)->default('Surat Keterangan Mahasiswa Aktif');
            $table->string('nomor_surat', 100)->unique();
            $table->string('perihal', 255)->default('Surat Keterangan Mahasiswa Aktif');
            $table->text('keperluan')->nullable();
            $table->string('qr_verification_token', 64)->unique();
            $table->string('file_pdf_path')->nullable();
            $table->enum('status', ['DRAFT', 'DISETUJUI', 'DITOLAK'])->default('DISETUJUI');
            $table->timestamp('tgl_terbit')->useCurrent();
            $table->timestamps();
        });

        // 2. Program Beasiswa & KIP-Kuliah
        Schema::create('beasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_beasiswa', 150);
            $table->string('penyelenggara', 150); // e.g. Kemendikbudristek, Yayasan, Internal Kampus
            $table->enum('jenis_cakupan', ['FULL', 'PARSIAL', 'NOMINAL'])->default('FULL');
            $table->decimal('persentase_potongan', 5, 2)->default(100.00); // 100% for KIP-K
            $table->decimal('nominal_potongan', 12, 2)->default(0.00);
            $table->integer('kuota')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Pendaftaran & Penerima Beasiswa Mahasiswa
        Schema::create('pendaftaran_beasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_beasiswa')->constrained('beasiswas')->cascadeOnDelete();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete();
            $table->enum('status_pengajuan', ['PENGAJUAN', 'DIVERIFIKASI', 'DISETUJUI', 'DITOLAK'])->default('DISETUJUI');
            $table->decimal('ipk_terakhir', 3, 2)->default(3.00);
            $table->timestamp('tgl_pengajuan')->useCurrent();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_beasiswas');
        Schema::dropIfExists('beasiswas');
        Schema::dropIfExists('surat_akademiks');
    }
};
