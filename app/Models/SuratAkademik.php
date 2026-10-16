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
        'digital_signature_hash',
        'signature_provider',
        'signer_name',
        'signed_at',
        'file_pdf_path',
        'status',
        'tgl_terbit',
    ];

    protected $casts = [
        'tgl_terbit' => 'datetime',
        'signed_at' => 'datetime',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function isDigitallySigned(): bool
    {
        return !empty($this->digital_signature_hash);
    }
}
