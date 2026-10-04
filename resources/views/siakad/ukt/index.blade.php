@extends('layouts.app')

@section('title', 'Tagihan & Pembayaran UKT')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 rounded-2xl border border-indigo-900/40 text-white shadow-sm">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>Host-to-Host (H2H) Bank Invoicing Gateway</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Portal Pembayaran UKT &amp; Biaya Kuliah</h1>
            <p class="text-sm text-slate-400 mt-0.5">Rincian tagihan semester aktif, informasi Virtual Account, dan riwayat pembayaran resmi.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700/60 text-right">
                <div class="text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Tahun Akademik</div>
                <div class="text-sm font-bold text-white">{{ $activeTa->nama_tahun ?? '-' }} ({{ $activeTa->semester ?? '-' }})</div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Active Invoice Breakdown Card -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 text-slate-200 shadow-sm space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-6 border-b border-slate-800">
            <div>
                <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold block">Nomor Virtual Account H2H (BNI / Mandiri / BRI)</span>
                <div class="text-2xl sm:text-3xl font-mono font-bold tracking-wide mt-1 text-emerald-400 select-all">
                    {{ $tagihan->nomor_va }}
                </div>
                <div class="text-xs text-slate-400 mt-2 flex flex-wrap items-center gap-2">
                    <span>Invoice: <strong class="font-mono text-slate-200">{{ $tagihan->nomor_invoice }}</strong></span>
                    <span>&bull;</span>
                    <span>Jatuh Tempo: <strong class="text-slate-200">{{ $tagihan->tgl_jatuh_tempo ? $tagihan->tgl_jatuh_tempo->format('d M Y') : '-' }}</strong></span>
                </div>
            </div>

            <div class="text-left md:text-right space-y-2">
                <span class="text-xs uppercase tracking-wider text-slate-400 block">Status Tagihan</span>
                <div>
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

                @if($tagihan->status_pembayaran !== 'Lunas')
                    <form action="{{ route('siakad.ukt.bayar', $tagihan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda ingin memproses pembayaran tagihan UKT ini melalui Virtual Account Bank?');">
                        @csrf
                        <button type="submit" class="mt-2 py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-sm transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Bayar Sekarang (Simulasi VA)</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Breakdown Details -->
        <div>
            <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Rincian Komponen Biaya Kuliah Semester</span>
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-950/70 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-3">Komponen Biaya</th>
                            <th class="py-3 px-3">Keterangan</th>
                            <th class="py-3 px-3 text-right">Nominal (IDR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        <tr>
                            <td class="py-3 px-3 font-medium text-white">Uang Kuliah Tunggal (UKT Pokok)</td>
                            <td class="py-3 px-3 text-slate-400">Kategori {{ $mahasiswa->ukt?->kelompok_ukt ?? 'Reguler' }} - Biaya Operasional Pendidikan</td>
                            <td class="py-3 px-3 text-right font-mono text-slate-200">Rp {{ number_format($tagihan->biaya_ukt, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3 font-medium text-white">Biaya Praktikum &amp; Laboratorium</td>
                            <td class="py-3 px-3 text-slate-400">Pemeliharaan Fasilitas Komputasi &amp; Riset</td>
                            <td class="py-3 px-3 text-right font-mono text-slate-200">Rp {{ number_format($tagihan->biaya_praktikum, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3 font-medium text-white">Iuran Kemahasiswaan &amp; BEM</td>
                            <td class="py-3 px-3 text-slate-400">Kegiatan Ormawa, Asuransi Mahasiswa, &amp; Minat Bakat</td>
                            <td class="py-3 px-3 text-right font-mono text-slate-200">Rp {{ number_format($tagihan->biaya_kemahasiswaan, 2, ',', '.') }}</td>
                        </tr>
                        @if($tagihan->total_potongan_beasiswa > 0)
                        <tr class="bg-emerald-950/20">
                            <td class="py-3 px-3 font-medium text-emerald-400">Potongan Beasiswa / KIP-Kuliah</td>
                            <td class="py-3 px-3 text-emerald-500">Bantuan Biaya Pendidikan Institusi</td>
                            <td class="py-3 px-3 text-right font-mono text-emerald-400">- Rp {{ number_format($tagihan->total_potongan_beasiswa, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot class="bg-slate-950 font-semibold text-white border-t border-slate-800">
                        <tr>
                            <td colspan="2" class="py-3 px-3 text-right text-slate-400">Total Tagihan Harus Dibayar:</td>
                            <td class="py-3 px-3 text-right font-mono text-indigo-400 text-sm font-bold">Rp {{ number_format($tagihan->total_harus_bayar, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 text-slate-200 shadow-sm space-y-4">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Riwayat Pembayaran &amp; Kuitansi Digital</span>
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-950/70 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-3">No. Kuitansi</th>
                        <th class="py-3 px-3">Tanggal Bayar</th>
                        <th class="py-3 px-3">Channel / Bank</th>
                        <th class="py-3 px-3">Jumlah Bayar</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($riwayatPembayaran as $bayar)
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-3 px-3 font-mono font-medium text-white">{{ $bayar->nomor_kuitansi }}</td>
                        <td class="py-3 px-3 text-slate-400">{{ $bayar->tgl_bayar ? $bayar->tgl_bayar->format('d M Y, H:i') : '-' }} WIB</td>
                        <td class="py-3 px-3 text-slate-300">{{ $bayar->channel_bayar }} ({{ $bayar->kode_bank }})</td>
                        <td class="py-3 px-3 font-mono font-medium text-emerald-400">Rp {{ number_format($bayar->jumlah_bayar, 2, ',', '.') }}</td>
                        <td class="py-3 px-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                Berhasil
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right">
                            <a href="{{ route('siakad.ukt.kwitansi', $bayar->id) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-400 hover:text-indigo-300 bg-indigo-500/10 hover:bg-indigo-500/20 px-2.5 py-1 rounded-lg border border-indigo-500/20 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Cetak Kuitansi</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500 text-xs">
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
