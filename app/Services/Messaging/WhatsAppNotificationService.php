<?php

namespace App\Services\Messaging;

use App\Jobs\SendWhatsAppNotificationJob;
use App\Models\Siswa;

class WhatsAppNotificationService
{
    /**
     * Dispatch UKT Payment Reminder Notification
     */
    public function sendUktPaymentReminder(Siswa $siswa, int $nominalUkt, string $dueDate): void
    {
        $phone = $siswa->no_telp ?? '081234567890';
        $formattedNominal = 'Rp ' . number_format($nominalUkt, 0, ',', '.');
        $message = "Yth. Mahasiswa {$siswa->nama} (NIM: {$siswa->nisn}),\n\n"
                 . "Tagihan UKT Semester ini sebesar {$formattedNominal} akan jatuh tempo pada {$dueDate}.\n"
                 . "Harap segera melakukan pembayaran via Virtual Account Bank Mitra untuk menghindari pemblokiran KRS.\n\n"
                 . "Terima kasih,\nBAAK Universitas SIAKAD Enterprise";

        SendWhatsAppNotificationJob::dispatch($phone, $message, 'UKT_REMINDER');
    }

    /**
     * Dispatch 2FA Authentication OTP Code
     */
    public function sendTwoFactorOtp(string $phoneNumber, string $otpCode): void
    {
        $message = "[SIAKAD ENTERPRISE] Kode OTP Keamanan Anda adalah: *{$otpCode}*.\n"
                 . "Kode ini berlaku selama 5 menit. JANGAN BAGIKAN KODE INI KEPADA SIAPAPUN.";

        SendWhatsAppNotificationJob::dispatch($phoneNumber, $message, '2FA_OTP');
    }

    /**
     * Dispatch EWS (Early Warning System) Academic Risk Alert
     */
    public function sendEwsRiskAlert(Siswa $siswa, string $riskType, string $description): void
    {
        $phone = $siswa->no_telp ?? '081234567890';
        $message = "PERINGATAN AKADEMIK (EWS SIAKAD):\n"
                 . "Mahasiswa: {$siswa->nama} (NIM: {$siswa->nisn})\n"
                 . "Jenis Risiko: {$riskType}\n"
                 . "Detail: {$description}\n\n"
                 . "Harap segera menghadap Dosen Pembimbing Akademik (PA) untuk bimbingan konseling.";

        SendWhatsAppNotificationJob::dispatch($phone, $message, 'EWS_ALERT');
    }
}
