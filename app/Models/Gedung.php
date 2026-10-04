<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gedung extends Model
{
    use HasFactory;

    protected $table = 'gedungs';

    protected $fillable = [
        'kode_gedung',
        'nama_gedung',
        'latitude',
        'longitude',
    ];

    public function ruangans(): HasMany
    {
        return $this->hasMany(Ruangan::class, 'id_gedung');
    }
}
