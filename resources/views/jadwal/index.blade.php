@extends('layouts.app')

@section('title', 'Jadwal Pelajaran Sekolah')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800">SIAKAD</span>
                <span class="text-xs text-slate-400">Jadwal &amp; Ruangan</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1">Jadwal Pelajaran</h1>
            <p class="text-xs text-slate-500 mt-0.5">Atur alokasi jam mengajar, kelas, mata pelajaran, guru pengampu, dan ruangan</p>
        </div>
        <a href="{{ route('web.jadwal.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition inline-flex items-center gap-1.5">
            <span>➕</span> Tambah Jadwal Baru
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <form method="GET" action="{{ route('web.jadwal.index') }}" class="flex flex-col sm:flex-row items-center gap-3 text-xs">
            <div class="w-full sm:w-60">
                <select name="id_kelas" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('id_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-48">
                <select name="hari" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Semua Hari --</option>
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                        <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-slate-900 text-white rounded-xl font-semibold hover:bg-slate-800 transition">Filter</button>
            @if(request('id_kelas') || request('hari'))
                <a href="{{ route('web.jadwal.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 font-semibold">Hari &amp; Jam</th>
                        <th class="py-3.5 px-4 font-semibold">Kelas</th>
                        <th class="py-3.5 px-4 font-semibold">Mata Pelajaran</th>
                        <th class="py-3.5 px-4 font-semibold">Guru Pengampu</th>
                        <th class="py-3.5 px-4 font-semibold">Ruangan</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jadwals as $jadwal)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-800 mr-2">
                                    {{ $jadwal->hari }}
                                </span>
                                <span class="font-mono text-slate-700 font-semibold">
                                    {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $jadwal->kelas?->nama_kelas }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $jadwal->mapel?->nama_mapel }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $jadwal->mapel?->kode_mapel }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">
                                {{ $jadwal->guru?->nama_guru }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px]">
                                    {{ $jadwal->ruangan ?? 'Kelas' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('web.jadwal.edit', $jadwal->id) }}" class="text-amber-600 hover:text-amber-700 font-semibold">Edit</a>
                                <form action="{{ route('web.jadwal.destroy', $jadwal->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">Belum ada data jadwal pelajaran yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jadwals->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $jadwals->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
