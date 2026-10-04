<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasFactory;

    protected $table = 'submissions';

    protected $fillable = [
        'id_assignment',
        'id_siswa',
        'file_path',
        'original_filename',
        'file_mime',
        'file_size',
        'submitted_at',
        'submission_microtime',
        'submission_token',
        'device_fingerprint',
        'client_ip',
        'is_late',
        'nilai',
        'feedback',
        'graded_at',
        'graded_by_dosen',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'submission_microtime' => 'decimal:6',
        'is_late' => 'boolean',
        'nilai' => 'decimal:2',
        'graded_at' => 'datetime',
        'file_size' => 'integer',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'id_assignment');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function dosenPenilai(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'graded_by_dosen');
    }

    /**
     * Get masked student identity for anonymous grading mode.
     */
    public function getMaskedIdentityAttribute(): string
    {
        return 'ANON-STUDENT-' . strtoupper(substr(md5((string) $this->id_siswa), 0, 8));
    }
}
