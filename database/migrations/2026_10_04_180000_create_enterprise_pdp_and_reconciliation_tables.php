<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * UU PDP Compliance: Encrypted PII Fields & Digital Privacy Consent
     */
    public function up(): void
    {
        // 1. Add Encrypted PII columns & Consent to Siswa
        Schema::table('siswas', function (Blueprint $table) {
            $table->text('nik')->nullable()->after('nis');
            $table->text('nama_ibu_kandung')->nullable()->after('alamat');
            $table->text('no_hp_wali')->nullable()->after('no_telp');
            $table->timestamp('consent_pdp_at')->nullable()->after('id_spp');
            $table->string('consent_pdp_ip', 45)->nullable()->after('consent_pdp_at');
        });

        // 2. Add Encrypted PII columns & Consent to Guru / Dosen
        Schema::table('gurus', function (Blueprint $table) {
            $table->text('nik')->nullable()->after('nip');
            $table->timestamp('consent_pdp_at')->nullable()->after('alamat');
            $table->string('consent_pdp_ip', 45)->nullable()->after('consent_pdp_at');
        });

        // 3. Add Privacy Consent tracking to Users
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('consent_pdp_at')->nullable()->after('sso_provider_id');
            $table->string('consent_pdp_ip', 45)->nullable()->after('consent_pdp_at');
        });

        // 4. Reconciliation discrepancy records table
        Schema::create('reconciliation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('bank_code', 10)->index();
            $table->date('settlement_date')->index();
            $table->string('file_source', 200)->nullable();
            $table->integer('total_bank_records')->default(0);
            $table->integer('total_matched_records')->default(0);
            $table->integer('total_discrepancies')->default(0);
            $table->decimal('total_amount_bank', 15, 2)->default(0.00);
            $table->decimal('total_amount_siakad', 15, 2)->default(0.00);
            $table->json('discrepancy_details')->nullable();
            $table->enum('status', ['MATCHED', 'DISCREPANCY_FOUND', 'RESOLVED'])->default('MATCHED')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliation_logs');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['consent_pdp_at', 'consent_pdp_ip']);
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn(['nik', 'consent_pdp_at', 'consent_pdp_ip']);
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['nik', 'nama_ibu_kandung', 'no_hp_wali', 'consent_pdp_at', 'consent_pdp_ip']);
        });
    }
};
