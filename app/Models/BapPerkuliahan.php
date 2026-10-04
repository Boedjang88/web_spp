<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BapPerkuliahan extends Model
{
    use HasFactory;

    protected $table = 'bap_perkuliahans';

    protected $fillable = [
        'id_kelas_kuliah',
        'id_guru',
        'id_ruangan',
        'pertemuan_ke',
        'tanggal_pelaksanaan',
        'jam_mulai_real',
        'jam_selesai_real',
        'materi_pembahasan',
        'catatan_dosen',
        'total_mahasiswa_hadir',
        'total_mahasiswa_absen',
        'status_verifikasi',
        'digital_signature_hash',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
    ];

    public function kelasKuliah(): BelongsTo
    {
        return $this->belongsTo(KelasKuliah::class, 'id_kelas_kuliah');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan');
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(PresensiKuliah::class, 'id_bap');
    }
}
