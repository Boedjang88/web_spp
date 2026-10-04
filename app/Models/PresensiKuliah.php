<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiKuliah extends Model
{
    use HasFactory;

    protected $table = 'presensi_kuliahs';

    protected $fillable = [
        'id_bap',
        'id_siswa',
        'status_kehadiran',
        'submit_lat',
        'submit_long',
        'jarak_meter_dari_ruangan',
        'device_fingerprint',
        'waktu_scan',
    ];

    protected $casts = [
        'submit_lat' => 'decimal:7',
        'submit_long' => 'decimal:7',
        'waktu_scan' => 'datetime',
    ];

    public function bap(): BelongsTo
    {
        return $this->belongsTo(BapPerkuliahan::class, 'id_bap');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }
}
