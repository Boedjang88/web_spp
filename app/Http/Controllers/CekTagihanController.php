<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Siswa;
use Illuminate\Http\Request;

class CekTagihanController extends Controller
{
    public function index()
    {
        return view('cek-tagihan');
    }

    public function search(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|max:10'
        ], [
            'nisn.required' => 'NISN wajib diisi.',
        ]);

        $siswa = Siswa::with([
            'kelas',
            'spp',
            'pembayarans.petugas',
            'nilais.mapel',
            'nilais.guru',
            'presensis'
        ])->where('nisn', $request->nisn)->first();

        if (!$siswa) {
            return redirect()->route('cek.index')->with('error', 'Data siswa dengan NISN tersebut tidak terdaftar dalam sistem akademik.');
        }

        ActivityLog::record('PORTAL_CEK_NISN', "Pencarian data siswa {$siswa->nama} (NISN: {$siswa->nisn}) via Portal Siswa Mandiri.");

        return view('cek-tagihan', compact('siswa'));
    }
}