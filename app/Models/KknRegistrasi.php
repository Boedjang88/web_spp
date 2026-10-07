<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KknRegistrasi extends Model
{
    use HasFactory;

    protected $table = 'kkn_registrasis';

    protected $fillable = [
        'id_siswa',
        'id_tahun_akademik',
        'id_dosen_dpl',
        'nama_kelompok',
        'desa_lokasi',
        'kecamatan',
        'kabupaten',
        'status_pendaftaran',
        'file_laporan_path',
        'nilai_angka',
        'nilai_huruf',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'id_tahun_akademik');
    }

    public function dpl(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'id_dosen_dpl');
    }
}
