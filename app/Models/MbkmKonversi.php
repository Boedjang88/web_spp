<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MbkmKonversi extends Model
{
    use HasFactory;

    protected $table = 'mbkm_konversis';

    protected $fillable = [
        'id_siswa',
        'id_mk',
        'nama_program_eksternal',
        'mitra_mbkm',
        'sks_diakui',
        'nilai_angka',
        'nilai_huruf',
        'status_verifikasi',
        'pejabat_pengesah',
        'tgl_pengesahan',
    ];

    protected $casts = [
        'nilai_angka' => 'decimal:2',
        'tgl_pengesahan' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'id_mk');
    }

    public function details(): HasMany
    {
        return $this->hasMany(MbkmKonversiDetail::class, 'id_mbkm_konversi');
    }
}
