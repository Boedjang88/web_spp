@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi UKT')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200 font-mono">FINANCE &amp; REPORTING</span>
                <span class="text-xs text-slate-400">Rekapitulasi Arus Kas</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">Rekapitulasi &amp; Laporan Keuangan UKT</h1>
            <p class="text-xs text-slate-500 mt-0.5">Filter transaksi penerimaan SPP/UKT, cetak dokumen resmi, dan ekspor ke format CSV / Excel</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('web.laporan.exportCsv', request()->query()) }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-emerald-600/20 transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export CSV</span>
            </a>
            <a href="{{ route('web.laporan.cetak', request()->query()) }}" target="_blank" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-indigo-600/20 transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Laporan</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <form method="GET" action="{{ route('web.laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end text-xs">
            <!-- Dari Tanggal -->
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-mono transition">
            </div>

            <!-- Sampai Tanggal -->
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-mono transition">
            </div>

            <!-- Filter Kelas -->
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Filter Kelas Kuliah</label>
                <select name="id_kelas" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    <option value="">-- Semua Kelas Kuliah --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('id_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Petugas -->
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Filter Petugas Kasir</label>
                <select name="id_petugas" class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition">
                    <option value="">-- Semua Petugas --</option>
                    @foreach($petugasList as $p)
                        <option value="{{ $p->id }}" {{ request('id_petugas') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md shadow-indigo-500/20 transition inline-flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter Data</span>
                </button>
                @if(request()->hasAny(['start_date', 'end_date', 'id_kelas', 'id_petugas']))
                    <a href="{{ route('web.laporan.index') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition text-center" title="Reset Filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Summary Highlights -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-5 text-white shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-emerald-100 uppercase tracking-wider block">Total Pemasukan (Sesuai Filter)</span>
                <div class="text-2xl font-black mt-1">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            </div>
            <span class="p-3 bg-white/10 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
        </div>

        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-blue-100 uppercase tracking-wider block">Volume Transaksi Tercatat</span>
                <div class="text-2xl font-black mt-1">{{ number_format($totalTransaksi) }} Transaksi</div>
            </div>
            <span class="p-3 bg-white/10 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </span>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                        <th class="py-3.5 px-4">No</th>
                        <th class="py-3.5 px-4">No. Bukti</th>
                        <th class="py-3.5 px-4">Tgl Transaksi</th>
                        <th class="py-3.5 px-4">Nama Mahasiswa</th>
                        <th class="py-3.5 px-4">Program Studi / Kelas</th>
                        <th class="py-3.5 px-4">Periode UKT</th>
                        <th class="py-3.5 px-4">Petugas</th>
                        <th class="py-3.5 px-4 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pembayarans as $index => $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 text-slate-400 font-mono">{{ $pembayarans->firstItem() + $index }}</td>
                            <td class="py-3.5 px-4 font-mono font-bold text-indigo-600">KWT-{{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-3.5 px-4 text-slate-600 font-mono">{{ $item->tgl_bayar }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $item->siswa?->nama }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $item->siswa?->kelas?->nama_kelas }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $item->bulan_dibayar }} {{ $item->tahun_dibayar }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $item->petugas?->name }}</td>
                            <td class="py-3.5 px-4 font-bold text-emerald-600 text-right font-mono">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-slate-700">Tidak ada transaksi ditemukan</p>
                                <p class="text-xs text-slate-400 mt-1">Coba sesuaikan rentang tanggal, filter kelas, atau reset filter Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pembayarans->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $pembayarans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
