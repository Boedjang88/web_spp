<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Krs extends Model
{
    use HasFactory;

    protected $table = 'krs';

    protected $fillable = [
        'id_siswa',
        'id_tahun_akademik',
        'id_dosen_wali',
        'max_sks_diizinkan',
        'total_sks_diambil',
        'ips_lalu',
        'status_krs',
        'catatan_pembimbing',
        'tgl_pengajuan',
        'tgl_persetujuan',
    ];

    protected $casts = [
        'ips_lalu' => 'decimal:2',
        'tgl_pengajuan' => 'datetime',
        'tgl_persetujuan' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }

    public function dosenWali(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_dosen_wali');
    }

    public function details(): HasMany
    {
        return $this->hasMany(KrsDetail::class, 'id_krs');
    }
}
