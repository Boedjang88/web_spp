<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\Submission;
use App\Services\Security\UploadSecurityGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentAssignmentController extends Controller
{
    public function __construct(
        protected UploadSecurityGateway $securityGateway
    ) {}

    /**
     * Display student LMS assignments dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $studentId = $user->id_mahasiswa ?? $user->id_siswa;
        $mahasiswa = Mahasiswa::with(['prodi.fakultas'])->findOrFail($studentId);

        // Get enrolled classes from approved KRS
        $krs = Krs::with('details.kelasKuliah.mataKuliah')
            ->where(function ($q) use ($mahasiswa, $user) {
                $q->where('id_siswa', $mahasiswa->id);
                if ($user->id_siswa) {
                    $q->orWhere('id_siswa', $user->id_siswa);
                }
            })
            ->where('status_krs', 'Disetujui')
            ->latest()
            ->first();

        $enrolledKelasIds = $krs?->details->pluck('id_kelas_kuliah')->filter()->values()->toArray() ?? [];

        // All assignments for enrolled classes
        $assignments = Assignment::with(['kelasKuliah.mataKuliah', 'kelasKuliah.dosen', 'submissions' => function ($q) use ($mahasiswa) {
                $q->where('id_mahasiswa', $mahasiswa->id)->orWhere('id_siswa', $mahasiswa->id);
            }])
            ->where(function ($q) use ($enrolledKelasIds) {
                $q->whereIn('id_kelas_kuliah', $enrolledKelasIds);
                foreach ($enrolledKelasIds as $cid) {
                    $q->orWhereJsonContains('target_kelas_ids', $cid);
                }
            })
            ->where('is_published', true)
            ->orderBy('deadline_at', 'asc')
            ->get();

        // Categorize
        $pendingAssignments = $assignments->filter(fn ($a) => $a->submissions->isEmpty() && (!$a->deadline_at || $a->deadline_at->isFuture()));
        $submittedAssignments = $assignments->filter(fn ($a) => $a->submissions->isNotEmpty() && is_null($a->submissions->first()->nilai));
        $gradedAssignments = $assignments->filter(fn ($a) => $a->submissions->isNotEmpty() && !is_null($a->submissions->first()->nilai));
        $overdueAssignments = $assignments->filter(fn ($a) => $a->submissions->isEmpty() && $a->deadline_at && $a->deadline_at->isPast());

        return view('siakad.tugas.index', compact(
            'mahasiswa',
            'assignments',
            'pendingAssignments',
            'submittedAssignments',
            'gradedAssignments',
            'overdueAssignments'
        ));
    }

    /**
     * Display assignment detail and submission portal
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $studentId = $user->id_mahasiswa ?? $user->id_siswa;
        $mahasiswa = Mahasiswa::findOrFail($studentId);

        $assignment = Assignment::with(['kelasKuliah.mataKuliah', 'kelasKuliah.dosen'])->findOrFail($id);

        $submission = Submission::where('id_assignment', $assignment->id)
            ->where(function ($q) use ($mahasiswa) {
                $q->where('id_mahasiswa', $mahasiswa->id)->orWhere('id_siswa', $mahasiswa->id);
            })
            ->first();

        return view('siakad.tugas.show', compact('mahasiswa', 'assignment', 'submission'));
    }

    /**
     * Submit assignment solution with file upload security check
     */
    public function submit(Request $request, $id)
    {
        $user = $request->user();
        $studentId = $user->id_mahasiswa ?? $user->id_siswa;
        $mahasiswa = Mahasiswa::findOrFail($studentId);

        $assignment = Assignment::findOrFail($id);

        $request->validate([
            'file_tugas' => 'required|file|max:10240', // 10MB max
            'catatan' => 'nullable|string|max:1000',
        ]);

        $file = $request->file('file_tugas');

        try {
            // Verify binary headers, scan for webshells, and store securely
            $storedFile = $this->securityGateway->inspectAndStore($file, "lms/submissions/{$assignment->id}");
        } catch (\Exception $e) {
            return back()->with('error', 'Unggah Berkas Ditolak: ' . $e->getMessage());
        }

        $now = now();
        $microtimeNow = microtime(true);
        $clientIp = $request->ip() ?? '127.0.0.1';
        $userAgent = $request->userAgent() ?? 'Web';
        $deviceFingerprint = hash('sha256', $clientIp . '|' . $userAgent);
        $submissionToken = 'SUB-' . strtoupper(Str::random(12));

        $isLate = false;
        if ($assignment->effective_deadline) {
            $isLate = $now->greaterThan($assignment->effective_deadline);
        }

        Submission::updateOrCreate(
            [
                'id_assignment' => $assignment->id,
                'id_mahasiswa' => $mahasiswa->id,
            ],
            [
                'id_siswa' => $mahasiswa->id,
                'file_path' => $storedFile['path'],
                'original_filename' => $storedFile['original_name'],
                'file_mime' => $storedFile['mime'],
                'file_size' => $storedFile['size'],
                'submitted_at' => $now,
                'submission_microtime' => $microtimeNow,
                'submission_token' => $submissionToken,
                'hash_receipt' => $storedFile['hash_receipt'],
                'device_fingerprint' => $deviceFingerprint,
                'client_ip' => $clientIp,
                'is_late' => $isLate,
                'feedback' => $request->input('catatan'),
            ]
        );

        return back()->with('success', "Tugas berhasil dikumpulkan! Bukti Tanda Terima (SHA-256 Receipt): {$storedFile['hash_receipt']}");
    }
}
