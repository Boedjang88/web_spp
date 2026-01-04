<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spps', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun'); // Contoh: 2024
            $table->integer('nominal'); // Contoh: 300000
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spps');
    }
};