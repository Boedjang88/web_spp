<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiH2h extends Model
{
    use HasFactory;

    protected $table = 'transaksi_h2hs';

    protected $fillable = [
        'id_tagihan_va',
        'nomor_transaksi_bank',
        'kode_bank',
        'nomor_va',
        'jumlah_dibayar',
        'waktu_transaksi_bank',
        'channel_bayar',
        'signature_hash',
        'raw_callback_payload',
        'status_callback',
    ];

    protected $casts = [
        'jumlah_dibayar' => 'decimal:2',
        'waktu_transaksi_bank' => 'datetime',
        'raw_callback_payload' => 'array',
    ];

    public function tagihanVa(): BelongsTo
    {
        return $this->belongsTo(TagihanVa::class, 'id_tagihan_va');
    }
}
