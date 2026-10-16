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
        Schema::table('bank_mitras', function (Blueprint $table) {
            $table->string('gateway_provider', 30)->default('H2H_DIRECT')->after('nama_bank'); // H2H_DIRECT, MIDTRANS, XENDIT
            $table->string('server_key', 255)->nullable()->after('secret_key');
            $table->string('client_key', 255)->nullable()->after('server_key');
            $table->string('merchant_id', 100)->nullable()->after('client_key');
            $table->string('environment', 20)->default('sandbox')->after('merchant_id'); // sandbox, production
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_mitras', function (Blueprint $table) {
            $table->dropColumn([
                'gateway_provider',
                'server_key',
                'client_key',
                'merchant_id',
                'environment',
            ]);
        });
    }
};
