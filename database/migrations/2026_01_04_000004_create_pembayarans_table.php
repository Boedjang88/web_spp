<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            
            // Petugas ambil dari tabel 'users' bawaan Laravel
            $table->foreignId('id_petugas')->constrained('users')->onDelete('cascade');
            
            // Siswa yang bayar
            $table->foreignId('id_siswa')->constrained('siswas')->onDelete('cascade');
            
            $table->date('tgl_bayar');
            $table->string('bulan_dibayar', 20); // Januari, Februari, dst
            $table->string('tahun_dibayar', 4); // 2025
            
            // ID SPP disimpen lagi disini buat history (kalau tahun depan naik, data lama aman)
            $table->foreignId('id_spp')->constrained('spps');
            
            $table->integer('jumlah_bayar');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};