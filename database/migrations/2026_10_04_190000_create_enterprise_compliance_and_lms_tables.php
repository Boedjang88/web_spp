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
        // 1. Alter siswas table for graduation clearance & SKPI points
        Schema::table('siswas', function (Blueprint $table) {
            if (!Schema::hasColumn('siswas', 'status_kelulusan')) {
                $table->string('status_kelulusan')->default('Aktif')->after('id_spp');
                $table->dateTime('tgl_kelulusan')->nullable()->after('status_kelulusan');
                $table->string('nomor_ijazah')->nullable()->after('tgl_kelulusan');
                $table->unsignedInteger('total_skpi_points')->default(0)->after('nomor_ijazah');
            }
        });

        // 2. Alter krs_details table for incomplete grade (BL/T) expiration
        Schema::table('krs_details', function (Blueprint $table) {
            if (!Schema::hasColumn('krs_details', 'incomplete_expires_at')) {
                $table->dateTime('incomplete_expires_at')->nullable()->after('nilai_akhir_huruf');
                $table->boolean('is_incomplete_expired')->default(false)->after('incomplete_expires_at');
            }
        });

        // 3. Early Warning System Logs
        if (!Schema::hasTable('early_warning_logs')) {
            Schema::create('early_warning_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_siswa')->constrained('siswas')->onDelete('cascade');
                $table->foreignId('id_dosen_pa')->nullable()->constrained('gurus')->onDelete('set null');
                $table->string('severity')->default('MEDIUM'); // LOW, MEDIUM, HIGH, CRITICAL
                $table->string('trigger_type'); // INACTIVE_KRS_CONSECUTIVE, LOW_GPA_CONSECUTIVE, LOW_ATTENDANCE
                $table->text('trigger_reason');
                $table->json('metrics_payload')->nullable();
                $table->boolean('is_resolved')->default(false);
                $table->dateTime('resolved_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['id_siswa', 'severity', 'is_resolved']);
            });
        }

        // 4. Financial Discrepancies Table
        if (!Schema::hasTable('financial_discrepancies')) {
            Schema::create('financial_discrepancies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_reconciliation_log')->nullable()->constrained('reconciliation_logs')->onDelete('set null');
                $table->string('bank_code', 20);
                $table->string('nomor_transaksi_bank')->nullable();
                $table->string('nomor_va')->nullable();
                $table->decimal('amount_bank', 15, 2)->default(0);
                $table->decimal('amount_siakad', 15, 2)->default(0);
                $table->decimal('discrepancy_amount', 15, 2)->default(0);
                $table->string('anomaly_type'); // UNMATCHED_IN_SIAKAD, AMOUNT_MISMATCH, UNMATCHED_IN_BANK, DUPLICATE_ENTRY
                $table->string('status', 30)->default('OPEN'); // OPEN, INVESTIGATING, RESOLVED
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['bank_code', 'status']);
                $table->index('nomor_transaksi_bank');
            });
        }

        // 5. Course Materials Table (LMS Timed-Release)
        if (!Schema::hasTable('course_materials')) {
            Schema::create('course_materials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_kelas_kuliah')->constrained('kelas_kuliahs')->onDelete('cascade');
                $table->string('judul');
                $table->text('deskripsi')->nullable();
                $table->string('file_path');
                $table->string('original_filename')->nullable();
                $table->string('file_type', 50)->nullable();
                $table->unsignedBigInteger('file_size')->default(0);
                $table->unsignedTinyInteger('minggu_ke')->default(1);
                $table->dateTime('publish_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['id_kelas_kuliah', 'minggu_ke', 'publish_at']);
            });
        }

        // 6. Assignments Table (LMS OBE Weight & Multi-Class Distribution)
        if (!Schema::hasTable('assignments')) {
            Schema::create('assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_kelas_kuliah')->constrained('kelas_kuliahs')->onDelete('cascade');
                $table->string('judul');
                $table->text('deskripsi')->nullable();
                $table->string('attachment_path')->nullable();
                $table->string('komponen_penilaian', 30)->default('TUGAS'); // TUGAS, QUIZ, PRAKTIKUM, UTS, UAS
                $table->decimal('bobot_persen', 5, 2)->default(10.00);
                $table->dateTime('deadline_at');
                $table->boolean('allow_late_submission')->default(false);
                $table->unsignedSmallInteger('late_grace_minutes')->default(0);
                $table->boolean('is_published')->default(true);
                $table->boolean('is_anonymous_grading')->default(false);
                $table->timestamps();

                $table->index(['id_kelas_kuliah', 'deadline_at', 'is_published']);
            });
        }

        // 7. Submissions Table (LMS Millisecond Precision, Fingerprint & Audit)
        if (!Schema::hasTable('submissions')) {
            Schema::create('submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_assignment')->constrained('assignments')->onDelete('cascade');
                $table->foreignId('id_siswa')->constrained('siswas')->onDelete('cascade');
                $table->string('file_path');
                $table->string('original_filename');
                $table->string('file_mime', 100);
                $table->unsignedBigInteger('file_size')->default(0);
                $table->dateTime('submitted_at');
                $table->decimal('submission_microtime', 16, 6)->nullable();
                $table->string('submission_token', 64)->unique();
                $table->string('device_fingerprint', 64)->nullable();
                $table->string('client_ip', 45)->nullable();
                $table->boolean('is_late')->default(false);
                $table->decimal('nilai', 5, 2)->nullable();
                $table->text('feedback')->nullable();
                $table->dateTime('graded_at')->nullable();
                $table->foreignId('graded_by_dosen')->nullable()->constrained('gurus')->onDelete('set null');
                $table->timestamps();

                $table->index(['id_assignment', 'id_siswa']);
                $table->index('submission_token');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('course_materials');
        Schema::dropIfExists('financial_discrepancies');
        Schema::dropIfExists('early_warning_logs');

        Schema::table('krs_details', function (Blueprint $table) {
            if (Schema::hasColumn('krs_details', 'incomplete_expires_at')) {
                $table->dropColumn(['incomplete_expires_at', 'is_incomplete_expired']);
            }
        });

        Schema::table('siswas', function (Blueprint $table) {
            if (Schema::hasColumn('siswas', 'status_kelulusan')) {
                $table->dropColumn(['status_kelulusan', 'tgl_kelulusan', 'nomor_ijazah', 'total_skpi_points']);
            }
        });
    }
};
