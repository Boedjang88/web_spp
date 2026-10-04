@extends('layouts.app')

@section('title', 'Data Mahasiswa')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800">SIAKAD</span>
                <span class="text-xs text-slate-400">Civitas Akademika</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1">Data Mahasiswa</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar mahasiswa aktif, kelas kuliah/prodi, tarif UKT, dan status pembayaran</p>
        </div>
        <a href="{{ route('web.siswa.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 transition inline-flex items-center gap-1.5">
            <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></span> Registrasi Mahasiswa Baru
        </a>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        
        <!-- Filter Bar -->
        <div class="p-4 border-b border-slate-200/80 bg-slate-50/50 flex flex-col md:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('web.siswa.index') }}" class="w-full flex flex-col sm:flex-row items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NIM, atau NISN..."
                    class="w-full sm:max-w-xs px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                
                <select name="id_kelas" class="w-full sm:w-auto px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Kelas Kuliah --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('id_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>

                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition">Filter</button>
                
                @if(request('search') || request('id_kelas'))
                    <a href="{{ route('web.siswa.index') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/50 text-slate-500 uppercase tracking-wider text-[10px] font-semibold">
                        <th class="py-3.5 px-4 font-semibold">No</th>
                        <th class="py-3.5 px-4 font-semibold">NIM / NISN</th>
                        <th class="py-3.5 px-4 font-semibold">Nama Mahasiswa</th>
                        <th class="py-3.5 px-4 font-semibold">Kelas Kuliah</th>
                        <th class="py-3.5 px-4 font-semibold">Tarif UKT</th>
                        <th class="py-3.5 px-4 font-semibold">No. Telepon</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($siswas as $index => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-slate-500">{{ $siswas->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-mono font-medium text-slate-900">
                                {{ $item->nim ?? $item->nisn }}
                                <span class="block text-[10px] text-slate-400">NISN: {{ $item->nisn }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $item->nama }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                    {{ $item->kelas?->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-medium">
                                Rp {{ number_format($item->spp?->nominal ?? 0, 0, ',', '.') }}
                                <span class="block text-[10px] text-slate-400">Thn {{ $item->spp?->tahun }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->no_telp }}</td>
                            <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('web.siswa.kartuUjian', $item->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-700 font-semibold bg-indigo-50 border border-indigo-200/60 px-2 py-1 rounded-lg text-[11px]">Kartu Ujian</a>
                                <a href="{{ route('web.pembayaran.create', ['id_siswa' => $item->id]) }}" class="text-emerald-700 hover:text-emerald-800 font-bold bg-emerald-50 border border-emerald-200/60 px-2 py-1 rounded-lg text-[11px]">Bayar UKT</a>
                                <a href="{{ route('web.siswa.show', $item->id) }}" class="text-slate-600 hover:text-slate-900 font-semibold text-[11px] px-1.5">Detail</a>
                                <a href="{{ route('web.siswa.edit', $item->id) }}" class="text-amber-600 hover:text-amber-700 font-semibold text-[11px] px-1.5">Edit</a>
                                <form action="{{ route('web.siswa.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus mahasiswa ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold text-[11px] px-1.5">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data mahasiswa yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($siswas->hasPages())
            <div class="p-4 border-t border-slate-200/80">
                {{ $siswas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
