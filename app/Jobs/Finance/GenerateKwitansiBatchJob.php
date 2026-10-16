<?php

namespace App\Jobs\Finance;

use App\Models\AuditTrailLog;
use App\Models\Pembayaran;
use App\Models\User;
use App\Notifications\ReportExportCompletedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateKwitansiBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 180;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $pembayaranIds,
        public int $userId,
        public string $archiveName = ''
    ) {
        if (empty($this->archiveName)) {
            $this->archiveName = 'batch-kwitansi-' . date('Ymd-His') . '.json';
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Storage::disk('local')->makeDirectory('exports');
        $relativeFilePath = 'exports/' . $this->archiveName;
        $fullPath = Storage::disk('local')->path($relativeFilePath);

        $pembayarans = Pembayaran::with(['siswa.kelas', 'petugas', 'spp'])
            ->whereIn('id', $this->pembayaranIds)
            ->get();

        $batchData = [];
        foreach ($pembayarans as $p) {
            $batchData[] = [
                'id' => $p->id,
                'nomor_kuitansi' => 'KWT-' . str_pad((string) $p->id, 6, '0', STR_PAD_LEFT),
                'tanggal_bayar' => $p->tgl_bayar,
                'siswa' => [
                    'nisn' => $p->siswa?->nisn,
                    'nis' => $p->siswa?->nis,
                    'nama' => $p->siswa?->nama,
                    'kelas' => $p->siswa?->kelas?->nama_kelas,
                ],
                'bulan' => $p->bulan_dibayar,
                'tahun' => $p->tahun_dibayar,
                'jumlah_bayar' => $p->jumlah_bayar,
                'petugas' => $p->petugas?->name,
            ];
        }

        file_put_contents($fullPath, json_encode($batchData, JSON_PRETTY_PRINT));
        $totalRecords = count($batchData);

        $user = User::find($this->userId);
        if ($user) {
            $downloadUrl = route('web.laporan.exportDownload', ['filename' => $this->archiveName]);
            $user->notify(new ReportExportCompletedNotification(
                reportTitle: 'Batch Kwitansi Pembayaran',
                fileName: $this->archiveName,
                filePath: $relativeFilePath,
                totalRecords: $totalRecords,
                downloadUrl: $downloadUrl
            ));
        }

        AuditTrailLog::create([
            'id_user' => $this->userId,
            'action_event' => 'QUEUE_BATCH_KWITANSI_GENERATED',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Queue/Worker (Horizon)',
            'url_endpoint' => 'job://GenerateKwitansiBatchJob',
            'request_payload' => [
                'archive_name' => $this->archiveName,
                'total_records' => $totalRecords,
                'pembayaran_ids' => $this->pembayaranIds,
            ],
            'timestamp_microseconds' => sprintf('%.6f', microtime(true)),
            'created_at' => now(),
        ]);

        Log::info("GenerateKwitansiBatchJob completed. Total records: {$totalRecords}, File: {$this->archiveName}");
    }
}
