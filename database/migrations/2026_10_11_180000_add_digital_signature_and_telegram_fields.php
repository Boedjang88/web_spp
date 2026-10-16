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
        Schema::table('surat_akademiks', function (Blueprint $table) {
            $table->string('digital_signature_hash', 128)->nullable()->after('qr_verification_token');
            $table->string('signature_provider', 30)->default('BSRE')->after('digital_signature_hash'); // BSRE, E_METERAI, INTERNAL_PKI
            $table->string('signer_name', 150)->nullable()->after('signature_provider');
            $table->timestamp('signed_at')->nullable()->after('signer_name');
        });

        Schema::table('tugas_akhirs', function (Blueprint $table) {
            $table->string('digital_signature_hash', 128)->nullable()->after('file_naskah_akhir_path');
            $table->string('signature_provider', 30)->default('BSRE')->after('digital_signature_hash');
            $table->boolean('is_digitally_signed')->default(false)->after('signature_provider');
            $table->timestamp('signed_at')->nullable()->after('is_digitally_signed');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_chat_id', 50)->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_akademiks', function (Blueprint $table) {
            $table->dropColumn([
                'digital_signature_hash',
                'signature_provider',
                'signer_name',
                'signed_at',
            ]);
        });

        Schema::table('tugas_akhirs', function (Blueprint $table) {
            $table->dropColumn([
                'digital_signature_hash',
                'signature_provider',
                'is_digitally_signed',
                'signed_at',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telegram_chat_id']);
        });
    }
};
