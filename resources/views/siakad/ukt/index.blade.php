@extends('layouts.app')

@section('title', 'Tagihan & Pembayaran UKT')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Portal Pembayaran UKT & Biaya Kuliah</h1>
            <p class="text-sm text-slate-500 mt-1">Rincian tagihan semester aktif, informasi Virtual Account, dan riwayat pembayaran resmi.</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Tahun Akademik: {{ $activeTa->nama_tahun ?? '-' }} ({{ $activeTa->semester ?? '-' }})
            </span>
        </div>
    </div>

    <!-- Active Invoice Breakdown Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 sm:p-8 bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <span class="text-xs uppercase tracking-wider text-indigo-300 font-semibold">Nomor Virtual Account H2H (BNI / Mandiri / BRI)</span>
                <div class="text-3xl font-mono font-bold tracking-wide mt-1 text-emerald-400">
                    {{ $tagihan->nomor_va }}
                </div>
                <div class="text-xs text-slate-300 mt-2 flex items-center gap-2">
                    <span>Invoice: <strong class="font-mono text-white">{{ $tagihan->nomor_invoice }}</strong></span>
                    <span>&bull;</span>
                    <span>Jatuh Tempo: <strong class="text-white">{{ $tagihan->tgl_jatuh_tempo ? $tagihan->tgl_jatuh_tempo->format('d M Y') : '-' }}</strong></span>
                </div>
            </div>
            <div class="mt-4 md:mt-0 text-left md:text-right">
                <span class="text-xs uppercase tracking-wider text-slate-300">Status Tagihan</span>
                <div class="mt-1">
                    @if($tagihan->status_pembayaran === 'Lunas')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            Lunas Terverifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                            Menunggu Pembayaran
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Breakdown Details -->
        <div class="p-6 sm:p-8">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Rincian Komponen Biaya Kuliah Semester</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border border-slate-200 rounded-lg">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 border-b">Komponen Biaya</th>
                            <th class="px-4 py-3 border-b">Keterangan</th>
                            <th class="px-4 py-3 border-b text-right">Nominal (IDR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td class="px-4 py-3.5 font-medium text-slate-900">Uang Kuliah Tunggal (UKT Pokok)</td>
                            <td class="px-4 py-3.5 text-slate-500">Kategori {{ $mahasiswa->ukt?->kelompok_ukt ?? 'Reguler' }} - Biaya Operasional Pendidikan</td>
                            <td class="px-4 py-3.5 text-right font-mono text-slate-800">Rp {{ number_format($tagihan->biaya_ukt, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3.5 font-medium text-slate-900">Biaya Praktikum & Laboratorium</td>
                            <td class="px-4 py-3.5 text-slate-500">Pemeliharaan Fasilitas Komputasi & Riset</td>
                            <td class="px-4 py-3.5 text-right font-mono text-slate-800">Rp {{ number_format($tagihan->biaya_praktikum, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3.5 font-medium text-slate-900">Iuran Kemahasiswaan & BEM</td>
                            <td class="px-4 py-3.5 text-slate-500">Kegiatan Ormawa, Asuransi Mahasiswa, & Minat Bakat</td>
                            <td class="px-4 py-3.5 text-right font-mono text-slate-800">Rp {{ number_format($tagihan->biaya_kemahasiswaan, 2, ',', '.') }}</td>
                        </tr>
                        @if($tagihan->total_potongan_beasiswa > 0)
                        <tr class="bg-emerald-50/50">
                            <td class="px-4 py-3.5 font-medium text-emerald-800">Potongan Beasiswa / KIP-Kuliah</td>
                            <td class="px-4 py-3.5 text-emerald-600">Bantuan Biaya Pendidikan Institusi</td>
                            <td class="px-4 py-3.5 text-right font-mono text-emerald-700">- Rp {{ number_format($tagihan->total_potongan_beasiswa, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot class="bg-slate-50 font-semibold text-slate-900">
                        <tr>
                            <td colspan="2" class="px-4 py-3.5 text-right">Total Tagihan Harus Dibayar:</td>
                            <td class="px-4 py-3.5 text-right font-mono text-indigo-600 text-base">Rp {{ number_format($tagihan->total_harus_bayar, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-base font-semibold text-slate-900">Riwayat Pembayaran & Kuitansi Digital</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">No. Kuitansi</th>
                        <th class="px-6 py-3">Tanggal Bayar</th>
                        <th class="px-6 py-3">Channel / Bank</th>
                        <th class="px-6 py-3">Jumlah Bayar</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($riwayatPembayaran as $bayar)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-mono font-medium text-slate-900">{{ $bayar->nomor_kuitansi }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $bayar->tgl_bayar->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $bayar->channel_bayar }} ({{ $bayar->kode_bank }})</td>
                        <td class="px-6 py-4 font-mono font-medium text-slate-900">Rp {{ number_format($bayar->jumlah_bayar, 2, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                Berhasil
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('siakad.ukt.kwitansi', $bayar->id) }}" target="_blank" class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Cetak Kuitansi
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            Belum ada riwayat transaksi pembayaran UKT yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
