<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kurikulum extends Model
{
    use HasFactory;

    protected $table = 'kurikulums';

    protected $fillable = [
        'id_prodi',
        'nama_kurikulum',
        'tahun_mulai',
        'total_sks_lulus',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'id_prodi');
    }

    public function mataKuliahs(): HasMany
    {
        return $this->hasMany(MataKuliah::class, 'id_kurikulum');
    }
}
