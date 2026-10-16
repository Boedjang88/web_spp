<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'telegram_chat_id',
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

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_petugas');
    }

    public function getStudentIdAttribute(): ?int
    {
        return $this->id_siswa ?? $this->id_mahasiswa;
    }

    protected static function booted(): void
    {
        static::saving(function ($user) {
            // Auto-bridge dual-domain student references if one is missing
            if ($user->isMahasiswa()) {
                if ($user->id_siswa && !$user->id_mahasiswa) {
                    $siswa = Siswa::find($user->id_siswa);
                    if ($siswa) {
                        $m = Mahasiswa::where('nim', $siswa->nisn)->orWhere('nama', $siswa->nama)->first();
                        if ($m) {
                            $user->id_mahasiswa = $m->id;
                        }
                    }
                } elseif ($user->id_mahasiswa && !$user->id_siswa) {
                    $mahasiswa = Mahasiswa::find($user->id_mahasiswa);
                    if ($mahasiswa) {
                        $s = Siswa::where('nisn', $mahasiswa->nim)->orWhere('nama', $mahasiswa->nama)->first();
                        if ($s) {
                            $user->id_siswa = $s->id;
                        }
                    }
                }
            }
        });

        static::deleting(function ($user) {
            if ($user->pembayarans()->exists()) {
                throw new \DomainException("Petugas/User '{$user->name}' tidak dapat dihapus karena memiliki riwayat transaksi pembayaran.");
            }
        });
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

    public function isWali(): bool
    {
        return in_array($this->role, ['wali', 'parent', 'orang_tua']);
    }

    public function isParent(): bool
    {
        return $this->isWali();
    }

    public function canManageUsers(): bool
    {
        return in_array($this->role, ['superadmin', 'super_admin', 'baak', 'admin']);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'parent') {
            return $this->isWali() || $this->isSuperAdmin() || $this->isAdmin();
        }

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