<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTelegramNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $chatId,
        public string $message,
        public string $parseMode = 'HTML'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $botToken = config('services.telegram.bot_token');

        if (empty($botToken)) {
            Log::info("SendTelegramNotificationJob: Bot token is not configured. Notification to {$this->chatId} skipped.");
            return;
        }

        try {
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            $response = Http::timeout(10)->post($url, [
                'chat_id' => $this->chatId,
                'text' => $this->message,
                'parse_mode' => $this->parseMode,
                'disable_web_page_preview' => true,
            ]);

            if (!$response->successful()) {
                Log::warning("Telegram notification failed for chat_id {$this->chatId}: " . $response->body());
            } else {
                Log::info("Telegram notification sent successfully to chat_id {$this->chatId}.");
            }
        } catch (\Throwable $e) {
            Log::error("Exception sending Telegram notification to {$this->chatId}: " . $e->getMessage());
            throw $e;
        }
    }
}
