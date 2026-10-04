<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TagihanUkt extends Model
{
    use HasFactory;

    protected $table = 'tagihan_ukts';

    protected $fillable = [
        'id_mahasiswa',
        'id_tahun_akademik',
        'id_bank_mitra',
        'nomor_va',
        'nomor_invoice',
        'biaya_ukt',
        'biaya_praktikum',
        'biaya_kemahasiswaan',
        'total_tagihan',
        'total_potongan_beasiswa',
        'total_harus_bayar',
        'total_sudah_bayar',
        'status_pembayaran',
        'tgl_jatuh_tempo',
        'tgl_lunas',
    ];

    protected $casts = [
        'biaya_ukt' => 'decimal:2',
        'biaya_praktikum' => 'decimal:2',
        'biaya_kemahasiswaan' => 'decimal:2',
        'total_tagihan' => 'decimal:2',
        'total_potongan_beasiswa' => 'decimal:2',
        'total_harus_bayar' => 'decimal:2',
        'total_sudah_bayar' => 'decimal:2',
        'tgl_jatuh_tempo' => 'datetime',
        'tgl_lunas' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }

    public function bankMitra(): BelongsTo
    {
        return $this->belongsTo(BankMitra::class, 'id_bank_mitra');
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(PembayaranUkt::class, 'id_tagihan_ukt');
    }

    public function getSisaTagihanAttribute(): float
    {
        return max(0, (float) $this->total_harus_bayar - (float) $this->total_sudah_bayar);
    }

    public function getSisaHarusBayarAttribute(): float
    {
        return $this->sisa_tagihan;
    }
}
