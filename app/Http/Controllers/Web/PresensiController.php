<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PresensiController extends Controller
{
    public function index(Request $request): View
    {
        $selectedKelasId = $request->get('id_kelas');
        $selectedTanggal = $request->get('tanggal', now()->toDateString());

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $siswas = collect();

        if ($selectedKelasId) {
            $siswas = Siswa::where('id_kelas', $selectedKelasId)
                ->with(['presensis' => function ($q) use ($selectedTanggal) {
                    $q->where('tanggal', $selectedTanggal);
                }])
                ->orderBy('nama')
                ->get();
        }

        return view('presensi.index', compact('kelasList', 'selectedKelasId', 'selectedTanggal', 'siswas'));
    }

    public function storeBatch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_kelas' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.id_siswa' => 'required|exists:siswas,id',
            'presensi.*.status' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'presensi.*.keterangan' => 'nullable|string|max:255',
        ]);

        $count = 0;
        foreach ($validated['presensi'] as $item) {
            Presensi::updateOrCreate(
                [
                    'id_siswa' => $item['id_siswa'],
                    'tanggal' => $validated['tanggal'],
                ],
                [
                    'id_kelas' => $validated['id_kelas'],
                    'status' => $item['status'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]
            );
            $count++;
        }

        ActivityLog::record('PRESENSI_INPUT', "Input presensi kelas #{$validated['id_kelas']} untuk {$count} siswa pada tanggal {$validated['tanggal']}.");

        return redirect()->route('web.presensi.index', [
            'id_kelas' => $validated['id_kelas'],
            'tanggal' => $validated['tanggal'],
        ])->with('success', "Presensi {$count} siswa berhasil disimpan!");
    }
}
