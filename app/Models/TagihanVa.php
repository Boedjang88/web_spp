<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TagihanVa extends Model
{
    use HasFactory;

    protected $table = 'tagihan_vas';

    protected $fillable = [
        'id_siswa',
        'id_tahun_akademik',
        'id_bank_mitra',
        'nomor_va',
        'nomor_invoice',
        'total_tagihan',
        'total_potongan_beasiswa',
        'total_harus_bayar',
        'total_sudah_bayar',
        'status_pembayaran',
        'tgl_jatuh_tempo',
        'tgl_lunas',
    ];

    protected $casts = [
        'total_tagihan' => 'decimal:2',
        'total_potongan_beasiswa' => 'decimal:2',
        'total_harus_bayar' => 'decimal:2',
        'total_sudah_bayar' => 'decimal:2',
        'tgl_jatuh_tempo' => 'datetime',
        'tgl_lunas' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }

    public function bankMitra(): BelongsTo
    {
        return $this->belongsTo(BankMitra::class, 'id_bank_mitra');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TagihanVaItem::class, 'id_tagihan_va');
    }

    public function transaksiH2hs(): HasMany
    {
        return $this->hasMany(TransaksiH2h::class, 'id_tagihan_va');
    }
}
