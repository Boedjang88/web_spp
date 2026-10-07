@extends('layouts.app')

@section('title', 'Tagihan & Pembayaran UKT')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-blue-950 via-slate-900 to-indigo-950 p-6 rounded-2xl border border-blue-900/50 text-white shadow-soft">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-blue-200 border border-white/10 text-[10px] font-mono font-bold uppercase mb-2">
                <span>H2H BANK INVOICING GATEWAY</span>
            </div>
            <h1 class="text-xl md:text-2xl font-bold tracking-tight text-white">Portal Pembayaran UKT &amp; Biaya Kuliah</h1>
            <p class="text-xs text-slate-300 mt-1 max-w-2xl leading-relaxed">Rincian tagihan semester aktif, informasi Virtual Account, dan riwayat pembayaran resmi.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 rounded-xl bg-white/10 border border-white/10 backdrop-blur text-right font-mono">
                <div class="text-[10px] text-slate-300 uppercase tracking-wider font-bold">Tahun Akademik</div>
                <div class="text-xs font-bold text-white">{{ $activeTa->nama_tahun ?? '-' }} ({{ $activeTa->semester ?? '-' }})</div>
            </div>
        </div>
    </div>

    <!-- Active Invoice Breakdown Card -->
    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-slate-900 dark:text-slate-100 shadow-soft space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
            <div>
                <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold block">Nomor Virtual Account H2H Bank</span>
                <div class="text-2xl sm:text-3xl font-mono font-black tracking-wider mt-1 text-blue-600 dark:text-blue-400 select-all">
                    {{ $tagihan->nomor_va }}
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex flex-wrap items-center gap-2 font-mono">
                    <span>Invoice: <strong class="text-slate-900 dark:text-slate-100">{{ $tagihan->nomor_invoice }}</strong></span>
                    <span>&bull;</span>
                    <span>Jatuh Tempo: <strong class="text-slate-900 dark:text-slate-100">{{ $tagihan->tgl_jatuh_tempo ? $tagihan->tgl_jatuh_tempo->format('d M Y') : '-' }}</strong></span>
                </div>
            </div>

            <div class="text-left md:text-right space-y-3 font-mono">
                <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block">Status Tagihan</span>
                <div>
                    @if($tagihan->status_pembayaran === 'Lunas')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            ✓ Lunas Terverifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                            ⏱ Menunggu Pembayaran
                        </span>
                    @endif
                </div>

                @if($tagihan->status_pembayaran !== 'Lunas')
                    <form action="{{ route('siakad.ukt.bayar', $tagihan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda ingin memproses pembayaran tagihan UKT ini melalui Virtual Account Bank?');">
                        @csrf
                        <button type="submit" class="mt-2 py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-sm font-sans">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Bayar Sekarang (Simulasi VA)</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Breakdown Details -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100 mb-3">
                Rincian Komponen Biaya Kuliah
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left font-mono">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-400 dark:text-slate-400 uppercase text-[10px] font-bold border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Komponen Biaya</th>
                            <th class="py-3 px-4">Keterangan</th>
                            <th class="py-3 px-4 text-right">Nominal (IDR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr>
                            <td class="py-3 px-4 font-sans font-bold text-slate-900 dark:text-slate-100">Uang Kuliah Tunggal (UKT Pokok)</td>
                            <td class="py-3 px-4 font-sans text-slate-500 dark:text-slate-400">Kategori {{ $mahasiswa->ukt?->kelompok_ukt ?? 'Reguler' }} - Biaya Operasional Pendidikan</td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900 dark:text-slate-100">Rp {{ number_format($tagihan->biaya_ukt, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-sans font-bold text-slate-900 dark:text-slate-100">Biaya Praktikum &amp; Laboratorium</td>
                            <td class="py-3 px-4 font-sans text-slate-500 dark:text-slate-400">Pemeliharaan Fasilitas Komputasi &amp; Riset</td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900 dark:text-slate-100">Rp {{ number_format($tagihan->biaya_praktikum, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 font-sans font-bold text-slate-900 dark:text-slate-100">Iuran Kemahasiswaan &amp; BEM</td>
                            <td class="py-3 px-4 font-sans text-slate-500 dark:text-slate-400">Kegiatan Ormawa &amp; Asuransi Mahasiswa</td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900 dark:text-slate-100">Rp {{ number_format($tagihan->biaya_kemahasiswaan, 2, ',', '.') }}</td>
                        </tr>
                        @if($tagihan->total_potongan_beasiswa > 0)
                        <tr>
                            <td class="py-3 px-4 font-sans font-bold text-emerald-600 dark:text-emerald-400">Potongan Beasiswa / KIP-Kuliah</td>
                            <td class="py-3 px-4 font-sans text-slate-500 dark:text-slate-400">Bantuan Biaya Pendidikan Institusi</td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-600 dark:text-emerald-400">- Rp {{ number_format($tagihan->total_potongan_beasiswa, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50/80 dark:bg-slate-800/80 font-bold border-t border-slate-200 dark:border-slate-700">
                            <td colspan="2" class="py-3 px-4 uppercase text-[11px] text-slate-700 dark:text-slate-300">Total Harus Dibayar</td>
                            <td class="py-3 px-4 text-right text-sm text-blue-600 dark:text-blue-400">Rp {{ number_format($tagihan->total_tagihan, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment History Card -->
    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-slate-900 dark:text-slate-100 shadow-soft space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-slate-100">
            Riwayat Pembayaran &amp; Bukti Kwitansi H2H
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left font-mono">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-400 dark:text-slate-400 uppercase text-[10px] font-bold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Tgl. Transaksi</th>
                        <th class="py-3 px-4">Channel Pembayaran</th>
                        <th class="py-3 px-4">Nominal Dibayar</th>
                        <th class="py-3 px-4">Status H2H</th>
                        <th class="py-3 px-4 text-right">Kwitansi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($riwayatPembayaran as $bayar)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                                {{ \Carbon\Carbon::parse($bayar->tgl_bayar)->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 font-sans font-bold text-slate-900 dark:text-slate-100">
                                Bank H2H Virtual Account
                            </td>
                            <td class="py-3 px-4 font-bold text-emerald-600 dark:text-emerald-400">
                                Rp {{ number_format($bayar->jumlah_bayar, 2, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 font-sans">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    ✓ SETTLED
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-sans">
                                <a href="{{ route('siakad.ukt.kwitansi', $bayar->id) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950 hover:bg-blue-100 dark:hover:bg-blue-900 px-3 py-1 rounded-xl border border-blue-200 dark:border-blue-800 transition">
                                    <span>Unduh Kwitansi</span> &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 font-sans">
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
