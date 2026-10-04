<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliahs';

    protected $fillable = [
        'id_kurikulum',
        'kode_mk',
        'nama_mk',
        'nama_mk_en',
        'sks_teori',
        'sks_praktik',
        'sks_total',
        'semester_rekomendasi',
        'jenis_mk',
    ];

    public function kurikulum(): BelongsTo
    {
        return $this->belongsTo(Kurikulum::class, 'id_kurikulum');
    }

    public function prasyarats(): BelongsToMany
    {
        return $this->belongsToMany(
            MataKuliah::class,
            'mata_kuliah_prasyarats',
            'id_mk',
            'id_mk_prasyarat'
        )->withPivot('nilai_minimum')->withTimestamps();
    }

    public function kelasKuliahs(): HasMany
    {
        return $this->hasMany(KelasKuliah::class, 'id_mk');
    }

    public function cpmks(): HasMany
    {
        return $this->hasMany(Cpmk::class, 'id_mk');
    }
}
