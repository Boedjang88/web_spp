@extends('layouts.app')

@section('title', 'Data Guru & Tenaga Pendidik')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800">SIAKAD</span>
                <span class="text-xs text-slate-400">Tenaga Pendidik</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1">Data Guru &amp; Pengajar</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data dewan guru, nomor induk pendidik, dan penugasan jadwal</p>
        </div>
        <a href="{{ route('web.guru.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition inline-flex items-center gap-1.5">
            <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></span> Tambah Guru Baru
        </a>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Search Bar -->
        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
            <form method="GET" action="{{ route('web.guru.index') }}" class="w-full max-w-md flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama guru, NIP, atau email..."
                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition">Cari</button>
                @if(request('search'))
                    <a href="{{ route('web.guru.index') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 font-semibold">No</th>
                        <th class="py-3.5 px-4 font-semibold">NIP</th>
                        <th class="py-3.5 px-4 font-semibold">Nama Guru</th>
                        <th class="py-3.5 px-4 font-semibold">L/P</th>
                        <th class="py-3.5 px-4 font-semibold">Kontak &amp; Email</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Jadwal Mengajar</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($gurus as $index => $guru)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-slate-500">{{ $gurus->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-mono font-semibold text-slate-700">{{ $guru->nip ?? '-' }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $guru->nama_guru }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $guru->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $guru->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                <div>{{ $guru->no_telp ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $guru->email ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700">
                                    {{ $guru->jadwals_count }} Kelas
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('web.guru.edit', $guru->id) }}" class="text-amber-600 hover:text-amber-700 font-semibold">Edit</a>
                                <form action="{{ route('web.guru.destroy', $guru->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus guru ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">Belum ada data guru yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($gurus->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $gurus->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
