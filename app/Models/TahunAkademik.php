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
        'tgl_nilai_mulai',
        'tgl_nilai_selesai',
        'is_active',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'tgl_krs_mulai' => 'date',
        'tgl_krs_selesai' => 'date',
        'tgl_nilai_mulai' => 'date',
        'tgl_nilai_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    public function isGradingOpen(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = now()->format('Y-m-d');

        if ($this->tgl_nilai_mulai && $today < $this->tgl_nilai_mulai->format('Y-m-d')) {
            return false;
        }

        if ($this->tgl_nilai_selesai && $today > $this->tgl_nilai_selesai->format('Y-m-d')) {
            return false;
        }

        return true;
    }

    public function isKrsOpen(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = now()->format('Y-m-d');

        if ($this->tgl_krs_mulai && $today < $this->tgl_krs_mulai->format('Y-m-d')) {
            return false;
        }

        if ($this->tgl_krs_selesai && $today > $this->tgl_krs_selesai->format('Y-m-d')) {
            return false;
        }

        return true;
    }

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
