<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class CekTagihanController extends Controller
{
    public function index()
    {
        // Tampilkan halaman awal (form pencarian)
        return view('cek-tagihan');
    }

    public function search(Request $request)
    {
        // Validasi input
        $request->validate([
            'nisn' => 'required|numeric'
        ]);

        // Cari siswa berdasarkan NISN, sekalian ambil data kelas & spp
        $siswa = Siswa::with(['kelas', 'spp', 'pembayarans'])->where('nisn', $request->nisn)->first();

        // Kalau gak ketemu, balikin dengan pesan error
        if (!$siswa) {
            return redirect()->route('cek.index')->with('error', 'Data siswa dengan NISN tersebut tidak ditemukan.');
        }

        // Kalau ketemu, tampilkan halaman yang sama tapi bawa data siswa
        return view('cek-tagihan', compact('siswa'));
    }
}