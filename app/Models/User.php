<?php

namespace App\Models;

// Tambahkan import ini
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Implementasikan interface FilamentUser
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Tambahkan method ini untuk mengatur hak akses
    public function canAccessPanel(Panel $panel): bool
    {
        // Contoh 1: Izinkan semua user yang login (kurang aman jika registrasi terbuka untuk umum)
        // return true;

        // Contoh 2: Hanya izinkan user dengan role tertentu (Disarankan)
        // Sesuaikan dengan logic role di aplikasi Anda.
        // Berdasarkan migration Anda, default role adalah 'petugas'.
        return $this->role === 'admin' || $this->role === 'petugas';
    }
}