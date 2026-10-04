<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAkademik extends Model
{
    use HasFactory;

    protected $table = 'tahun_akademiks';

    protected $fillable = [
        'kode_tahun',
        'nama_tahun',
        'semester',
        'tgl_mulai',
        'tgl_selesai',
        'tgl_krs_mulai',
        'tgl_krs_selesai',
        'is_active',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'tgl_krs_mulai' => 'date',
        'tgl_krs_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function kelasKuliahs(): HasMany
    {
        return $this->hasMany(KelasKuliah::class, 'id_tahun_akademik');
    }

    public function krsList(): HasMany
    {
        return $this->hasMany(Krs::class, 'id_tahun_akademik');
    }
}
