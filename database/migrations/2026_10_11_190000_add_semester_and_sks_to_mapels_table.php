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
        Schema::table('mapels', function (Blueprint $table) {
            if (!Schema::hasColumn('mapels', 'semester')) {
                $table->integer('semester')->default(1)->after('kkm');
            }
            if (!Schema::hasColumn('mapels', 'semester_rekomendasi')) {
                $table->integer('semester_rekomendasi')->default(1)->after('semester');
            }
            if (!Schema::hasColumn('mapels', 'sks')) {
                $table->integer('sks')->default(3)->after('semester_rekomendasi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mapels', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('mapels', 'sks')) {
                $columnsToDrop[] = 'sks';
            }
            if (Schema::hasColumn('mapels', 'semester_rekomendasi')) {
                $columnsToDrop[] = 'semester_rekomendasi';
            }
            if (Schema::hasColumn('mapels', 'semester')) {
                $columnsToDrop[] = 'semester';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
