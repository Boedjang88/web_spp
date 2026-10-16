<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EarlyWarningLog;
use App\Models\Siswa;
use App\Services\Academic\EarlyWarningService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EarlyWarningSystemController extends Controller
{
    public function index(Request $request): View
    {
        $severityFilter = $request->get('severity');

        $warnings = EarlyWarningLog::with(['siswa.kelas', 'dosenPa'])
            ->when($severityFilter, fn($q) => $q->where('severity', $severityFilter))
            ->latest()
            ->paginate(15);

        $totalCritical = EarlyWarningLog::where('severity', 'CRITICAL')->count();
        $totalHigh = EarlyWarningLog::where('severity', 'HIGH')->count();
        $totalMedium = EarlyWarningLog::where('severity', 'MEDIUM')->count();

        return view('siakad.baak.ews-index', compact('warnings', 'totalCritical', 'totalHigh', 'totalMedium', 'severityFilter'));
    }

    public function runScan(EarlyWarningService $ewsService)
    {
        $totalActive = Siswa::where('status_kelulusan', 'Aktif')->count();

        if ($totalActive > 50) {
            Siswa::where('status_kelulusan', 'Aktif')->chunkById(100, function ($students) {
                foreach ($students as $siswa) {
                    \App\Jobs\AnalyzeStudentDropoutRisk::dispatch($siswa->id);
                }
            });

            return redirect()->back()->with('success', "Pemindaian Early Warning System (EWS) untuk {$totalActive} mahasiswa sedang diproses di latar belakang.");
        }

        $students = Siswa::where('status_kelulusan', 'Aktif')->get();
        $count = 0;

        foreach ($students as $siswa) {
            $created = $ewsService->analyzeStudent($siswa);
            $count += count($created);
        }

        return redirect()->back()->with('success', "Pemindaian Early Warning System (EWS) selesai! {$count} peringatan risiko DO terdeteksi.");
    }
}
