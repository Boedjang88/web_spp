<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;
    
    protected $table = 'pembayarans';

    protected $fillable = [
        'id_petugas', 'id_siswa', 'tgl_bayar', 
        'bulan_dibayar', 'tahun_dibayar', 'id_spp', 'jumlah_bayar'
    ];

    // Relasi
    public function siswa(): BelongsTo { return $this->belongsTo(Siswa::class, 'id_siswa'); }
    public function petugas(): BelongsTo { return $this->belongsTo(User::class, 'id_petugas'); }
    public function spp(): BelongsTo { return $this->belongsTo(Spp::class, 'id_spp'); }
}