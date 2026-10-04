<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Synchronization (PDDIKTI/LMS), Tracer Study, Facility Booking & Immutable Audit Logs
     */
    public function up(): void
    {
        // 1. PDDIKTI Feeder Sync Architecture
        Schema::create('pddikti_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tipe_entitas', 50); // Mahasiswa, Dosen, KelasKuliah, Nilai, AKM
            $table->string('id_entitas_lokal', 50)->index();
            $table->string('id_feeder_pddikti', 100)->nullable()->index();
            $table->enum('status_sync', ['PENDING', 'SUCCESS', 'FAILED'])->default('PENDING')->index();
            $table->json('payload_terkirim')->nullable();
            $table->json('response_feeder')->nullable();
            $table->text('pesan_error')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

        // 2. LMS (Moodle / Canvas) Event-Driven Synchronization Queue
        Schema::create('lms_sync_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kelas_kuliah')->constrained('kelas_kuliahs')->cascadeOnDelete();
            $table->enum('event_type', ['CREATE_COURSE', 'ENROLL_TEACHER', 'ENROLL_STUDENT', 'SYNC_GRADES'])->index();
            $table->string('lms_course_id', 100)->nullable();
            $table->enum('status', ['QUEUED', 'PROCESSING', 'COMPLETED', 'FAILED'])->default('QUEUED')->index();
            $table->integer('retry_count')->default(0);
            $table->text('error_log')->nullable();
            $table->timestamps();
        });

        // 3. Tracer Study & Alumni Surveys
        Schema::create('tracer_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->unique()->constrained('siswas')->cascadeOnDelete();
            $table->year('tahun_lulus')->index();
            $table->enum('status_alumni', ['Bekerja', 'Wirausaha', 'Melanjutkan Studi', 'Mencari Kerja'])->index();
            $table->string('nama_instansi_kerja', 150)->nullable();
            $table->string('jabatan_posisi', 100)->nullable();
            $table->decimal('gaji_pertama', 12, 2)->nullable();
            $table->integer('masa_tunggu_bulan')->default(0); // Waktu tunggu dapat kerja
            $table->enum('keselarasan_bidang', ['Sangat Selaras', 'Selaras', 'Kurang Selaras', 'Tidak Selaras'])->default('Sangat Selaras');
            $table->timestamps();
        });

        // 4. Employer Feedback (Guest-Accessible Secure Token Portal)
        Schema::create('employer_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tracer_study')->constrained('tracer_studies')->cascadeOnDelete();
            $table->string('access_token', 64)->unique()->index(); // Token-secured URL
            $table->string('nama_penilai_atasan', 150);
            $table->string('jabatan_penilai', 100);
            $table->string('email_perusahaan', 150);
            $table->string('nama_perusahaan', 150);
            $table->integer('skor_integritas_etika')->default(4); // 1-5
            $table->integer('skor_keahlian_bidang')->default(4);
            $table->integer('skor_bahasa_asing')->default(4);
            $table->integer('skor_penggunaan_ti')->default(4);
            $table->integer('skor_komunikasi')->default(4);
            $table->integer('skor_kerjasama_tim')->default(4);
            $table->integer('skor_pengembangan_diri')->default(4);
            $table->text('saran_kurikulum')->nullable();
            $table->boolean('is_completed')->default(false)->index();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 5. Smart Facility & Room Booking Manager
        Schema::create('booking_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_ruangan')->constrained('ruangans')->cascadeOnDelete();
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();
            $table->string('nama_kegiatan', 200);
            $table->string('organisasi_pemohon', 150);
            $table->date('tanggal_booking')->index();
            $table->time('jam_mulai')->index();
            $table->time('jam_selesai')->index();
            $table->enum('status_persetujuan', ['Diajukan', 'Disetujui', 'Ditolak', 'Dibatalkan'])->default('Diajukan')->index();
            $table->text('alasan_penolakan')->nullable();
            $table->string('pejabat_approver', 150)->nullable();
            $table->timestamps();
        });

        // 6. Immutable Audit Trail Logging (High-Precision Microseconds)
        Schema::create('audit_trail_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action_event', 100)->index(); // KRS_PESSIMISTIC_LOCK, GRADE_SUBMIT, H2H_PAYMENT
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url_endpoint', 255)->nullable();
            $table->json('request_payload')->nullable();
            $table->json('state_before')->nullable();
            $table->json('state_after')->nullable();
            $table->string('timestamp_microseconds', 30)->index(); // e.g. 1765293849.123456
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trail_logs');
        Schema::dropIfExists('booking_fasilitas');
        Schema::dropIfExists('employer_feedbacks');
        Schema::dropIfExists('tracer_studies');
        Schema::dropIfExists('lms_sync_queues');
        Schema::dropIfExists('pddikti_sync_logs');
    }
};
