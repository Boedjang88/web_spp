<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Single Sign-On (SSO) & Multi-Factor Authentication (MFA / TOTP)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mfa_secret', 64)->nullable()->after('remember_token');
            $table->boolean('mfa_enabled')->default(false)->after('mfa_secret');
            $table->string('sso_provider', 50)->nullable()->after('mfa_enabled'); // Google Workspace, Microsoft Azure AD, CAS
            $table->string('sso_provider_id', 150)->nullable()->after('sso_provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mfa_secret', 'mfa_enabled', 'sso_provider', 'sso_provider_id']);
        });
    }
};
