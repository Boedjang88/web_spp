@extends('layouts.app')

@section('title', 'Transaksi Pembayaran UKT')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-brand-50 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 border border-brand-200 dark:border-brand-800">SIAKAD</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Keuangan Mahasiswa</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white mt-1">Transaksi Pembayaran UKT</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola dan telusuri riwayat transaksi pembayaran UKT mahasiswa</p>
        </div>
        <a href="{{ route('web.pembayaran.create') }}" class="w-full sm:w-auto justify-center px-4 py-2.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5 min-h-10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Catat Pembayaran UKT Baru</span>
        </a>
    </div>

    <!-- Filter & Table Card -->
    <x-card :padding="false">
        
        <!-- Filter Bar -->
        <div class="p-4 border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50">
            <form method="GET" action="{{ route('web.pembayaran.index') }}" class="w-full flex flex-col sm:flex-row items-center gap-2.5 text-xs">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NIM Mahasiswa..."
                    class="w-full sm:flex-1 px-3.5 py-2 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 min-h-9.5">
                
                <select name="bulan_dibayar" class="w-full sm:w-auto px-3.5 py-2 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 min-h-9.5">
                    <option value="">-- Semua Bulan --</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bulan)
                        <option value="{{ $bulan }}" {{ request('bulan_dibayar') == $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                    @endforeach
                </select>

                <input type="number" name="tahun_dibayar" value="{{ request('tahun_dibayar') }}" placeholder="Tahun (cth: 2026)"
                    class="w-full sm:w-32 px-3.5 py-2 text-xs bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 min-h-9.5">

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-slate-900 dark:bg-brand-600 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition min-h-9.5">Filter</button>
                    @if(request('search') || request('bulan_dibayar') || request('tahun_dibayar'))
                        <a href="{{ route('web.pembayaran.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 hover:underline min-h-9.5 flex items-center justify-center">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Desktop Table View (>= 768px) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px] font-semibold">
                        <th class="py-3.5 px-4 font-semibold">No</th>
                        <th class="py-3.5 px-4 font-semibold">Mahasiswa &amp; Kelas Kuliah</th>
                        <th class="py-3.5 px-4 font-semibold">Periode UKT</th>
                        <th class="py-3.5 px-4 font-semibold">Tanggal Bayar</th>
                        <th class="py-3.5 px-4 font-semibold">Petugas Verifikasi</th>
                        <th class="py-3.5 px-4 font-semibold">Nominal</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($pembayarans as $index => $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400 font-mono">{{ $pembayarans->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-medium text-slate-900 dark:text-white">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $item->siswa?->nama ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">NIM: {{ $item->siswa?->nim ?? $item->siswa?->nisn }} ({{ $item->siswa?->kelas?->nama_kelas }})</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-brand-50 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800">
                                    {{ $item->bulan_dibayar }} {{ $item->tahun_dibayar }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300 font-mono">{{ $item->tgl_bayar }}</td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300">{{ $item->petugas?->name ?? 'Petugas BAAK' }}</td>
                            <td class="py-3 px-4 font-bold text-emerald-700 dark:text-emerald-400 font-mono">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('web.pembayaran.cetak', $item->id) }}" target="_blank"
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-brand-700 dark:text-brand-300 bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/60 dark:hover:bg-brand-900 border border-brand-200/80 dark:border-brand-800 px-2 py-1 rounded-lg transition" title="Cetak Kwitansi">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        Kwitansi
                                    </a>
                                    <a href="{{ route('web.pembayaran.show', $item->id) }}"
                                        class="inline-flex items-center px-2 py-1 text-[11px] font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg transition">
                                        Detail
                                    </a>
                                    <form action="{{ route('web.pembayaran.destroy', $item->id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Batalkan transaksi pembayaran ini? Tindakan ini tidak dapat diurungkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 text-[11px] font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/60 border border-rose-200/80 dark:border-rose-800 px-2 py-1 rounded-lg transition" title="Batalkan Transaksi">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Batal
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada riwayat transaksi pembayaran UKT.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Adaptive Card List (< 768px) -->
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800 p-4 space-y-4">
            @forelse($pembayarans as $item)
                <div class="pt-4 first:pt-0 space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="text-[10px] text-slate-400 font-mono block">NIM: {{ $item->siswa?->nim ?? $item->siswa?->nisn }} ({{ $item->siswa?->kelas?->nama_kelas }})</span>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm truncate">{{ $item->siswa?->nama ?? '-' }}</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-brand-50 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800 shrink-0">
                            {{ $item->bulan_dibayar }} {{ $item->tahun_dibayar }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Nominal Bayar</span>
                        <span class="font-bold text-emerald-700 dark:text-emerald-400 font-mono text-sm">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</span>
                    </div>

                    <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center justify-between">
                        <span>Tgl: <strong class="text-slate-700 dark:text-slate-300 font-mono">{{ $item->tgl_bayar }}</strong></span>
                        <span>Verifikator: <strong class="text-slate-700 dark:text-slate-300">{{ $item->petugas?->name ?? 'Petugas BAAK' }}</strong></span>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <a href="{{ route('web.pembayaran.cetak', $item->id) }}" target="_blank"
                            class="flex-1 text-center py-2 px-3 bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/60 dark:hover:bg-brand-900 text-brand-700 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800 rounded-xl text-xs font-semibold min-h-9.5 flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Kwitansi
                        </a>
                        <a href="{{ route('web.pembayaran.show', $item->id) }}"
                            class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold min-h-9.5 flex items-center justify-center">
                            Detail
                        </a>
                        <form action="{{ route('web.pembayaran.destroy', $item->id) }}" method="POST"
                            onsubmit="return confirm('Batalkan transaksi pembayaran ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-3 py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800 rounded-xl text-xs font-semibold min-h-9.5 flex items-center justify-center">
                                Batal
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 text-xs font-medium">
                    Tidak ada riwayat transaksi pembayaran UKT.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($pembayarans->hasPages())
            <div class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                {{ $pembayarans->links() }}
            </div>
        @endif
    </x-card>

</div>
@endsection
