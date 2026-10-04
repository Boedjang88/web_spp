<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswas';

    protected $fillable = [
        'nim',
        'nisn',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'id_prodi',
        'id_dosen_pa',
        'id_ukt',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'kode_pos',
        'nama_ayah',
        'nama_ibu_kandung',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'penghasilan_ortu',
        'asal_sekolah',
        'tahun_lulus_sekolah',
        'nomor_ijazah_sekolah',
        'no_telp',
        'email_pribadi',
        'no_hp_wali',
        'status_kelulusan',
        'tgl_kelulusan',
        'nomor_ijazah',
        'total_skpi_points',
        'consent_pdp_at',
        'consent_pdp_ip',
    ];

    protected $casts = [
        'nik' => 'encrypted',
        'nama_ibu_kandung' => 'encrypted',
        'no_hp_wali' => 'encrypted',
        'tanggal_lahir' => 'date',
        'consent_pdp_at' => 'datetime',
        'tgl_kelulusan' => 'datetime',
        'total_skpi_points' => 'integer',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'id_prodi');
    }

    public function dosenPa(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'id_dosen_pa');
    }

    public function ukt(): BelongsTo
    {
        return $this->belongsTo(Ukt::class, 'id_ukt');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id_mahasiswa');
    }

    public function krs(): HasMany
    {
        return $this->hasMany(Krs::class, 'id_siswa');
    }

    public function tagihanUkts(): HasMany
    {
        return $this->hasMany(TagihanUkt::class, 'id_mahasiswa');
    }

    public function pembayaranUkts(): HasMany
    {
        return $this->hasMany(PembayaranUkt::class, 'id_mahasiswa');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'id_mahasiswa');
    }

    public function presensiMahasiswas(): HasMany
    {
        return $this->hasMany(PresensiMahasiswa::class, 'id_mahasiswa');
    }

    public function earlyWarningLogs(): HasMany
    {
        return $this->hasMany(EarlyWarningLog::class, 'id_siswa');
    }

    public function skpiAktivitas(): HasMany
    {
        return $this->hasMany(SkpiAktivitas::class, 'id_siswa');
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(PresensiKuliah::class, 'id_siswa');
    }

    // Backward compatibility helper
    public function getNisAttribute()
    {
        return $this->nim;
    }
}
