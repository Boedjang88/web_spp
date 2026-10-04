<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosens';

    protected $fillable = [
        'nidn',
        'nidk',
        'nip',
        'nama_dosen',
        'gelar_depan',
        'gelar_belakang',
        'jenis_kelamin',
        'no_telp',
        'email',
        'alamat',
        'id_prodi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'id_prodi');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id_dosen');
    }

    public function kelasKuliahs(): BelongsToMany
    {
        return $this->belongsToMany(KelasKuliah::class, 'dosen_kelas', 'id_guru', 'id_kelas_kuliah')
            ->withPivot('is_koordinator', 'persentase_mengajar')
            ->withTimestamps();
    }

    public function baps(): HasMany
    {
        return $this->hasMany(BapPerkuliahan::class, 'id_guru');
    }

    public function mahasiswaBimbingan(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'id_dosen_pa');
    }

    public function getNamaLengkapGelarAttribute(): string
    {
        $depan = $this->gelar_depan ? trim($this->gelar_depan) . ' ' : '';
        $belakang = $this->gelar_belakang ? ', ' . trim($this->gelar_belakang) : '';
        return $depan . $this->nama_dosen . $belakang;
    }
}
