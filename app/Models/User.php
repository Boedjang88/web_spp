<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'id_guru',
        'id_siswa',
        'id_dosen',
        'id_mahasiswa',
        'is_active',
        'mfa_secret',
        'mfa_enabled',
        'sso_provider',
        'sso_provider_id',
        'avatar_path',
        'consent_pdp_at',
        'consent_pdp_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'mfa_enabled' => 'boolean',
            'consent_pdp_at' => 'datetime',
        ];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'id_dosen');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }

    public function isSuperAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'super_admin']);
    }

    public function isBaak(): bool
    {
        return in_array($this->role, ['superadmin', 'super_admin', 'baak', 'admin', 'admin_akademik', 'petugas']);
    }

    public function isAdmin(): bool
    {
        return $this->isBaak();
    }

    public function isDosen(): bool
    {
        return in_array($this->role, ['dosen', 'guru', 'lecturer']);
    }

    public function isGuru(): bool
    {
        return $this->isDosen();
    }

    public function isMahasiswa(): bool
    {
        return in_array($this->role, ['mahasiswa', 'siswa', 'student']);
    }

    public function isSiswa(): bool
    {
        return $this->isMahasiswa();
    }

    public function canManageUsers(): bool
    {
        return in_array($this->role, ['superadmin', 'super_admin', 'baak', 'admin']);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['superadmin', 'super_admin', 'baak', 'admin', 'petugas']);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_path && file_exists(public_path($this->avatar_path))) {
            return asset($this->avatar_path);
        }
        return null;
    }
}