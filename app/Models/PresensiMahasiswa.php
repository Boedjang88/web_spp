<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'presensi_mahasiswas';

    protected $fillable = [
        'id_mahasiswa',
        'id_kelas_kuliah',
        'id_bap',
        'waktu_hadir',
        'latitude',
        'longitude',
        'status',
        'device_fingerprint',
        'verified_at',
    ];

    protected $casts = [
        'waktu_hadir' => 'datetime',
        'verified_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }

    public function kelasKuliah(): BelongsTo
    {
        return $this->belongsTo(KelasKuliah::class, 'id_kelas_kuliah');
    }

    public function bap(): BelongsTo
    {
        return $this->belongsTo(BapPerkuliahan::class, 'id_bap');
    }
}
