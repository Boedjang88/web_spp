@extends('layouts.app')

@section('title', 'Tagihan & Pembayaran UKT')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-slate-900 via-slate-850 to-brand-900 p-6 sm:p-8 rounded-2xl border border-slate-800 text-white shadow-soft">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-brand-200 border border-white/10 text-xs font-semibold mb-2">
                <svg class="w-3.5 h-3.5 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>Host-to-Host (H2H) Bank Invoicing Gateway</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Portal Pembayaran UKT &amp; Biaya Kuliah</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">Rincian tagihan semester aktif, informasi Virtual Account, dan riwayat pembayaran resmi.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 backdrop-blur text-right">
                <div class="text-[10px] text-slate-300 uppercase tracking-wider font-bold">Tahun Akademik</div>
                <div class="text-sm font-bold text-white">{{ $activeTa->nama_tahun ?? '-' }} ({{ $activeTa->semester ?? '-' }})</div>
            </div>
        </div>
    </div>

    <!-- Active Invoice Breakdown Card -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-soft text-slate-900 space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block">Nomor Virtual Account H2H (BNI / Mandiri / BRI)</span>
                <div class="text-2xl sm:text-3xl font-mono font-black tracking-wide mt-1 text-brand-600 select-all">
                    {{ $tagihan->nomor_va }}
                </div>
                <div class="text-xs text-slate-500 mt-2 flex flex-wrap items-center gap-2">
                    <span>Invoice: <strong class="font-mono text-slate-800">{{ $tagihan->nomor_invoice }}</strong></span>
                    <span>&bull;</span>
                    <span>Jatuh Tempo: <strong class="text-slate-800">{{ $tagihan->tgl_jatuh_tempo ? $tagihan->tgl_jatuh_tempo->format('d M Y') : '-' }}</strong></span>
                </div>
            </div>

            <div class="text-left md:text-right space-y-2">
                <span class="text-xs uppercase tracking-wider text-slate-400 font-bold block">Status Tagihan</span>
                <div>
                    @if($tagihan->status_pembayaran === 'Lunas')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Lunas Terverifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            Menunggu Pembayaran
                        </span>
                    @endif
                </div>

                @if($tagihan->status_pembayaran !== 'Lunas')
                    <form action="{{ route('siakad.ukt.bayar', $tagihan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda ingin memproses pembayaran tagihan UKT ini melalui Virtual Account Bank?');">
                        @csrf
                        <button type="submit" class="mt-2 py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <span>Bayar Sekarang (Simulasi VA)</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Breakdown Details -->
        <div>
            <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Rincian Komponen Biaya Kuliah Semester</span>
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-3">Komponen Biaya</th>
                            <th class="py-3 px-3">Keterangan</th>
                            <th class="py-3 px-3 text-right">Nominal (IDR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-3 px-3 font-semibold text-slate-900">Uang Kuliah Tunggal (UKT Pokok)</td>
                            <td class="py-3 px-3 text-slate-500">Kategori {{ $mahasiswa->ukt?->kelompok_ukt ?? 'Reguler' }} - Biaya Operasional Pendidikan</td>
                            <td class="py-3 px-3 text-right font-mono text-slate-900 font-semibold">Rp {{ number_format($tagihan->biaya_ukt, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3 font-semibold text-slate-900">Biaya Praktikum &amp; Laboratorium</td>
                            <td class="py-3 px-3 text-slate-500">Pemeliharaan Fasilitas Komputasi &amp; Riset</td>
                            <td class="py-3 px-3 text-right font-mono text-slate-900 font-semibold">Rp {{ number_format($tagihan->biaya_praktikum, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3 font-semibold text-slate-900">Iuran Kemahasiswaan &amp; BEM</td>
                            <td class="py-3 px-3 text-slate-500">Kegiatan Ormawa, Asuransi Mahasiswa, &amp; Minat Bakat</td>
                            <td class="py-3 px-3 text-right font-mono text-slate-900 font-semibold">Rp {{ number_format($tagihan->biaya_kemahasiswaan, 2, ',', '.') }}</td>
                        </tr>
                        @if($tagihan->total_potongan_beasiswa > 0)
                        <tr class="bg-emerald-50/60">
                            <td class="py-3 px-3 font-semibold text-emerald-700">Potongan Beasiswa / KIP-Kuliah</td>
                            <td class="py-3 px-3 text-emerald-600">Bantuan Biaya Pendidikan Institusi</td>
                            <td class="py-3 px-3 text-right font-mono text-emerald-700 font-bold">- Rp {{ number_format($tagihan->total_potongan_beasiswa, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot class="bg-slate-50 font-semibold text-slate-900 border-t border-slate-200">
                        <tr>
                            <td colspan="2" class="py-3 px-3 text-right text-slate-500 font-bold">Total Tagihan Harus Dibayar:</td>
                            <td class="py-3 px-3 text-right font-mono text-brand-600 text-sm font-black">Rp {{ number_format($tagihan->total_harus_bayar, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-soft text-slate-900 space-y-4">
        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Riwayat Pembayaran &amp; Kuitansi Digital</span>
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-3">No. Kuitansi</th>
                        <th class="py-3 px-3">Tanggal Bayar</th>
                        <th class="py-3 px-3">Channel / Bank</th>
                        <th class="py-3 px-3">Jumlah Bayar</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayatPembayaran as $bayar)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3 px-3 font-mono font-bold text-slate-900">{{ $bayar->nomor_kuitansi }}</td>
                        <td class="py-3 px-3 text-slate-500">{{ $bayar->tgl_bayar ? $bayar->tgl_bayar->format('d M Y, H:i') : '-' }} WIB</td>
                        <td class="py-3 px-3 text-slate-700 font-medium">{{ $bayar->channel_bayar }} ({{ $bayar->kode_bank }})</td>
                        <td class="py-3 px-3 font-mono font-bold text-emerald-600">Rp {{ number_format($bayar->jumlah_bayar, 2, ',', '.') }}</td>
                        <td class="py-3 px-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Berhasil
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right">
                            <a href="{{ route('siakad.ukt.kwitansi', $bayar->id) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-brand-600 hover:text-brand-700 bg-brand-50 hover:bg-brand-100 px-2.5 py-1 rounded-lg border border-brand-200 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Cetak Kuitansi</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
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
