@extends('layouts.app')

@section('title', 'Nilai Akademik Siswa')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800">SIAKAD</span>
                <span class="text-xs text-slate-400">Penilaian &amp; E-Rapor</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1">Nilai Akademik &amp; E-Rapor</h1>
            <p class="text-xs text-slate-500 mt-0.5">Input nilai Tugas, UTS, UAS, kalkulasi nilai akhir otomatis, predikat, dan cetak rapor</p>
        </div>
        <a href="{{ route('web.nilai.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition inline-flex items-center gap-1.5">
            <span><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></span> Input Nilai Siswa
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <form method="GET" action="{{ route('web.nilai.index') }}" class="flex flex-col md:flex-row items-center gap-3 text-xs">
            <div class="w-full md:flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa atau NISN..."
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="w-full md:w-48">
                <select name="id_kelas" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('id_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-48">
                <select name="id_mapel" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Semua Mapel --</option>
                    @foreach($mapelList as $m)
                        <option value="{{ $m->id }}" {{ request('id_mapel') == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-slate-900 text-white rounded-xl font-semibold hover:bg-slate-800 transition">Filter</button>
            @if(request('search') || request('id_kelas') || request('id_mapel'))
                <a href="{{ route('web.nilai.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 font-semibold">Siswa &amp; Kelas</th>
                        <th class="py-3.5 px-4 font-semibold">Mata Pelajaran</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Tugas (30%)</th>
                        <th class="py-3.5 px-4 font-semibold text-center">UTS (30%)</th>
                        <th class="py-3.5 px-4 font-semibold text-center">UAS (40%)</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Nilai Akhir</th>
                        <th class="py-3.5 px-4 font-semibold text-center">Predikat</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($nilais as $n)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $n->siswa?->nama }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $n->siswa?->nisn }} &bull; {{ $n->siswa?->kelas?->nama_kelas }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-800">{{ $n->mapel?->nama_mapel }}</div>
                                <div class="text-[10px] text-slate-400">KKM: {{ $n->mapel?->kkm }} &bull; Sem: {{ $n->semester }}</div>
                            </td>
                            <td class="py-3 px-4 text-center font-mono">{{ (float) $n->nilai_tugas }}</td>
                            <td class="py-3 px-4 text-center font-mono">{{ (float) $n->nilai_uts }}</td>
                            <td class="py-3 px-4 text-center font-mono">{{ (float) $n->nilai_uas }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-black {{ $n->nilai_akhir >= ($n->mapel?->kkm ?? 75) ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ (float) $n->nilai_akhir }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800">
                                    {{ $n->predikat }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('web.nilai.rapor', $n->id_siswa) }}" target="_blank" class="text-blue-600 hover:text-blue-700 font-bold" title="Cetak E-Rapor"><svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Rapor</a>
                                <a href="{{ route('web.nilai.edit', $n->id) }}" class="text-amber-600 hover:text-amber-700 font-semibold">Edit</a>
                                <form action="{{ route('web.nilai.destroy', $n->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus data nilai ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-400">Belum ada data nilai akademik yang diinput.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($nilais->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $nilais->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
