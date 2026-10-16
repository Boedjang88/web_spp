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
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('web.siswa.kartuUjian', $item->id) }}" target="_blank"
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200/70 px-2 py-1 rounded-lg transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/></svg>
                                        Kartu Ujian
                                    </a>
                                    <a href="{{ route('web.pembayaran.create', ['id_siswa' => $item->id]) }}"
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/70 px-2 py-1 rounded-lg transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        Bayar UKT
                                    </a>
                                    {{-- Secondary action group --}}
                                    <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden">
                                        <a href="{{ route('web.siswa.show', $item->id) }}"
                                            class="inline-flex items-center px-2 py-1 text-[11px] font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition border-r border-slate-200">
                                            Detail
                                        </a>
                                        <a href="{{ route('web.siswa.edit', $item->id) }}"
                                            class="inline-flex items-center px-2 py-1 text-[11px] font-medium text-amber-600 hover:bg-amber-50 hover:text-amber-700 transition">
                                            Edit
                                        </a>
                                    </div>
                                    {{-- Destructive action (visually separated) --}}
                                    <form action="{{ route('web.siswa.destroy', $item->id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Hapus mahasiswa {{ $item->nama }}? Data yang terhapus tidak dapat dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 text-[11px] font-medium text-rose-600 hover:bg-rose-50 hover:text-rose-700 border border-rose-200/70 px-2 py-1 rounded-lg transition" title="Hapus Mahasiswa">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
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
