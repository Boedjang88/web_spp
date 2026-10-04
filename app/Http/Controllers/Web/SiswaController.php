<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Spp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Siswa::with(['kelas', 'spp']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('id_kelas')) {
            $query->where('id_kelas', $request->id_kelas);
        }

        $siswas = $query->orderBy('nama')->paginate(10)->withQueryString();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('siswa.index', compact('siswas', 'kelasList'));
    }

    public function create(): View
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $sppList = Spp::orderBy('tahun', 'desc')->get();

        return view('siswa.create', compact('kelasList', 'sppList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nisn' => 'required|string|size:10|unique:siswas,nisn',
            'nis' => 'required|string|max:8',
            'nama' => 'required|string|max:50',
            'id_kelas' => 'required|exists:kelas,id',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:15',
            'id_spp' => 'required|exists:spps,id',
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.size' => 'NISN harus 10 digit angka.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'nama.required' => 'Nama siswa wajib diisi.',
            'id_kelas.required' => 'Kelas wajib dipilih.',
            'id_spp.required' => 'Tarif SPP wajib dipilih.',
        ]);

        $siswa = Siswa::create($validated);
        \App\Models\ActivityLog::record('SISWA_CREATE', "Menambahkan data siswa baru {$siswa->nama} (NISN: {$siswa->nisn}).");

        return redirect()->route('web.siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(string|int $id): View
    {
        $siswa = Siswa::with(['kelas', 'spp', 'pembayarans.petugas'])->findOrFail($id);
        $tunggakan = $siswa->info_tunggakan;

        return view('siswa.show', compact('siswa', 'tunggakan'));
    }

    public function suratTagihan(string|int $id): View
    {
        $siswa = Siswa::with(['kelas', 'spp', 'pembayarans.petugas'])->findOrFail($id);
        \App\Models\ActivityLog::record('SURAT_TAGIHAN_PRINT', "Mencetak/melihat surat tagihan resmi siswa {$siswa->nama} (NISN: {$siswa->nisn}).");

        return view('siswa.surat-tagihan', compact('siswa'));
    }

    public function edit(string|int $id): View
    {
        $siswa = Siswa::findOrFail($id);
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $sppList = Spp::orderBy('tahun', 'desc')->get();

        return view('siswa.edit', compact('siswa', 'kelasList', 'sppList'));
    }

    public function update(Request $request, string|int $id): RedirectResponse
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nisn' => ['required', 'string', 'size:10', Rule::unique('siswas', 'nisn')->ignore($siswa->id)],
            'nis' => 'required|string|max:8',
            'nama' => 'required|string|max:50',
            'id_kelas' => 'required|exists:kelas,id',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:15',
            'id_spp' => 'required|exists:spps,id',
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.size' => 'NISN harus 10 digit.',
            'nisn.unique' => 'NISN sudah digunakan siswa lain.',
            'nama.required' => 'Nama siswa wajib diisi.',
        ]);

        $siswa->update($validated);
        \App\Models\ActivityLog::record('SISWA_UPDATE', "Memperbarui biodata siswa {$siswa->nama} (NISN: {$siswa->nisn}).");

        return redirect()->route('web.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function kartuUjian(string|int $id): View|RedirectResponse
    {
        $siswa = Siswa::with(['kelas', 'spp'])->findOrFail($id);

        // Security check for Siswa role: cannot view other students' exam pass
        if (auth()->check() && auth()->user()->role === 'siswa') {
            if (auth()->user()->id_siswa && auth()->user()->id_siswa != $siswa->id) {
                abort(403, 'Akses ditolak. Anda hanya dapat mencetak kartu ujian milik Anda sendiri.');
            }
        }

        $tunggakan = $siswa->info_tunggakan;
        $isLunas = $tunggakan['total_bulan'] === 0;

        return view('siswa.kartu-ujian', compact('siswa', 'tunggakan', 'isLunas'));
    }

    public function destroy(string|int $id): RedirectResponse
    {
        $siswa = Siswa::findOrFail($id);

        if ($siswa->pembayarans()->count() > 0) {
            return back()->with('error', "Siswa {$siswa->nama} tidak dapat dihapus karena memiliki {$siswa->pembayarans()->count()} riwayat transaksi pembayaran SPP.");
        }

        $nama = $siswa->nama;
        $nisn = $siswa->nisn;
        $siswa->delete();

        \App\Models\ActivityLog::record('SISWA_DELETE', "Menghapus data siswa {$nama} (NISN: {$nisn}).");

        return redirect()->route('web.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}
