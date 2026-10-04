<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KelasKuliah extends Model
{
    use HasFactory;

    protected $table = 'kelas_kuliahs';

    protected $fillable = [
        'id_mk',
        'id_tahun_akademik',
        'nama_kelas',
        'kuota_maksimal',
        'total_terisi',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'id_mk');
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }

    public function dosens(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'dosen_kelas', 'id_kelas_kuliah', 'id_guru')
            ->withPivot('is_koordinator', 'persentase_mengajar')
            ->withTimestamps();
    }

    public function jadwalKuliahs(): HasMany
    {
        return $this->hasMany(JadwalKuliah::class, 'id_kelas_kuliah');
    }

    public function krsDetails(): HasMany
    {
        return $this->hasMany(KrsDetail::class, 'id_kelas_kuliah');
    }

    public function bobotPenilaian(): HasOne
    {
        return $this->hasOne(BobotPenilaian::class, 'id_kelas_kuliah');
    }

    public function baps(): HasMany
    {
        return $this->hasMany(BapPerkuliahan::class, 'id_kelas_kuliah');
    }

    public function getSisaKuotaAttribute(): int
    {
        return max(0, $this->kuota_maksimal - $this->total_terisi);
    }
}
