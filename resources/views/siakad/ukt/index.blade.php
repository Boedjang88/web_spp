@extends('layouts.app')

@section('title', 'Tagihan & Pembayaran UKT')

@section('content')
<div class="space-y-5">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-zinc-900 dark:bg-zinc-950 p-5 md:p-6 rounded-xl border border-zinc-800 text-white">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded bg-zinc-800 text-zinc-300 border border-zinc-700 text-[10px] font-mono font-semibold mb-2">
                <span>H2H BANK INVOICING GATEWAY</span>
            </div>
            <h1 class="text-xl font-bold tracking-tight text-white">Portal Pembayaran UKT &amp; Biaya Kuliah</h1>
            <p class="text-xs text-zinc-400 mt-1 max-w-2xl leading-relaxed">Rincian tagihan semester aktif, informasi Virtual Account, dan riwayat pembayaran resmi.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-3.5 py-2 rounded-lg bg-zinc-800 border border-zinc-700 text-right font-mono">
                <div class="text-[10px] text-zinc-400 uppercase tracking-wider font-semibold">Tahun Akademik</div>
                <div class="text-xs font-bold text-white">{{ $activeTa->nama_tahun ?? '-' }} ({{ $activeTa->semester ?? '-' }})</div>
            </div>
        </div>
    </div>

    <!-- Active Invoice Breakdown Card -->
    <div class="p-5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 space-y-5">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-5 border-b border-zinc-100 dark:border-zinc-800">
            <div>
                <span class="text-[10px] font-mono uppercase tracking-wider text-zinc-500 font-semibold block">Nomor Virtual Account H2H Bank</span>
                <div class="text-2xl font-mono font-bold tracking-wider mt-1 text-zinc-900 dark:text-zinc-100 select-all">
                    {{ $tagihan->nomor_va }}
                </div>
                <div class="text-xs text-zinc-500 mt-1.5 flex flex-wrap items-center gap-2 font-mono">
                    <span>Invoice: <strong class="text-zinc-800 dark:text-zinc-200">{{ $tagihan->nomor_invoice }}</strong></span>
                    <span>&bull;</span>
                    <span>Jatuh Tempo: <strong class="text-zinc-800 dark:text-zinc-200">{{ $tagihan->tgl_jatuh_tempo ? $tagihan->tgl_jatuh_tempo->format('d M Y') : '-' }}</strong></span>
                </div>
            </div>

            <div class="text-left md:text-right space-y-2 font-mono">
                <span class="text-[10px] uppercase tracking-wider text-zinc-500 font-semibold block">Status Tagihan</span>
                <div>
                    @if($tagihan->status_pembayaran === 'Lunas')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700">
                            Lunas Terverifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700">
                            Menunggu Pembayaran
                        </span>
                    @endif
                </div>

                @if($tagihan->status_pembayaran !== 'Lunas')
                    <form action="{{ route('siakad.ukt.bayar', $tagihan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda ingin memproses pembayaran tagihan UKT ini melalui Virtual Account Bank?');">
                        @csrf
                        <button type="submit" class="mt-2 py-2 px-3.5 rounded-lg bg-zinc-900 dark:bg-zinc-100 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-xs font-semibold transition flex items-center gap-1.5">
                            <span>Bayar Sekarang (Simulasi VA)</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Breakdown Details -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100 mb-3">
                Rincian Komponen Biaya Kuliah
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left font-mono">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 uppercase text-[10px] border-b border-zinc-200 dark:border-zinc-800">
                        <tr>
                            <th class="py-2.5 px-3">Komponen Biaya</th>
                            <th class="py-2.5 px-3">Keterangan</th>
                            <th class="py-2.5 px-3 text-right">Nominal (IDR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        <tr>
                            <td class="py-2.5 px-3 font-sans font-semibold text-zinc-900 dark:text-zinc-100">Uang Kuliah Tunggal (UKT Pokok)</td>
                            <td class="py-2.5 px-3 font-sans text-zinc-500">Kategori {{ $mahasiswa->ukt?->kelompok_ukt ?? 'Reguler' }} - Biaya Operasional Pendidikan</td>
                            <td class="py-2.5 px-3 text-right font-bold text-zinc-900 dark:text-zinc-100">Rp {{ number_format($tagihan->biaya_ukt, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 font-sans font-semibold text-zinc-900 dark:text-zinc-100">Biaya Praktikum &amp; Laboratorium</td>
                            <td class="py-2.5 px-3 font-sans text-zinc-500">Pemeliharaan Fasilitas Komputasi &amp; Riset</td>
                            <td class="py-2.5 px-3 text-right font-bold text-zinc-900 dark:text-zinc-100">Rp {{ number_format($tagihan->biaya_praktikum, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 font-sans font-semibold text-zinc-900 dark:text-zinc-100">Iuran Kemahasiswaan &amp; BEM</td>
                            <td class="py-2.5 px-3 font-sans text-zinc-500">Kegiatan Ormawa &amp; Asuransi Mahasiswa</td>
                            <td class="py-2.5 px-3 text-right font-bold text-zinc-900 dark:text-zinc-100">Rp {{ number_format($tagihan->biaya_kemahasiswaan, 2, ',', '.') }}</td>
                        </tr>
                        @if($tagihan->total_potongan_beasiswa > 0)
                        <tr>
                            <td class="py-2.5 px-3 font-sans font-semibold text-zinc-900 dark:text-zinc-100">Potongan Beasiswa / KIP-Kuliah</td>
                            <td class="py-2.5 px-3 font-sans text-zinc-500">Bantuan Biaya Pendidikan Institusi</td>
                            <td class="py-2.5 px-3 text-right font-bold text-zinc-900 dark:text-zinc-100">- Rp {{ number_format($tagihan->total_potongan_beasiswa, 2, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot class="bg-zinc-50 dark:bg-zinc-800/50 font-semibold text-zinc-900 dark:text-zinc-100 border-t border-zinc-200 dark:border-zinc-800">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 text-right font-sans font-bold text-zinc-500">Total Tagihan:</td>
                            <td class="py-2.5 px-3 text-right text-sm font-bold text-zinc-900 dark:text-zinc-100">Rp {{ number_format($tagihan->total_harus_bayar, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="p-5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            Riwayat Pembayaran &amp; Kwitansi Digital
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left font-mono">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 uppercase text-[10px] border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="py-2.5 px-3">No. Kwitansi</th>
                        <th class="py-2.5 px-3">Tanggal Bayar</th>
                        <th class="py-2.5 px-3">Channel / Bank</th>
                        <th class="py-2.5 px-3">Jumlah Bayar</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse($riwayatPembayaran as $bayar)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                        <td class="py-2.5 px-3 font-bold text-zinc-900 dark:text-zinc-100">{{ $bayar->nomor_kuitansi }}</td>
                        <td class="py-2.5 px-3 text-zinc-500">{{ $bayar->tgl_bayar ? $bayar->tgl_bayar->format('d M Y, H:i') : '-' }} WIB</td>
                        <td class="py-2.5 px-3 text-zinc-700 dark:text-zinc-300 font-sans font-medium">{{ $bayar->channel_bayar }} ({{ $bayar->kode_bank }})</td>
                        <td class="py-2.5 px-3 font-bold text-zinc-900 dark:text-zinc-100">Rp {{ number_format($bayar->jumlah_bayar, 2, ',', '.') }}</td>
                        <td class="py-2.5 px-3 font-sans">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700">
                                Berhasil
                            </span>
                        </td>
                        <td class="py-2.5 px-3 text-right font-sans">
                            <a href="{{ route('siakad.ukt.kwitansi', $bayar->id) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-medium text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 px-2.5 py-1 rounded border border-zinc-300 dark:border-zinc-700 transition">
                                <span>Cetak Kwitansi</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-zinc-500 text-xs font-sans">
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
