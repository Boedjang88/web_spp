<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Host-to-Host (H2H) Enterprise Banking & Automated Virtual Account Billing
     */
    public function up(): void
    {
        // 1. Bank Mitra Host-to-Host
        Schema::create('bank_mitras', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bank', 10)->unique(); // BNI, MANDIRI, BRI, BCA, BSI
            $table->string('nama_bank', 100);
            $table->string('prefix_va', 10); // e.g. 988 (BNI VA), 887 (Mandiri VA)
            $table->string('secret_key', 128); // HMAC Secret for Webhook Signature Verification
            $table->string('webhook_url', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tagihan Virtual Account (VA Billing)
        Schema::create('tagihan_vas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('id_tahun_akademik')->constrained('tahun_akademiks')->cascadeOnDelete();
            $table->foreignId('id_bank_mitra')->nullable()->constrained('bank_mitras')->nullOnDelete();
            $table->string('nomor_va', 30)->unique()->index();
            $table->string('nomor_invoice', 50)->unique()->index();
            $table->decimal('total_tagihan', 12, 2)->default(0.00);
            $table->decimal('total_potongan_beasiswa', 12, 2)->default(0.00);
            $table->decimal('total_harus_bayar', 12, 2)->default(0.00);
            $table->decimal('total_sudah_bayar', 12, 2)->default(0.00);
            $table->enum('status_pembayaran', ['Belum Bayar', 'Sebagian', 'Lunas', 'Kadaluarsa'])->default('Belum Bayar')->index();
            $table->dateTime('tgl_jatuh_tempo');
            $table->dateTime('tgl_lunas')->nullable();
            $table->timestamps();

            $table->unique(['id_siswa', 'id_tahun_akademik']);
        });

        // 3. Rincian Item Tagihan VA
        Schema::create('tagihan_va_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tagihan_va')->constrained('tagihan_vas')->cascadeOnDelete();
            $table->string('nama_item', 150); // UKT / SPP Tetap, SKS Variabel, Biaya Lab, Praktikum
            $table->decimal('nominal', 12, 2);
            $table->timestamps();
        });

        // 4. Log Transaksi H2H & Webhook Callback Payloads
        Schema::create('transaksi_h2hs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tagihan_va')->constrained('tagihan_vas')->cascadeOnDelete();
            $table->string('nomor_transaksi_bank', 100)->unique();
            $table->string('kode_bank', 10)->index();
            $table->string('nomor_va', 30)->index();
            $table->decimal('jumlah_dibayar', 12, 2);
            $table->dateTime('waktu_transaksi_bank');
            $table->string('channel_bayar', 50)->nullable(); // ATM, Mobile Banking, Teller, QRIS
            $table->string('signature_hash', 128)->nullable();
            $table->json('raw_callback_payload')->nullable();
            $table->enum('status_callback', ['SUCCESS', 'DUPLICATE', 'REJECTED'])->default('SUCCESS');
            $table->timestamps();
        });

        // 5. Financial Clearance Lock (Pelepas Blokir KRS Real-time)
        Schema::create('financial_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_siswa')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('id_tahun_akademik')->constrained('tahun_akademiks')->cascadeOnDelete();
            $table->boolean('is_krs_unlocked')->default(false)->index();
            $table->boolean('is_uts_unlocked')->default(false)->index();
            $table->boolean('is_uas_unlocked')->default(false)->index();
            $table->timestamp('unlocked_at')->nullable();
            $table->string('unlocked_by_channel', 50)->nullable(); // H2H_WEBHOOK, ADMIN_MANUAL
            $table->timestamps();

            $table->unique(['id_siswa', 'id_tahun_akademik']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_clearances');
        Schema::dropIfExists('transaksi_h2hs');
        Schema::dropIfExists('tagihan_va_items');
        Schema::dropIfExists('tagihan_vas');
        Schema::dropIfExists('bank_mitras');
    }
};
