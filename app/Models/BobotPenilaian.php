<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BobotPenilaian extends Model
{
    use HasFactory;

    protected $table = 'bobot_penilaians';

    protected $fillable = [
        'id_kelas_kuliah',
        'bobot_kehadiran',
        'bobot_tugas',
        'bobot_quiz',
        'bobot_uts',
        'bobot_uas',
        'bobot_praktikum',
    ];

    public function kelasKuliah(): BelongsTo
    {
        return $this->belongsTo(KelasKuliah::class, 'id_kelas_kuliah');
    }
}
