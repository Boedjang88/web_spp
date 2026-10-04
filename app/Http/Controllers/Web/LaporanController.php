<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $petugasList = User::orderBy('name')->get();

        $query = Pembayaran::with(['petugas', 'siswa.kelas', 'spp']);

        if ($request->filled('start_date')) {
            $query->whereDate('tgl_bayar', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tgl_bayar', '<=', $request->end_date);
        }

        if ($request->filled('id_kelas')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('id_kelas', $request->id_kelas);
            });
        }

        if ($request->filled('id_petugas')) {
            $query->where('id_petugas', $request->id_petugas);
        }

        $totalPemasukan = (clone $query)->sum('jumlah_bayar');
        $totalTransaksi = (clone $query)->count();
        $pembayarans = $query->orderBy('tgl_bayar', 'desc')->paginate(15)->withQueryString();

        return view('laporan.index', compact(
            'pembayarans',
            'kelasList',
            'petugasList',
            'totalPemasukan',
            'totalTransaksi'
        ));
    }

    public function cetak(Request $request): View
    {
        $query = Pembayaran::with(['petugas', 'siswa.kelas', 'spp']);

        if ($request->filled('start_date')) {
            $query->whereDate('tgl_bayar', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tgl_bayar', '<=', $request->end_date);
        }
        if ($request->filled('id_kelas')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('id_kelas', $request->id_kelas);
            });
        }
        if ($request->filled('id_petugas')) {
            $query->where('id_petugas', $request->id_petugas);
        }

        $pembayarans = $query->orderBy('tgl_bayar', 'asc')->get();
        $totalPemasukan = $pembayarans->sum('jumlah_bayar');
        $terbilang = $this->terbilang($totalPemasukan) . ' rupiah';

        $filterKelas = $request->filled('id_kelas') ? Kelas::find($request->id_kelas) : null;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        return view('laporan.cetak', compact(
            'pembayarans',
            'totalPemasukan',
            'terbilang',
            'filterKelas',
            'startDate',
            'endDate'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Pembayaran::with(['petugas', 'siswa.kelas', 'spp']);

        if ($request->filled('start_date')) {
            $query->whereDate('tgl_bayar', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tgl_bayar', '<=', $request->end_date);
        }
        if ($request->filled('id_kelas')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('id_kelas', $request->id_kelas);
            });
        }
        if ($request->filled('id_petugas')) {
            $query->where('id_petugas', $request->id_petugas);
        }

        $pembayarans = $query->orderBy('tgl_bayar', 'asc')->get();
        $fileName = 'laporan-pembayaran-spp-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($pembayarans) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, [
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

            foreach ($pembayarans as $p) {
                fputcsv($file, [
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
            }

            fclose($file);
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
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
