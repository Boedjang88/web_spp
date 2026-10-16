<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportExportCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $reportTitle,
        public string $fileName,
        public string $filePath,
        public int $totalRecords,
        public ?string $downloadUrl = null
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->reportTitle,
            'file_name' => $this->fileName,
            'file_path' => $this->filePath,
            'total_records' => $this->totalRecords,
            'download_url' => $this->downloadUrl,
            'completed_at' => now()->toIso8601String(),
            'message' => "Ekspor '{$this->reportTitle}' berhasil diselesaikan dengan total {$this->totalRecords} baris data.",
        ];
    }
}
