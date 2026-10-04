<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Assignment extends Model
{
    use HasFactory;

    protected $table = 'assignments';

    protected $fillable = [
        'id_kelas_kuliah',
        'target_kelas_ids',
        'judul',
        'deskripsi',
        'file_path',
        'attachment_path',
        'komponen_penilaian',
        'bobot_persen',
        'bobot_nilai_bap',
        'deadline',
        'deadline_at',
        'allow_late_submission',
        'late_grace_minutes',
        'is_published',
        'is_anonymous_grading',
    ];

    protected $casts = [
        'target_kelas_ids' => 'array',
        'bobot_persen' => 'decimal:2',
        'bobot_nilai_bap' => 'decimal:2',
        'deadline' => 'datetime',
        'deadline_at' => 'datetime',
        'allow_late_submission' => 'boolean',
        'late_grace_minutes' => 'integer',
        'is_published' => 'boolean',
        'is_anonymous_grading' => 'boolean',
    ];

    public function kelasKuliah(): BelongsTo
    {
        return $this->belongsTo(KelasKuliah::class, 'id_kelas_kuliah');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'id_assignment');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Get effective deadline Carbon instance.
     */
    public function getEffectiveDeadlineAttribute(): ?Carbon
    {
        return $this->deadline ?? $this->deadline_at;
    }

    /**
     * Determine if a submission timestamp is considered late.
     */
    public function isTimestampLate(Carbon $timestamp): bool
    {
        $deadline = $this->effective_deadline ?? now();
        $cutoff = $deadline->copy()->addMinutes($this->late_grace_minutes ?? 0);
        return $timestamp->greaterThan($cutoff);
    }

    /**
     * Check if submissions are currently accepted.
     */
    public function canAcceptSubmission(Carbon $timestamp): bool
    {
        if (!$this->is_published) {
            return false;
        }

        if ($this->allow_late_submission) {
            return true;
        }

        $deadline = $this->effective_deadline ?? now();
        $cutoff = $deadline->copy()->addMinutes($this->late_grace_minutes ?? 0);
        return $timestamp->lessThanOrEqualTo($cutoff);
    }
}
