@extends('layouts.app')

@section('title', 'Transaksi Pembayaran UKT')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800">SIAKAD</span>
                <span class="text-xs text-slate-400">Keuangan Mahasiswa</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1">Transaksi Pembayaran UKT</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola dan telusuri riwayat transaksi pembayaran UKT mahasiswa</p>
        </div>
        <a href="{{ route('web.pembayaran.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 transition inline-flex items-center gap-1.5">
            <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></span> Catat Pembayaran UKT Baru
        </a>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        
        <!-- Filter Bar -->
        <div class="p-4 border-b border-slate-200/80 bg-slate-50/50 flex flex-col md:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('web.pembayaran.index') }}" class="w-full flex flex-col sm:flex-row items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NIM Mahasiswa..."
                    class="w-full sm:max-w-xs px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                
                <select name="bulan_dibayar" class="w-full sm:w-auto px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Bulan --</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bulan)
                        <option value="{{ $bulan }}" {{ request('bulan_dibayar') == $bulan ? 'selected' : '' }}>{{ $bulan }}</option>
                    @endforeach
                </select>

                <input type="number" name="tahun_dibayar" value="{{ request('tahun_dibayar') }}" placeholder="Tahun (cth: 2025)"
                    class="w-full sm:w-32 px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">

                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition">Filter</button>
                
                @if(request('search') || request('bulan_dibayar') || request('tahun_dibayar'))
                    <a href="{{ route('web.pembayaran.index') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/50 text-slate-500 uppercase tracking-wider text-[10px] font-semibold">
                        <th class="py-3.5 px-4 font-semibold">No</th>
                        <th class="py-3.5 px-4 font-semibold">Mahasiswa & Kelas Kuliah</th>
                        <th class="py-3.5 px-4 font-semibold">Periode UKT</th>
                        <th class="py-3.5 px-4 font-semibold">Tanggal Bayar</th>
                        <th class="py-3.5 px-4 font-semibold">Petugas Verifikasi</th>
                        <th class="py-3.5 px-4 font-semibold">Nominal</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pembayarans as $index => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-slate-500">{{ $pembayarans->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-medium text-slate-900">
                                <span class="font-bold text-slate-900 block">{{ $item->siswa?->nama ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">NIM: {{ $item->siswa?->nim ?? $item->siswa?->nisn }} ({{ $item->siswa?->kelas?->nama_kelas }})</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                    {{ $item->bulan_dibayar }} {{ $item->tahun_dibayar }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->tgl_bayar }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->petugas?->name ?? 'Petugas BAAK' }}</td>
                            <td class="py-3 px-4 font-bold text-emerald-700">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('web.pembayaran.cetak', $item->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-700 font-semibold bg-indigo-50 border border-indigo-200/60 px-2 py-1 rounded-lg text-[11px] inline-flex items-center gap-1">
                                    <span><svg class="w-3.5 h-3.5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></span> Kwitansi
                                </a>
                                <a href="{{ route('web.pembayaran.show', $item->id) }}" class="text-slate-600 hover:text-slate-900 font-semibold text-[11px]">Detail</a>
                                <form action="{{ route('web.pembayaran.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Batalkan/Hapus transaksi UKT ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold text-[11px]">Batal</button>
                                </form>
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

        <!-- Pagination -->
        @if($pembayarans->hasPages())
            <div class="p-4 border-t border-slate-200/80">
                {{ $pembayarans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
