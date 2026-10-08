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
        'lokasi_kkn',
        'kelompok',
    ];

    public function getLokasiKknAttribute()
    {
        return $this->desa_lokasi;
    }

    public function setLokasiKknAttribute($value)
    {
        $this->attributes['desa_lokasi'] = $value;
    }

    public function getKelompokAttribute()
    {
        return $this->nama_kelompok;
    }

    public function setKelompokAttribute($value)
    {
        $this->attributes['nama_kelompok'] = $value;
    }

    public function getDplNameAttribute()
    {
        return $this->attributes['dpl_name'] ?? 'Dr. Ahmad Fauzi, M.T.';
    }

    public function setDplNameAttribute($value)
    {
        $this->attributes['dpl_name'] = $value;
    }

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
