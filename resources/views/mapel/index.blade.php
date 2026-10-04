@extends('layouts.app')

@section('title', 'Data Mata Pelajaran')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800">SIAKAD</span>
                <span class="text-xs text-slate-400">Kurikulum Sekolah</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1">Data Mata Pelajaran</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola kurikulum, kode mapel, kelompok mata pelajaran, dan standar KKM</p>
        </div>
        <a href="{{ route('web.mapel.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition inline-flex items-center gap-1.5">
            <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></span> Tambah Mapel Baru
        </a>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Search Bar -->
        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
            <form method="GET" action="{{ route('web.mapel.index') }}" class="w-full max-w-md flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mapel, kode, kelompok..."
                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition">Cari</button>
                @if(request('search'))
                    <a href="{{ route('web.mapel.index') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 font-semibold">No</th>
                        <th class="py-3.5 px-4 font-semibold">Kode Mapel</th>
                        <th class="py-3.5 px-4 font-semibold">Nama Mata Pelajaran</th>
                        <th class="py-3.5 px-4 font-semibold">Kelompok</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Standar KKM</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Jadwal Kelas</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mapels as $index => $mapel)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-slate-500">{{ $mapels->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-blue-700">{{ $mapel->kode_mapel }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $mapel->nama_mapel }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $mapel->kelompok }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-emerald-50 text-emerald-700">
                                    {{ $mapel->kkm }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700">
                                    {{ $mapel->jadwals_count }} Sesi
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('web.mapel.edit', $mapel->id) }}" class="text-amber-600 hover:text-amber-700 font-semibold">Edit</a>
                                <form action="{{ route('web.mapel.destroy', $mapel->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus mapel ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">Belum ada mata pelajaran yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mapels->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $mapels->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
