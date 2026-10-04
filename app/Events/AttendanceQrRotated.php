<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceQrRotated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $idBap;
    public ?int $idKelasKuliah;
    public string $qrToken;
    public int $expiresInSeconds;
    public string $timestamp;

    /**
     * Create a new event instance.
     */
    public function __construct(int $idBap, ?int $idKelasKuliah, string $qrToken, int $expiresInSeconds = 10)
    {
        $this->idBap = $idBap;
        $this->idKelasKuliah = $idKelasKuliah;
        $this->qrToken = $qrToken;
        $this->expiresInSeconds = $expiresInSeconds;
        $this->timestamp = now()->toIso8601String();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('attendance-qr.' . $this->idBap),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'qr.rotated';
    }

    /**
     * Data to broadcast with the event.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id_bap' => $this->idBap,
            'id_kelas_kuliah' => $this->idKelasKuliah,
            'qr_token' => $this->qrToken,
            'expires_in_seconds' => $this->expiresInSeconds,
            'timestamp' => $this->timestamp,
        ];
    }
}
