<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->char('nisn', 10)->unique();
            $table->char('nis', 8);
            $table->string('nama', 50);
            
            // Relasi ke tabel Kelas
            // onDelete('cascade') artinya kalau kelas dihapus, siswa ikut kehapus (biar gak error)
            $table->foreignId('id_kelas')->constrained('kelas')->onDelete('cascade');
            
            $table->text('alamat');
            $table->string('no_telp', 15);
            
            // Relasi ke tabel SPP (Tarif siswa ini berapa)
            $table->foreignId('id_spp')->constrained('spps')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};