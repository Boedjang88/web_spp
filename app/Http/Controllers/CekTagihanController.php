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
            'nisn' => 'required|string|max:50'
        ], [
            'nisn.required' => 'NIM / NISN mahasiswa wajib diisi.',
        ]);

        $searchKey = trim($request->nisn);

        $siswa = Siswa::with([
            'kelas',
            'spp',
            'pembayarans.petugas',
            'nilais.mapel',
            'nilais.guru',
            'presensis'
        ])->where(function ($q) use ($searchKey) {
            $q->where('nisn', $searchKey)
              ->orWhere('nis', $searchKey)
              ->orWhere('nik', $searchKey)
              ->orWhere('nama', 'like', "%{$searchKey}%");
        })->first();

        if (!$siswa) {
            return redirect()->route('cek.index')->with('error', "Data mahasiswa dengan NIM/NISN '{$searchKey}' tidak ditemukan dalam sistem akademik.");
        }

        ActivityLog::record('PORTAL_CEK_NISN', "Pencarian data mahasiswa {$siswa->nama} (NIM: {$siswa->nis}) via Portal Layanan Mandiri.");

        return view('cek-tagihan', compact('siswa'));
    }
}