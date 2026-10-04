@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Data Kelas</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data kelas dan kompetensi keahlian sekolah</p>
        </div>
        <a href="{{ route('web.kelas.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1">
            <span>➕</span> Tambah Kelas Baru
        </a>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Search Bar -->
        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
            <form method="GET" action="{{ route('web.kelas.index') }}" class="w-full max-w-sm flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kelas / jurusan..."
                    class="w-full px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-medium hover:bg-slate-900">Cari</button>
                @if(request('search'))
                    <a href="{{ route('web.kelas.index') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4 font-semibold">No</th>
                        <th class="py-3 px-4 font-semibold">Nama Kelas</th>
                        <th class="py-3 px-4 font-semibold">Kompetensi Keahlian</th>
                        <th class="py-3 px-4 font-semibold text-center">Jumlah Siswa</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kelas as $index => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-slate-500">{{ $kelas->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $item->nama_kelas }}</td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->kompetensi_keahlian }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700">
                                    {{ $item->siswas_count }} Siswa
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('web.kelas.edit', $item->id) }}" class="text-amber-600 hover:text-amber-700 font-semibold">Edit</a>
                                <form action="{{ route('web.kelas.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus kelas ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Tidak ada data kelas yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($kelas->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $kelas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
