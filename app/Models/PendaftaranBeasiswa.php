<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendaftaranBeasiswa extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_beasiswas';

    protected $fillable = [
        'id_beasiswa',
        'id_siswa',
        'status_pengajuan',
        'ipk_terakhir',
        'tgl_pengajuan',
        'catatan',
    ];

    protected $casts = [
        'ipk_terakhir' => 'decimal:2',
        'tgl_pengajuan' => 'datetime',
    ];

    public function beasiswa(): BelongsTo
    {
        return $this->belongsTo(Beasiswa::class, 'id_beasiswa');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }
}
