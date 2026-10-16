<?php

namespace App\Jobs\Finance;

use App\Models\AuditTrailLog;
use App\Models\Pembayaran;
use App\Models\User;
use App\Notifications\ReportExportCompletedNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExportPembayaranReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $filters,
        public int $userId,
        public string $fileName = ''
    ) {
        if (empty($this->fileName)) {
            $this->fileName = 'laporan-pembayaran-spp-' . date('Ymd-His') . '.csv';
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $query = Pembayaran::with(['petugas', 'siswa.kelas', 'spp']);

        if (!empty($this->filters['start_date'])) {
            $query->whereDate('tgl_bayar', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('tgl_bayar', '<=', $this->filters['end_date']);
        }
        if (!empty($this->filters['id_kelas'])) {
            $query->whereHas('siswa', function ($q) {
                $q->where('id_kelas', $this->filters['id_kelas']);
            });
        }
        if (!empty($this->filters['id_petugas'])) {
            $query->where('id_petugas', $this->filters['id_petugas']);
        }

        $query->orderBy('tgl_bayar', 'asc');

        // Ensure storage directory exists
        Storage::disk('local')->makeDirectory('exports');
        $relativeFilePath = 'exports/' . $this->fileName;
        $fullPath = Storage::disk('local')->path($relativeFilePath);

        $handle = fopen($fullPath, 'w');
        if (!$handle) {
            throw new Exception("Gagal membuka file export: {$fullPath}");
        }

        // CSV Header
        fputcsv($handle, [
            'No Kwitansi',
            'Tanggal Bayar',
            'NISN',
            'NIS',
            'Nama Siswa',
            'Kelas',
            'Periode SPP',
            'Petugas',
            'Jumlah Bayar (Rp)'
        ]);

        $totalRecords = 0;

        // Process in memory-efficient chunks
        $query->chunk(500, function ($pembayarans) use ($handle, &$totalRecords) {
            foreach ($pembayarans as $p) {
                fputcsv($handle, [
                    'KWT-' . str_pad((string) $p->id, 6, '0', STR_PAD_LEFT),
                    $p->tgl_bayar,
                    $p->siswa?->nisn,
                    $p->siswa?->nis,
                    $p->siswa?->nama,
                    $p->siswa?->kelas?->nama_kelas,
                    $p->bulan_dibayar . ' ' . $p->tahun_dibayar,
                    $p->petugas?->name,
                    $p->jumlah_bayar,
                ]);
                $totalRecords++;
            }
        });

        fclose($handle);

        // Notify the user who initiated the export
        $user = User::find($this->userId);
        if ($user) {
            $downloadUrl = route('web.laporan.exportDownload', ['filename' => $this->fileName]);
            $user->notify(new ReportExportCompletedNotification(
                reportTitle: 'Rekap Pembayaran SPP',
                fileName: $this->fileName,
                filePath: $relativeFilePath,
                totalRecords: $totalRecords,
                downloadUrl: $downloadUrl
            ));
        }

        // Record immutable audit trail
        AuditTrailLog::create([
            'id_user' => $this->userId,
            'action_event' => 'QUEUE_REPORT_EXPORT_COMPLETED',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Queue/Worker (Horizon)',
            'url_endpoint' => 'job://ExportPembayaranReportJob',
            'request_payload' => [
                'file_name' => $this->fileName,
                'total_records' => $totalRecords,
                'filters' => $this->filters,
            ],
            'timestamp_microseconds' => sprintf('%.6f', microtime(true)),
            'created_at' => now(),
        ]);

        Log::info("ExportPembayaranReportJob completed successfully. Total records: {$totalRecords}, File: {$this->fileName}");
    }
}
