@extends('layouts.app')

@section('title', 'Jadwal Perkuliahan')

@section('content')
<div class="space-y-6" x-data="{ viewMode: 'grid' }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-soft">
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800 border border-indigo-200">SIAKAD</span>
                <span class="text-xs text-slate-400">Jadwal &amp; Ruangan Kuliah</span>
                @if(isset($tahunAkademikAktif))
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        T.A. {{ $tahunAkademikAktif->tahun_akademik }} ({{ ucfirst($tahunAkademikAktif->semester) }})
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1.5">Jadwal Perkuliahan</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                @if(auth()->user() && auth()->user()->isMahasiswa())
                    Jadwal perkuliahan mingguan untuk kelas dan semester Anda.
                @elseif(auth()->user() && auth()->user()->isDosen())
                    Jadwal pengajaran perkuliahan mingguan Anda.
                @else
                    Atur alokasi jam tatap muka, kelas kuliah, dosen pengampu, dan ruang perkuliahan.
                @endif
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if(auth()->user() && auth()->user()->isAdmin())
                <a href="{{ route('web.jadwal.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/20 transition inline-flex items-center gap-1.5 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Jadwal Kuliah Baru
                </a>
            @endif
        </div>
    </div>

    <!-- Filter & View Mode Switcher -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-4 flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('web.jadwal.index') }}" class="flex flex-wrap items-center gap-3 text-xs flex-1">
            @if(auth()->user() && auth()->user()->isAdmin())
                <div class="w-full sm:w-48">
                    <select name="id_kelas" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ request('id_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="w-full sm:w-36">
                <select name="hari" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Hari --</option>
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                        <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-44">
                <select name="semester" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Semester --</option>
                    @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" {{ request('semester') == (string)$s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                    <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                    <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Semester Genap</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl font-semibold hover:bg-slate-800 transition">Filter</button>
                @if(request('id_kelas') || request('hari') || request('semester'))
                    <a href="{{ route('web.jadwal.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-semibold transition">Reset</a>
                @endif
            </div>
        </form>

        <!-- View Switcher Tabs -->
        <div class="flex items-center bg-slate-100 p-1 rounded-xl self-start md:self-auto shrink-0">
            <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-slate-600 font-medium hover:text-slate-900'" class="px-3.5 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Grid Mingguan
            </button>
            <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-slate-600 font-medium hover:text-slate-900'" class="px-3.5 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                Tabel List
            </button>
        </div>
    </div>

    <!-- VIEW MODE 1: WEEKLY TIMETABLE GRID -->
    <div x-show="viewMode === 'grid'" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                @php
                    $dayJadwals = $jadwalMingguan[$day] ?? collect();
                @endphp
                <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-3 flex flex-col min-h-[380px]">
                    <!-- Day Header -->
                    <div class="flex items-center justify-between pb-2.5 mb-3 border-b border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                            <h3 class="font-bold text-slate-800 text-sm">{{ $day }}</h3>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-[10px] font-bold">
                            {{ count($dayJadwals) }}
                        </span>
                    </div>

                    <!-- Card Items -->
                    <div class="space-y-3 flex-1 overflow-y-auto">
                        @forelse($dayJadwals as $item)
                            <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-2">
                                <div>
                                    <div class="flex items-center justify-between text-[11px] font-mono text-indigo-600 font-bold bg-indigo-50 px-2 py-1 rounded-md mb-2">
                                        <span>{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</span>
                                        <span class="text-[10px] bg-indigo-200 text-indigo-900 px-1.5 py-0.2 rounded">R. {{ $item->ruangan ?? '-' }}</span>
                                    </div>
                                    <h4 class="font-bold text-slate-900 text-xs line-clamp-2">{{ $item->mapel?->nama_mapel ?? 'Mata Kuliah' }}</h4>
                                    <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $item->mapel?->kode_mapel ?? '-' }} • SKS {{ $item->mapel?->sks ?? 2 }}</p>
                                </div>

                                <div class="pt-2 border-t border-slate-100 space-y-1 text-[11px]">
                                    <div class="flex items-center justify-between text-slate-600">
                                        <span class="text-[10px] text-slate-400">Dosen:</span>
                                        <span class="font-medium text-slate-800 truncate max-w-[120px]" title="{{ $item->guru?->nama_guru }}">{{ $item->guru?->nama_guru ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-slate-600">
                                        <span class="text-[10px] text-slate-400">Kelas:</span>
                                        <span class="font-semibold text-indigo-600">{{ $item->kelas?->nama_kelas ?? '-' }}</span>
                                    </div>
                                </div>

                                @if(auth()->user() && auth()->user()->isAdmin())
                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2 text-[10px]">
                                        <a href="{{ route('web.jadwal.edit', $item->id) }}" class="text-amber-600 hover:underline font-semibold">Edit</a>
                                        <form action="{{ route('web.jadwal.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:underline font-semibold">Hapus</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-10 text-center text-slate-400 space-y-1">
                                <svg class="w-8 h-8 text-slate-300 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-[11px]">Tidak ada perkuliahan</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- VIEW MODE 2: TABLE LIST -->
    <div x-show="viewMode === 'table'" class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-slate-50/50 text-slate-500 uppercase tracking-wider text-[10px] font-semibold">
                        <th class="py-3.5 px-4 font-semibold">Hari &amp; Jam</th>
                        <th class="py-3.5 px-4 font-semibold">Kelas Kuliah</th>
                        <th class="py-3.5 px-4 font-semibold">Mata Kuliah</th>
                        <th class="py-3.5 px-4 font-semibold">Dosen Pengampu</th>
                        <th class="py-3.5 px-4 font-semibold">Ruang Perkuliahan</th>
                        @if(auth()->user() && auth()->user()->isAdmin())
                            <th class="py-3.5 px-4 font-semibold text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jadwals as $jadwal)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-800 border border-indigo-200/60 mr-2">
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
                                <div class="text-[10px] text-slate-400 font-mono">{{ $jadwal->mapel?->kode_mapel }} • Semester {{ $jadwal->mapel?->semester ?? $jadwal->mapel?->semester_rekomendasi ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">
                                {{ $jadwal->guru?->nama_guru }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-mono border border-slate-200">
                                    {{ $jadwal->ruangan ?? 'Ruang Kuliah' }}
                                </span>
                            </td>
                            @if(auth()->user() && auth()->user()->isAdmin())
                                <td class="py-3.5 px-4 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('web.jadwal.edit', $jadwal->id) }}" class="text-amber-600 hover:text-amber-700 font-semibold">Edit</a>
                                    <form action="{{ route('web.jadwal.destroy', $jadwal->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus jadwal perkuliahan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold">Hapus</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ (auth()->user() && auth()->user()->isAdmin()) ? 6 : 5 }}" class="py-10 text-center text-slate-400">Belum ada jadwal perkuliahan yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jadwals->hasPages())
            <div class="p-4 border-t border-slate-200/80">
                {{ $jadwals->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
