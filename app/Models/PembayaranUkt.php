<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranUkt extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_ukts';

    protected $fillable = [
        'id_user',
        'id_mahasiswa',
        'id_tagihan_ukt',
        'id_ukt',
        'tgl_bayar',
        'semester_dibayar',
        'tahun_dibayar',
        'jumlah_bayar',
        'channel_bayar',
        'nomor_transaksi_bank',
        'kode_bank',
        'nomor_kuitansi',
        'status_transaksi',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tgl_bayar' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }

    public function tagihanUkt(): BelongsTo
    {
        return $this->belongsTo(TagihanUkt::class, 'id_tagihan_ukt');
    }

    public function ukt(): BelongsTo
    {
        return $this->belongsTo(Ukt::class, 'id_ukt');
    }

    /**
     * Terbilang generator for Indonesian Rupiah
     */
    public function getTerbilangAttribute(): string
    {
        return self::terbilang((int) $this->jumlah_bayar) . ' rupiah';
    }

    public static function terbilang(int $angka): string
    {
        $angka = abs($angka);
        $baca = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        $terbilang = '';

        if ($angka < 12) {
            $terbilang = ' ' . $baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = self::terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            $terbilang = self::terbilang((int) ($angka / 10)) . ' puluh' . self::terbilang($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = ' seratus' . self::terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = self::terbilang((int) ($angka / 100)) . ' ratus' . self::terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = ' seribu' . self::terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = self::terbilang((int) ($angka / 1000)) . ' ribu' . self::terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = self::terbilang((int) ($angka / 1000000)) . ' juta' . self::terbilang($angka % 1000000);
        }

        return trim($terbilang);
    }
}
