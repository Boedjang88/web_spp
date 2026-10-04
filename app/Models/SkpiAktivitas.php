<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkpiAktivitas extends Model
{
    use HasFactory;

    protected $table = 'skpi_aktivitas';

    protected $fillable = [
        'id_siswa',
        'kategori',
        'nama_kegiatan_id',
        'nama_kegiatan_en',
        'penyelenggara',
        'tahun_kegiatan',
        'poin_sacs',
        'file_bukti_sertifikat',
        'status_verifikasi',
        'pejabat_verifikator',
        'tgl_verifikasi',
    ];

    protected $casts = [
        'tgl_verifikasi' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }
}
