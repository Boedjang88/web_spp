@extends('layouts.app')

@section('title', 'Data Kelas Kuliah')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800">SIAKAD</span>
                <span class="text-xs text-slate-400">Manajemen Kelas</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1">Data Kelas Kuliah</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data kelas perkuliahan dan rumpun program studi/jurusan</p>
        </div>
        <a href="{{ route('web.kelas.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 transition inline-flex items-center gap-1.5">
            <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></span> Tambah Kelas Kuliah Baru
        </a>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        
        <!-- Search Bar -->
        <div class="p-4 border-b border-slate-200/80 bg-slate-50/50 flex items-center justify-between">
            <form method="GET" action="{{ route('web.kelas.index') }}" class="w-full max-w-sm flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kelas / prodi..."
                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition">Cari</button>
                @if(request('search'))
                    <a href="{{ route('web.kelas.index') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/50 text-slate-500 uppercase tracking-wider text-[10px] font-semibold">
                        <th class="py-3.5 px-4 font-semibold">No</th>
                        <th class="py-3.5 px-4 font-semibold">Nama Kelas Kuliah</th>
                        <th class="py-3.5 px-4 font-semibold">Program Studi / Keahlian</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Jumlah Mahasiswa</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kelas as $index => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-slate-500">{{ $kelas->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $item->nama_kelas }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->kompetensi_keahlian }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                    {{ $item->siswas_count }} Mahasiswa
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('web.kelas.edit', $item->id) }}" class="text-amber-600 hover:text-amber-700 font-semibold">Edit</a>
                                <form action="{{ route('web.kelas.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus kelas kuliah ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Tidak ada data kelas kuliah yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($kelas->hasPages())
            <div class="p-4 border-t border-slate-200/80">
                {{ $kelas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
