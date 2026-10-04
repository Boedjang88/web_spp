<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianSidang extends Model
{
    use HasFactory;

    protected $table = 'penilaian_sidangs';

    protected $fillable = [
        'id_sidang',
        'id_guru',
        'skor_presentasi',
        'skor_penguasaan_materi',
        'skor_metodologi_karya',
        'skor_total_penguji',
        'catatan_penguji',
    ];

    protected $casts = [
        'skor_presentasi' => 'decimal:2',
        'skor_penguasaan_materi' => 'decimal:2',
        'skor_metodologi_karya' => 'decimal:2',
        'skor_total_penguji' => 'decimal:2',
    ];

    public function sidang(): BelongsTo
    {
        return $this->belongsTo(SidangSkripsi::class, 'id_sidang');
    }

    public function dosenPenguji(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
}
