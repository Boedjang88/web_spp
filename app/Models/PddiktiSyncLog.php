<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PddiktiSyncLog extends Model
{
    use HasFactory;

    protected $table = 'pddikti_sync_logs';

    protected $fillable = [
        'tipe_entitas',
        'id_entitas_lokal',
        'id_feeder_pddikti',
        'status_sync',
        'payload_terkirim',
        'response_feeder',
        'pesan_error',
        'synced_at',
    ];

    protected $casts = [
        'payload_terkirim' => 'array',
        'response_feeder' => 'array',
        'synced_at' => 'datetime',
    ];
}
