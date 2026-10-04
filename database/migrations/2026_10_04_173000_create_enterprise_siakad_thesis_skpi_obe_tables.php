<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tugas Akhir/Skripsi, Yudisium, SKPI (SACS), and OBE Curriculum Matrix
     */
    public function up(): void
    {
        // 1. Tugas Akhir / Skripsi
        Schema::create('tugas_akhirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->unique()->constrained('siswas')->cascadeOnDelete();
            $table->string('judul_skripsi', 255)->index();
            $table->string('judul_skripsi_en', 255)->nullable();
            $table->text('abstrak_id')->nullable();
            $table->text('abstrak_en')->nullable();
            $table->decimal('skor_plagiarisme_persen', 5, 2)->default(0.00);
            $table->string('file_proposal_path')->nullable();
            $table->string('file_naskah_akhir_path')->nullable();
            $table->enum('status_skripsi', [
                'Pengajuan Proposal', 'Seminar Proposal', 'Revisi Proposal',
                'Bimbingan', 'Seminar Hasil', 'Sidang Meja Hijau', 'Lulus Yudisium'
            ])->default('Pengajuan Proposal')->index();
            $table->date('tgl_lulus_sidang')->nullable();
            $table->timestamps();
        });

        // 2. Dosen Pembimbing & Penguji Skripsi
        Schema::create('pembimbing_skripsis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tugas_akhir')->constrained('tugas_akhirs')->cascadeOnDelete();
            $table->foreignId('id_guru')->constrained('gurus')->cascadeOnDelete();
            $table->enum('peran', ['Pembimbing Utama', 'Pembimbing Pendamping', 'Ketua Penguji', 'Anggota Penguji'])->default('Pembimbing Utama');
            $table->integer('urutan')->default(1);
            $table->timestamps();

            $table->unique(['id_tugas_akhir', 'id_guru', 'peran']);
        });

        // 3. Digital Logbook Bimbingan Skripsi
        Schema::create('logbook_bimbingans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tugas_akhir')->constrained('tugas_akhirs')->cascadeOnDelete();
            $table->foreignId('id_guru')->constrained('gurus')->cascadeOnDelete();
            $table->date('tanggal_bimbingan');
            $table->text('catatan_kemajuan_mahasiswa');
            $table->text('arahan_dosen_pembimbing')->nullable();
            $table->string('file_lampiran_draft')->nullable();
            $table->enum('status_acc', ['Pending', 'Disetujui', 'Perlu Revisi'])->default('Pending')->index();
            $table->timestamp('tgl_disetujui')->nullable();
            $table->timestamps();
        });

        // 4. Sidang Skripsi & Digital Rubric Examination Board
        Schema::create('sidang_skripsis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tugas_akhir')->constrained('tugas_akhirs')->cascadeOnDelete();
            $table->foreignId('id_ruangan')->nullable()->constrained('ruangans')->nullOnDelete();
            $table->enum('jenis_sidang', ['Seminar Proposal', 'Seminar Hasil', 'Sidang Akhir'])->default('Sidang Akhir');
            $table->dateTime('waktu_sidang');
            $table->decimal('nilai_rata_rata', 5, 2)->nullable();
            $table->string('nilai_huruf', 2)->nullable();
            $table->enum('hasil_keputusan', ['Lulus Tanpa Revisi', 'Lulus Dengan Revisi', 'Mengulang Sidang', 'Belum Sidang'])->default('Belum Sidang');
            $table->text('catatan_revisi_sidang')->nullable();
            $table->date('batas_waktu_revisi')->nullable();
            $table->timestamps();
        });

        Schema::create('penilaian_sidangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sidang')->constrained('sidang_skripsis')->cascadeOnDelete();
            $table->foreignId('id_guru')->constrained('gurus')->cascadeOnDelete(); // Dosen Penguji
            $table->decimal('skor_presentasi', 5, 2)->default(0); // Bobot 20%
            $table->decimal('skor_penguasaan_materi', 5, 2)->default(0); // Bobot 40%
            $table->decimal('skor_metodologi_karya', 5, 2)->default(0); // Bobot 40%
            $table->decimal('skor_total_penguji', 5, 2)->default(0);
            $table->text('catatan_penguji')->nullable();
            $table->timestamps();

            $table->unique(['id_sidang', 'id_guru']);
        });

        // 5. SKPI (Surat Keterangan Pendamping Ijazah) & SACS Credit Point
        Schema::create('skpi_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete();
            $table->enum('kategori', [
                'Prestasi & Kompetisi', 'Organisasi & Kepemimpinan',
                'Sertifikasi Keahlian', 'Pengabdian Masyarakat', 'Karya Ilmiah / HKI'
            ])->index();
            $table->string('nama_kegiatan_id', 255);
            $table->string('nama_kegiatan_en', 255);
            $table->string('penyelenggara', 150);
            $table->integer('tahun_kegiatan');
            $table->integer('poin_sacs')->default(5);
            $table->string('file_bukti_sertifikat')->nullable();
            $table->enum('status_verifikasi', ['Draft', 'Diajukan', 'Disetujui Kaprodi', 'Disahkan Dekan', 'Ditolak'])->default('Draft')->index();
            $table->string('pejabat_verifikator', 150)->nullable();
            $table->timestamp('tgl_verifikasi')->nullable();
            $table->timestamps();
        });

        // 6. OBE (Outcome-Based Education): CPL & CPMK Relational Matrix
        Schema::create('cpls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_prodi')->constrained('program_studis')->cascadeOnDelete();
            $table->string('kode_cpl', 20)->index(); // CPL-01, CPL-02
            $table->enum('aspek', ['Sikap', 'Pengetahuan', 'Keterampilan Umum', 'Keterampilan Khusus'])->default('Pengetahuan');
            $table->text('deskripsi_cpl_id');
            $table->text('deskripsi_cpl_en')->nullable();
            $table->timestamps();

            $table->unique(['id_prodi', 'kode_cpl']);
        });

        Schema::create('cpmks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_mk')->constrained('mata_kuliahs')->cascadeOnDelete();
            $table->foreignId('id_cpl')->constrained('cpls')->cascadeOnDelete();
            $table->string('kode_cpmk', 20)->index(); // CPMK-01, CPMK-02
            $table->text('deskripsi_cpmk_id');
            $table->text('deskripsi_cpmk_en')->nullable();
            $table->integer('bobot_persentase')->default(25); // Target weight in Course
            $table->timestamps();

            $table->unique(['id_mk', 'kode_cpmk']);
        });

        Schema::create('nilai_obes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_krs_detail')->constrained('krs_details')->cascadeOnDelete();
            $table->foreignId('id_cpmk')->constrained('cpmks')->cascadeOnDelete();
            $table->decimal('skor_pencapaian', 5, 2)->default(0.00); // 0.00 - 100.00
            $table->boolean('is_terpenuhi')->default(false); // >= KKM
            $table->timestamps();

            $table->unique(['id_krs_detail', 'id_cpmk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_obes');
        Schema::dropIfExists('cpmks');
        Schema::dropIfExists('cpls');
        Schema::dropIfExists('skpi_aktivitas');
        Schema::dropIfExists('penilaian_sidangs');
        Schema::dropIfExists('sidang_skripsis');
        Schema::dropIfExists('logbook_bimbingans');
        Schema::dropIfExists('pembimbing_skripsis');
        Schema::dropIfExists('tugas_akhirs');
    }
};
