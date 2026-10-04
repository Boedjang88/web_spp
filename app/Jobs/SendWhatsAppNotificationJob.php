<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(
        public string $phoneNumber,
        public string $message,
        public string $notificationType = 'GENERAL'
    ) {}

    public function handle(): void
    {
        $apiUrl = config('services.whatsapp.url', 'https://api.whatsapp-gateway.campus.ac.id/send');
        $apiKey = config('services.whatsapp.key', 'WA_SECRET_API_KEY_123');

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'target' => $this->phoneNumber,
                'message' => $this->message,
                'type' => $this->notificationType,
            ]);

            if ($response->failed()) {
                Log::warning("WhatsApp Notification failed [{$this->notificationType}] to {$this->phoneNumber}: " . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error("WhatsApp Notification exception [{$this->notificationType}]: " . $e->getMessage());
            throw $e;
        }
    }
}
