<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratAkademik extends Model
{
    use HasFactory;

    protected $table = 'surat_akademiks';

    protected $fillable = [
        'id_siswa',
        'jenis_surat',
        'nomor_surat',
        'perihal',
        'keperluan',
        'qr_verification_token',
        'file_pdf_path',
        'status',
        'tgl_terbit',
    ];

    protected $casts = [
        'tgl_terbit' => 'datetime',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }
}
