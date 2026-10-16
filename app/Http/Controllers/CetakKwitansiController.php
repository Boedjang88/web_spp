<?php

namespace App\Http\Controllers;

use App\Jobs\Finance\GenerateKwitansiBatchJob;
use App\Models\Pembayaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CetakKwitansiController extends Controller
{
    /**
     * Handle kwitansi printing or queued background batch generation.
     */
    public function __invoke(Request $request, $id): View|JsonResponse
    {
        // Support background queue generation if requested via async query parameter
        if ($request->boolean('async')) {
            $userId = auth()->id() ?? 1;
            $archiveName = 'kwitansi-' . $id . '-' . date('Ymd-His') . '.json';
            GenerateKwitansiBatchJob::dispatch([(int) $id], $userId, $archiveName);

            return response()->json([
                'success' => true,
                'message' => 'Proses generate kwitansi telah dijadwalkan di antrean sistem (Queue/Horizon).',
                'archive_name' => $archiveName,
            ], 202);
        }

        // Cari data pembayaran, kalau tidak ada error 404
        $pembayaran = Pembayaran::with(['siswa.kelas', 'petugas', 'spp'])->findOrFail($id);

        // Tampilkan view kwitansi
        return view('kwitansi', compact('pembayaran'));
    }

    /**
     * Dispatch multiple receipt generation to background queue.
     */
    public function batchQueue(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:pembayarans,id',
        ]);

        $ids = $request->input('ids');
        $userId = auth()->id() ?? 1;
        $archiveName = 'batch-kwitansi-' . date('Ymd-His') . '.json';

        GenerateKwitansiBatchJob::dispatch($ids, $userId, $archiveName);

        return response()->json([
            'success' => true,
            'message' => 'Batch kwitansi (' . count($ids) . ' transaksi) sedang diproses di antrean background.',
            'archive_name' => $archiveName,
        ], 202);
    }
}