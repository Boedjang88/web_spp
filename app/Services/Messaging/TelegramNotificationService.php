<?php

namespace App\Services\Messaging;

use App\Jobs\SendTelegramNotificationJob;
use App\Models\Siswa;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    /**
     * Send arbitrary Telegram notification via queue.
     */
    public function sendMessage(string $chatId, string $message, string $parseMode = 'HTML', bool $async = true): bool
    {
        if (empty($chatId)) {
            Log::warning('TelegramNotificationService: No valid chatId provided.');
            return false;
        }

        if ($async) {
            SendTelegramNotificationJob::dispatch($chatId, $message, $parseMode);
        } else {
            SendTelegramNotificationJob::dispatchSync($chatId, $message, $parseMode);
        }

        return true;
    }

    /**
     * Resolve destination Telegram Chat ID for a given student.
     */
    public function resolveChatId(Siswa $siswa, ?string $chatId = null): ?string
    {
        if (!empty($chatId)) {
            return (string) $chatId;
        }

        // Student's linked user account
        if ($siswa->user && !empty($siswa->user->telegram_chat_id)) {
            return (string) $siswa->user->telegram_chat_id;
        }

        // Default fallback channel / chat ID from config
        return config('services.telegram.default_chat_id');
    }

    /**
     * Send tuition / UKT billing reminder to student or guardian.
     */
    public function sendBillingReminder(Siswa $siswa, float $amount, string $dueDate, ?string $chatId = null, bool $async = true): bool
    {
        $targetChatId = $this->resolveChatId($siswa, $chatId);
        if (!$targetChatId) {
            return false;
        }

        $formattedAmount = 'Rp ' . number_format($amount, 0, ',', '.');
        $nama = htmlspecialchars($siswa->nama, ENT_QUOTES, 'UTF-8');
        $nisn = htmlspecialchars($siswa->nisn ?? $siswa->nis ?? '-', ENT_QUOTES, 'UTF-8');

        $message = "📢 <b>Pemberitahuan Tagihan Pembayaran SPP/UKT</b>\n\n"
            . "Yth. Sdr/i <b>{$nama}</b> ({$nisn}),\n\n"
            . "Diinformasikan bahwa Anda memiliki kewajiban pembayaran perkuliahan sebagai berikut:\n"
            . "💰 <b>Jumlah Tagihan:</b> {$formattedAmount}\n"
            . "📅 <b>Batas Akhir (Jatuh Tempo):</b> {$dueDate}\n\n"
            . "Pembayaran dapat dilakukan melalui Virtual Account Bank Mitra atau Payment Gateway yang tersedia pada portal akademik.\n"
            . "Abaikan pesan ini apabila Anda telah menyelesaikan pembayaran.\n\n"
            . "<i>Sistem Informasi Akademik & Keuangan Terpadu</i>";

        return $this->sendMessage($targetChatId, $message, 'HTML', $async);
    }

    /**
     * Send academic schedule alert (lectures, exams, thesis defense).
     */
    public function sendScheduleAlert(Siswa $siswa, string $courseName, string $dateTime, string $room, ?string $chatId = null, bool $async = true): bool
    {
        $targetChatId = $this->resolveChatId($siswa, $chatId);
        if (!$targetChatId) {
            return false;
        }

        $nama = htmlspecialchars($siswa->nama, ENT_QUOTES, 'UTF-8');
        $course = htmlspecialchars($courseName, ENT_QUOTES, 'UTF-8');
        $roomEscaped = htmlspecialchars($room, ENT_QUOTES, 'UTF-8');

        $message = "🗓️ <b>Pengingat Jadwal Perkuliahan / Akademik</b>\n\n"
            . "Halo <b>{$nama}</b>,\n\n"
            . "Berikut adalah jadwal akademik Anda yang akan datang:\n"
            . "📚 <b>Mata Kuliah / Kegiatan:</b> {$course}\n"
            . "⏰ <b>Waktu:</b> {$dateTime}\n"
            . "📍 <b>Ruang / Lokasi:</b> {$roomEscaped}\n\n"
            . "Mohon hadir tepat waktu dan lakukan presensi kehadiran melalui SIAKAD Mobile / Portal.\n\n"
            . "<i>Biro Administrasi Akademik & Kemahasiswaan</i>";

        return $this->sendMessage($targetChatId, $message, 'HTML', $async);
    }

    /**
     * Send Early Warning System (EWS) predictive dropout risk alert to student, advisor, or guardian.
     */
    public function sendEwsRiskAlert(Siswa $siswa, int $riskScore, string $riskCategory, ?string $chatId = null, bool $async = true): bool
    {
        $targetChatId = $this->resolveChatId($siswa, $chatId);
        if (!$targetChatId) {
            return false;
        }

        $nama = htmlspecialchars($siswa->nama, ENT_QUOTES, 'UTF-8');
        $nisn = htmlspecialchars($siswa->nisn ?? $siswa->nis ?? '-', ENT_QUOTES, 'UTF-8');
        $badge = match ($riskCategory) {
            'CRITICAL' => '🚨 <b>KRITIS</b>',
            'HIGH' => '⚠️ <b>TINGGI</b>',
            'MEDIUM' => '⚡ <b>SEDANG</b>',
            default => 'ℹ️ <b>RENDAH</b>',
        };

        $message = "⚠️ <b>Notifikasi Early Warning System (EWS) Akademik</b>\n\n"
            . "Yth. Mahasiswa/i <b>{$nama}</b> ({$nisn}),\n\n"
            . "Sistem mendeteksi indikator penurunan performa akademik Anda:\n"
            . "📊 <b>Skor Risiko Dropout:</b> {$riskScore} / 100\n"
            . "🔴 <b>Tingkat Risiko:</b> {$badge}\n\n"
            . "Faktor penilaian mencakup tingkat kehadiran kuliah, tren IPK/IPS, dan status kewajiban finansial.\n"
            . "Diharapkan segera berkonsultasi dengan Dosen Pembimbing Akademik (PA) Anda untuk pendampingan.\n\n"
            . "<i>Pusat Layanan Akademik & Konseling Mahasiswa</i>";

        return $this->sendMessage($targetChatId, $message, 'HTML', $async);
    }
}
