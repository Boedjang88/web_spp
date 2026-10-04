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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'id_guru')) {
                $table->foreignId('id_guru')->nullable()->after('role')->constrained('gurus')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'id_siswa')) {
                $table->foreignId('id_siswa')->nullable()->after('id_guru')->constrained('siswas')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('id_siswa');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_guru']);
            $table->dropForeign(['id_siswa']);
            $table->dropColumn(['id_guru', 'id_siswa', 'is_active']);
        });
    }
};
