<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MbkmKonversiDetail extends Model
{
    protected $table = 'mbkm_konversi_details';

    protected $fillable = [
        'id_mbkm_konversi',
        'id_matakuliah',
        'sks_diakui',
        'nilai_angka_konversi',
        'nilai_huruf_konversi',
        'bobot_mutu',
        'catatan_dosen_pa',
    ];

    public function mbkmKonversi(): BelongsTo
    {
        return $this->belongsTo(MbkmKonversi::class, 'id_mbkm_konversi');
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'id_matakuliah');
    }
}
