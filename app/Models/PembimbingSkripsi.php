<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembimbingSkripsi extends Model
{
    use HasFactory;

    protected $table = 'pembimbing_skripsis';

    protected $fillable = [
        'id_tugas_akhir',
        'id_guru',
        'peran',
        'urutan',
    ];

    public function tugasAkhir(): BelongsTo
    {
        return $this->belongsTo(TugasAkhir::class, 'id_tugas_akhir');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
}
