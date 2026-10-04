<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * University Entity & Terminology Refactoring (Mahasiswa, Dosen, UKT, Invoicing)
     */
    public function up(): void
    {
        // 1. Dosens Table (University Lecturers)
        if (!Schema::hasTable('dosens')) {
            Schema::create('dosens', function (Blueprint $table) {
                $table->id();
                $table->string('nidn', 30)->unique()->nullable()->index();
                $table->string('nidk', 30)->unique()->nullable()->index();
                $table->string('nip', 30)->nullable()->index();
                $table->string('nama_dosen', 150);
                $table->string('gelar_depan', 30)->nullable();
                $table->string('gelar_belakang', 50)->nullable();
                $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
                $table->string('no_telp', 20)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('alamat')->nullable();
                $table->foreignId('id_prodi')->nullable()->constrained('program_studis')->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. UKT (Uang Kuliah Tunggal) Master Matrix
        if (!Schema::hasTable('ukts')) {
            Schema::create('ukts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_prodi')->nullable()->constrained('program_studis')->nullOnDelete();
                $table->integer('tahun')->index();
                $table->string('kelompok_ukt', 20)->default('UKT 3'); // UKT 1 s/d UKT 8
                $table->decimal('nominal', 12, 2);
                $table->decimal('biaya_praktikum', 12, 2)->default(0.00);
                $table->decimal('biaya_kemahasiswaan', 12, 2)->default(50000.00);
                $table->text('deskripsi')->nullable();
                $table->timestamps();
            });
        }

        // 3. Mahasiswas Table (University Students)
        if (!Schema::hasTable('mahasiswas')) {
            Schema::create('mahasiswas', function (Blueprint $table) {
                $table->id();
                $table->string('nim', 30)->unique()->index();
                $table->string('nisn', 30)->nullable()->index();
                $table->text('nik')->nullable(); // Encrypted
                $table->string('nama', 150)->index();
                $table->foreignId('id_prodi')->nullable()->constrained('program_studis')->nullOnDelete();
                $table->foreignId('id_dosen_pa')->nullable()->constrained('dosens')->nullOnDelete();
                $table->foreignId('id_ukt')->nullable()->constrained('ukts')->nullOnDelete();
                $table->string('alamat')->nullable();
                $table->text('nama_ibu_kandung')->nullable(); // Encrypted
                $table->string('no_telp', 20)->nullable();
                $table->text('no_hp_wali')->nullable(); // Encrypted
                $table->string('status_kelulusan', 30)->default('Aktif')->index(); // Aktif, Lulus, Cuti, DropOut
                $table->dateTime('tgl_kelulusan')->nullable();
                $table->string('nomor_ijazah', 100)->nullable();
                $table->unsignedInteger('total_skpi_points')->default(0);
                $table->dateTime('consent_pdp_at')->nullable();
                $table->string('consent_pdp_ip', 45)->nullable();
                $table->timestamps();
            });
        }

        // 4. Tagihan UKT (Semester Fee Invoicing & Fee Breakdown)
        if (!Schema::hasTable('tagihan_ukts')) {
            Schema::create('tagihan_ukts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_mahasiswa')->constrained('mahasiswas')->cascadeOnDelete();
                $table->foreignId('id_tahun_akademik')->constrained('tahun_akademiks')->cascadeOnDelete();
                $table->foreignId('id_bank_mitra')->nullable()->constrained('bank_mitras')->nullOnDelete();
                $table->string('nomor_va', 30)->unique()->index();
                $table->string('nomor_invoice', 50)->unique()->index();
                $table->decimal('biaya_ukt', 12, 2)->default(0.00);
                $table->decimal('biaya_praktikum', 12, 2)->default(0.00);
                $table->decimal('biaya_kemahasiswaan', 12, 2)->default(0.00);
                $table->decimal('total_tagihan', 12, 2)->default(0.00);
                $table->decimal('total_potongan_beasiswa', 12, 2)->default(0.00);
                $table->decimal('total_harus_bayar', 12, 2)->default(0.00);
                $table->decimal('total_sudah_bayar', 12, 2)->default(0.00);
                $table->enum('status_pembayaran', ['Belum Bayar', 'Sebagian', 'Lunas', 'Kadaluarsa'])->default('Belum Bayar')->index();
                $table->dateTime('tgl_jatuh_tempo');
                $table->dateTime('tgl_lunas')->nullable();
                $table->timestamps();

                $table->unique(['id_mahasiswa', 'id_tahun_akademik']);
            });
        }

        // 5. Pembayaran UKT (Payment Transactions & Receipts)
        if (!Schema::hasTable('pembayaran_ukts')) {
            Schema::create('pembayaran_ukts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();
                $table->foreignId('id_mahasiswa')->constrained('mahasiswas')->cascadeOnDelete();
                $table->foreignId('id_tagihan_ukt')->nullable()->constrained('tagihan_ukts')->nullOnDelete();
                $table->foreignId('id_ukt')->nullable()->constrained('ukts')->nullOnDelete();
                $table->dateTime('tgl_bayar');
                $table->string('semester_dibayar', 20)->default('Genap');
                $table->string('tahun_dibayar', 10)->default('2026');
                $table->decimal('jumlah_bayar', 12, 2);
                $table->string('channel_bayar', 50)->default('H2H_BANK');
                $table->string('nomor_transaksi_bank', 100)->nullable()->unique();
                $table->string('kode_bank', 20)->default('BNI');
                $table->string('nomor_kuitansi', 50)->unique();
                $table->string('status_transaksi', 30)->default('SUCCESS');
                $table->timestamps();
            });
        }

        // 6. Update Users table for University relations & Roles
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'id_mahasiswa')) {
                $table->foreignId('id_mahasiswa')->nullable()->constrained('mahasiswas')->nullOnDelete()->after('id_siswa');
            }
            if (!Schema::hasColumn('users', 'id_dosen')) {
                $table->foreignId('id_dosen')->nullable()->constrained('dosens')->nullOnDelete()->after('id_guru');
            }
        });

        // 7. Update Assignments table for Multi-class distribution
        Schema::table('assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('assignments', 'target_kelas_ids')) {
                $table->json('target_kelas_ids')->nullable()->after('id_kelas_kuliah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            if (Schema::hasColumn('assignments', 'target_kelas_ids')) {
                $table->dropColumn('target_kelas_ids');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'id_dosen')) {
                $table->dropForeign(['id_dosen']);
                $table->dropColumn('id_dosen');
            }
            if (Schema::hasColumn('users', 'id_mahasiswa')) {
                $table->dropForeign(['id_mahasiswa']);
                $table->dropColumn('id_mahasiswa');
            }
        });

        Schema::dropIfExists('pembayaran_ukts');
        Schema::dropIfExists('tagihan_ukts');
        Schema::dropIfExists('mahasiswas');
        Schema::dropIfExists('ukts');
        Schema::dropIfExists('dosens');
    }
};
