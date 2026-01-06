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
    // Cek dulu: Kalau kolom 'role' BELUM ada, baru tambahin.
    // Kalau udah ada, lewatin aja (biar gak error).
    if (!Schema::hasColumn('users', 'role')) {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('siswa')->after('email');
        });
    }
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
};
