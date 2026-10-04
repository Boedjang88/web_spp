@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Data Siswa</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar siswa, kelas, tarif SPP, dan status tunggakan</p>
        </div>
        <a href="{{ route('web.siswa.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1">
            <span>➕</span> Registrasi Siswa Baru
        </a>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Filter Bar -->
        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col md:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('web.siswa.index') }}" class="w-full flex flex-col sm:flex-row items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, NISN, atau NIS..."
                    class="w-full sm:max-w-xs px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <select name="id_kelas" class="w-full sm:w-auto px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('id_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>

                <button type="submit" class="w-full sm:w-auto px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-medium hover:bg-slate-900">Filter</button>
                
                @if(request('search') || request('id_kelas'))
                    <a href="{{ route('web.siswa.index') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4 font-semibold">No</th>
                        <th class="py-3 px-4 font-semibold">NISN / NIS</th>
                        <th class="py-3 px-4 font-semibold">Nama Siswa</th>
                        <th class="py-3 px-4 font-semibold">Kelas</th>
                        <th class="py-3 px-4 font-semibold">Tarif SPP</th>
                        <th class="py-3 px-4 font-semibold">No. Telepon</th>
                        <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($siswas as $index => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-slate-500">{{ $siswas->firstItem() + $index }}</td>
                            <td class="py-3 px-4 font-mono font-medium text-slate-900">
                                {{ $item->nisn }}
                                <span class="block text-[10px] text-slate-400">NIS: {{ $item->nis }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $item->nama }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700">
                                    {{ $item->kelas?->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-medium">
                                Rp {{ number_format($item->spp?->nominal ?? 0, 0, ',', '.') }}
                                <span class="block text-[10px] text-slate-400">Thn {{ $item->spp?->tahun }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $item->no_telp }}</td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('web.pembayaran.create', ['id_siswa' => $item->id]) }}" class="text-blue-600 hover:text-blue-700 font-bold bg-blue-50 px-2 py-1 rounded">Bayar</a>
                                <a href="{{ route('web.siswa.show', $item->id) }}" class="text-slate-600 hover:text-slate-900 font-semibold">Detail</a>
                                <a href="{{ route('web.siswa.edit', $item->id) }}" class="text-amber-600 hover:text-amber-700 font-semibold">Edit</a>
                                <form action="{{ route('web.siswa.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus siswa ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($siswas->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $siswas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
