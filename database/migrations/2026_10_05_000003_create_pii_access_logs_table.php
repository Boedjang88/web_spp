<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Schema for UU PDP PII Access Audit Logs.
     */
    public function up(): void
    {
        if (!Schema::hasTable('pii_access_logs')) {
            Schema::create('pii_access_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('target_model');
                $table->string('target_id');
                $table->string('accessed_field');
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->string('purpose_reason')->default('ADMIN_VIEW_UNMASKED_PII');
                $table->timestamps();

                $table->index(['user_id', 'target_model'], 'idx_pii_audit');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pii_access_logs');
    }
};
