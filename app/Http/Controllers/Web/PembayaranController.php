<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pembayaran::with(['petugas', 'siswa.kelas', 'spp']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('bulan_dibayar')) {
            $query->where('bulan_dibayar', $request->bulan_dibayar);
        }

        if ($request->filled('tahun_dibayar')) {
            $query->where('tahun_dibayar', $request->tahun_dibayar);
        }

        $pembayarans = $query->orderBy('tgl_bayar', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('pembayaran.index', compact('pembayarans'));
    }

    public function create(Request $request): View
    {
        $selectedSiswaId = $request->get('id_siswa');
        $siswas = Siswa::with(['kelas', 'spp'])->orderBy('nama')->get();
        $selectedSiswa = $selectedSiswaId ? Siswa::with(['kelas', 'spp'])->find($selectedSiswaId) : null;

        $daftarBulan = [
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
        ];

        return view('pembayaran.create', compact('siswas', 'selectedSiswa', 'daftarBulan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:siswas,id',
            'tgl_bayar' => 'required|date',
            'bulan_dibayar' => 'required', // can be array or string
            'tahun_dibayar' => 'required|integer|digits:4',
            'jumlah_bayar' => 'nullable|numeric|min:0',
        ], [
            'id_siswa.required' => 'Siswa wajib dipilih.',
            'tgl_bayar.required' => 'Tanggal bayar wajib diisi.',
            'bulan_dibayar.required' => 'Bulan yang dibayar wajib dipilih.',
            'tahun_dibayar.required' => 'Tahun yang dibayar wajib diisi.',
        ]);

        $siswa = Siswa::with('spp')->findOrFail($validated['id_siswa']);
        $bulanList = is_array($validated['bulan_dibayar']) ? $validated['bulan_dibayar'] : [$validated['bulan_dibayar']];
        $idSpp = $siswa->id_spp;
        $spp = Spp::find($idSpp);
        $nominalPerBulan = $spp ? $spp->nominal : 0;

        $createdPayments = [];
        $skippedMonths = [];

        foreach ($bulanList as $bulan) {
            // Check duplicate payment
            $existing = Pembayaran::where('id_siswa', $siswa->id)
                ->where('bulan_dibayar', $bulan)
                ->where('tahun_dibayar', (string) $validated['tahun_dibayar'])
                ->first();

            if ($existing) {
                $skippedMonths[] = $bulan;
                continue;
            }

            $createdPayments[] = Pembayaran::create([
                'id_petugas' => Auth::id() ?? 1,
                'id_siswa' => $siswa->id,
                'tgl_bayar' => $validated['tgl_bayar'],
                'bulan_dibayar' => $bulan,
                'tahun_dibayar' => (string) $validated['tahun_dibayar'],
                'id_spp' => $idSpp,
                'jumlah_bayar' => $nominalPerBulan,
            ]);
        }

        if (empty($createdPayments)) {
            return back()->withInput()->with('error', "Semua bulan yang dipilih (" . implode(', ', $skippedMonths) . ") sudah pernah dibayar sebelumnya!");
        }

        $totalNominal = count($createdPayments) * $nominalPerBulan;
        \App\Models\ActivityLog::record(
            'PEMBAYARAN_CREATE',
            "Menerima pembayaran SPP siswa {$siswa->nama} (NISN: {$siswa->nisn}) sebanyak " . count($createdPayments) . " bulan ({$validated['tahun_dibayar']}) total Rp " . number_format($totalNominal, 0, ',', '.')
        );

        $lastPayment = end($createdPayments);
        $successMsg = count($createdPayments) > 1
            ? "Berhasil mencatat pembayaran " . count($createdPayments) . " bulan (" . implode(', ', array_map(fn($p) => $p->bulan_dibayar, $createdPayments)) . ")!"
            : "Transaksi pembayaran SPP bulan {$lastPayment->bulan_dibayar} berhasil dicatat!";

        return redirect()->route('web.pembayaran.show', $lastPayment->id)->with('success', $successMsg);
    }

    public function show(string|int $id): View
    {
        $pembayaran = Pembayaran::with(['petugas', 'siswa.kelas', 'spp'])->findOrFail($id);
        $terbilang = $this->terbilang($pembayaran->jumlah_bayar) . ' rupiah';
        $waLink = $this->generateWhatsAppLink($pembayaran);

        return view('pembayaran.show', compact('pembayaran', 'terbilang', 'waLink'));
    }

    public function cetakKwitansi(string|int $id): View
    {
        $pembayaran = Pembayaran::with(['petugas', 'siswa.kelas', 'spp'])->findOrFail($id);
        $terbilang = $this->terbilang($pembayaran->jumlah_bayar) . ' rupiah';
        $waLink = $this->generateWhatsAppLink($pembayaran);

        \App\Models\ActivityLog::record(
            'KWITANSI_PRINT',
            "Mencetak kwitansi pembayaran #{$pembayaran->id} untuk siswa {$pembayaran->siswa?->nama} (Periode {$pembayaran->bulan_dibayar} {$pembayaran->tahun_dibayar})."
        );

        return view('pembayaran.kwitansi', compact('pembayaran', 'terbilang', 'waLink'));
    }

    private function generateWhatsAppLink(Pembayaran $pembayaran): string
    {
        $phone = preg_replace('/[^0-9]/', '', $pembayaran->siswa?->no_telp ?? '');
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $formattedNominal = 'Rp ' . number_format($pembayaran->jumlah_bayar, 0, ',', '.');
        $message = "Halo Bpk/Ibu wali dari *{$pembayaran->siswa?->nama}* (NISN: {$pembayaran->siswa?->nisn}),\n\n"
            . "Kami mengonfirmasi bahwa pembayaran SPP telah *BERHASIL DITERIMA* dengan rincian sbb:\n"
            . "📄 *No. Kwitansi:* KWT-" . str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT) . "\n"
            . "📅 *Periode SPP:* {$pembayaran->bulan_dibayar} {$pembayaran->tahun_dibayar}\n"
            . "💰 *Nominal:* {$formattedNominal}\n"
            . "🗓️ *Tanggal Bayar:* {$pembayaran->tgl_bayar}\n"
            . "👤 *Petugas:* {$pembayaran->petugas?->name}\n\n"
            . "Status: *LUNAS*\n"
            . "Terima kasih atas kerja samanya.\n_- SMK Web SPP Official-_";

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $pembayaran = Pembayaran::with('siswa')->findOrFail($id);
        $namaSiswa = $pembayaran->siswa?->nama;
        $periode = "{$pembayaran->bulan_dibayar} {$pembayaran->tahun_dibayar}";
        $pembayaran->delete();

        \App\Models\ActivityLog::record(
            'PEMBAYARAN_DELETE',
            "Membatalkan/menghapus transaksi pembayaran SPP #{$id} ({$namaSiswa} - Periode {$periode})."
        );

        return redirect()->route('web.pembayaran.index')->with('success', 'Transaksi pembayaran berhasil dihapus/dibatalkan.');
    }

    private function terbilang(int $angka): string
    {
        $angka = abs($angka);
        $baca = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
        $terbilang = '';

        if ($angka < 12) {
            $terbilang = ' ' . $baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10) . ' belas';
        } elseif ($angka < 100) {
            $terbilang = $this->terbilang((int) ($angka / 10)) . ' puluh' . $this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = ' seratus' . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = $this->terbilang((int) ($angka / 100)) . ' ratus' . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = ' seribu' . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = $this->terbilang((int) ($angka / 1000)) . ' ribu' . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = $this->terbilang((int) ($angka / 1000000)) . ' juta' . $this->terbilang($angka % 1000000);
        }

        return trim($terbilang);
    }
}
