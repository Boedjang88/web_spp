<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KrsDetail extends Model
{
    use HasFactory;

    protected $table = 'krs_details';

    protected $fillable = [
        'id_krs',
        'id_kelas_kuliah',
        'status_ambil',
        'nilai_kehadiran',
        'nilai_tugas',
        'nilai_quiz',
        'nilai_uts',
        'nilai_uas',
        'nilai_praktikum',
        'nilai_akhir_angka',
        'nilai_akhir_huruf',
        'bobot_mutu',
        'is_lulus',
        'is_published',
    ];

    protected $casts = [
        'nilai_kehadiran' => 'decimal:2',
        'nilai_tugas' => 'decimal:2',
        'nilai_quiz' => 'decimal:2',
        'nilai_uts' => 'decimal:2',
        'nilai_uas' => 'decimal:2',
        'nilai_praktikum' => 'decimal:2',
        'nilai_akhir_angka' => 'decimal:2',
        'bobot_mutu' => 'decimal:2',
        'is_lulus' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function krs(): BelongsTo
    {
        return $this->belongsTo(Krs::class, 'id_krs');
    }

    public function kelasKuliah(): BelongsTo
    {
        return $this->belongsTo(KelasKuliah::class, 'id_kelas_kuliah');
    }

    public function edomEvaluasi(): HasOne
    {
        return $this->hasOne(EdomEvaluasi::class, 'id_krs_detail');
    }

    public function nilaiObes(): HasMany
    {
        return $this->hasMany(NilaiObe::class, 'id_krs_detail');
    }
}
