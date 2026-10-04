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
        'judul',
        'deskripsi',
        'attachment_path',
        'komponen_penilaian',
        'bobot_persen',
        'deadline_at',
        'allow_late_submission',
        'late_grace_minutes',
        'is_published',
        'is_anonymous_grading',
    ];

    protected $casts = [
        'bobot_persen' => 'decimal:2',
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
     * Determine if a submission timestamp is considered late.
     */
    public function isTimestampLate(Carbon $timestamp): bool
    {
        $cutoff = $this->deadline_at->copy()->addMinutes($this->late_grace_minutes);
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

        $cutoff = $this->deadline_at->copy()->addMinutes($this->late_grace_minutes);
        return $timestamp->lessThanOrEqualTo($cutoff);
    }
}
