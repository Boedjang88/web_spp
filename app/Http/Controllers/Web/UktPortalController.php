<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\PembayaranUkt;
use App\Models\TahunAkademik;
use App\Services\Finance\UktBillingService;
use Illuminate\Http\Request;

class UktPortalController extends Controller
{
    public function __construct(
        protected UktBillingService $billingService
    ) {}

    /**
     * Display student UKT billing workspace
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $studentId = $user->id_mahasiswa ?? $user->id_siswa;
        $mahasiswa = Mahasiswa::with(['prodi.fakultas', 'ukt'])->findOrFail($studentId);

        $activeTa = TahunAkademik::where('is_active', true)->first()
            ?? TahunAkademik::latest()->first();

        $tagihan = $this->billingService->generateSemesterBill($mahasiswa, $activeTa);
        $riwayatPembayaran = PembayaranUkt::where('id_mahasiswa', $mahasiswa->id)
            ->orderBy('tgl_bayar', 'desc')
            ->get();

        return view('siakad.ukt.index', compact('mahasiswa', 'activeTa', 'tagihan', 'riwayatPembayaran'));
    }

    /**
     * Print official UKT payment receipt with Terbilang and verification QR code
     */
    public function cetakKwitansi(Request $request, $id)
    {
        $user = $request->user();
        $pembayaran = PembayaranUkt::with(['mahasiswa.prodi.fakultas', 'tagihanUkt.tahunAkademik', 'user'])->findOrFail($id);

        // Anti-IDOR check for student role
        if ($user->isMahasiswa()) {
            $studentId = $user->id_mahasiswa ?? $user->id_siswa;
            if ((int) $studentId !== (int) $pembayaran->id_mahasiswa) {
                abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses untuk mengunduh kwitansi mahasiswa lain.');
            }
        }

        $terbilang = $pembayaran->terbilang;
        $verificationPayload = json_encode([
            'kuitansi' => $pembayaran->nomor_kuitansi,
            'nim' => $pembayaran->mahasiswa?->nim,
            'nama' => $pembayaran->mahasiswa?->nama,
            'nominal' => $pembayaran->jumlah_bayar,
            'waktu' => $pembayaran->tgl_bayar->format('Y-m-d H:i:s'),
            'verified' => true,
        ]);

        return view('siakad.ukt.kwitansi', compact('pembayaran', 'terbilang', 'verificationPayload'));
    }
}
