<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;

class CetakKwitansiController extends Controller
{
    public function __invoke($id)
    {
        // Cari data pembayaran, kalau gak ada error 404
        $pembayaran = Pembayaran::with(['siswa.kelas', 'petugas', 'spp'])->findOrFail($id);

        // Tampilkan view kwitansi
        return view('kwitansi', compact('pembayaran'));
    }
}