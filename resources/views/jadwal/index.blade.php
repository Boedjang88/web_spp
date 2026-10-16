@extends('layouts.app')

@section('title', 'Jadwal Perkuliahan')

@section('content')
@php
    $currentView = request('view', 'grid');
    if (!in_array($currentView, ['grid', 'table'])) {
        $currentView = 'grid';
    }
@endphp
<div class="space-y-6" x-data="{ viewMode: '{{ $currentView }}', activeDayTab: 'all' }">

    <!-- Top Header Banner (Enterprise Modern Style) -->
    <div class="bg-linear-to-r from-slate-900 via-indigo-950 to-blue-950 p-6 md:p-8 rounded-2xl border border-indigo-900/40 text-white flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 shadow-xl relative overflow-hidden print:bg-white print:text-black print:p-0 print:border-none print:shadow-none">
        <!-- Background decorative glow -->
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none print:hidden"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none print:hidden"></div>

        <div class="relative z-10">
            <div class="flex flex-wrap items-center gap-2 mb-2 font-mono">
                <span class="px-2.5 py-0.5 bg-indigo-500/20 text-indigo-200 border border-indigo-400/30 text-[10px] font-bold rounded-full uppercase tracking-wider">
                    SIAKAD 5.6 CLOUD &bull; PERKULIAHAN
                </span>
                @if(isset($tahunAkademikAktif))
                    <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[11px] font-semibold rounded-full flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        T.A. {{ $tahunAkademikAktif->tahun_akademik ?? $tahunAkademikAktif->nama_tahun ?? '2025/2026' }} ({{ ucfirst($tahunAkademikAktif->semester ?? 'Ganjil') }})
                    </span>
                @endif
                @if($hariIni)
                    <span class="px-2.5 py-0.5 bg-blue-500/20 text-blue-300 border border-blue-400/30 text-[11px] font-medium rounded-full">
                        Hari Ini: <strong class="text-white">{{ $hariIni }}</strong>
                    </span>
                @endif
            </div>

            <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Jadwal Perkuliahan Mingguan</h1>
            
            <p class="text-xs md:text-sm text-indigo-200/80 mt-1 max-w-2xl leading-relaxed">
                @if(auth()->user() && auth()->user()->isMahasiswa())
                    Jadwal perkuliahan mingguan untuk <span class="font-semibold text-white">{{ auth()->user()->siswa?->nama ?? auth()->user()->mahasiswa?->nama_lengkap ?? auth()->user()->name }}</span>@if(auth()->user()->siswa?->kelas) (Kelas {{ auth()->user()->siswa->kelas->nama_kelas }})@endif.
                @elseif(auth()->user() && auth()->user()->isDosen())
                    Alokasi jam mengajar tatap muka untuk Dosen <span class="font-semibold text-white">{{ auth()->user()->guru?->nama_guru ?? auth()->user()->name }}</span>.
                @else
                    Kelola dan pantau alokasi jam tatap muka, kelas kuliah, dosen pengampu, dan pemakaian ruang perkuliahan.
                @endif
            </p>
        </div>

        <!-- Action Buttons (Print & Add) -->
        <div class="relative z-10 flex flex-wrap items-center gap-2.5 self-stretch sm:self-auto print:hidden">
            <button onclick="window.print()" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/15 rounded-xl text-xs font-semibold backdrop-blur-sm transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Jadwal</span>
            </button>

            @if(auth()->user() && auth()->user()->isAdmin())
                <a href="{{ route('web.jadwal.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-indigo-600/30 transition flex items-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Jadwal</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Stats Cards (4 Columns) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 print:hidden">
        <!-- Card 1: Total Perkuliahan -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 p-4 shadow-soft flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Matakuliah</p>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $totalJadwal }}</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Jadwal</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Beban SKS -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 p-4 shadow-soft flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Beban SKS</p>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $totalSks }}</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">SKS</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Hari Aktif Kuliah -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 p-4 shadow-soft flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hari Kuliah Aktif</p>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $totalHariAktif }}</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Hari / Pekan</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Ruangan Terpakai -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 p-4 shadow-soft flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-purple-50 dark:bg-purple-950/60 border border-purple-200 dark:border-purple-800 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ruang Perkuliahan</p>
                <div class="flex items-baseline gap-1.5 mt-0.5">
                    <span class="text-xl font-extrabold text-purple-600 dark:text-purple-400">{{ $totalRuangan }}</span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Ruangan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar & View Mode Switcher -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 p-4 shadow-soft flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4 print:hidden">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('web.jadwal.index') }}" class="flex flex-wrap items-center gap-2.5 text-xs flex-1">
            <input type="hidden" name="view" id="filter-view-input" value="{{ $currentView }}" :value="viewMode">

            @if(auth()->user() && auth()->user()->isAdmin())
                <div class="w-full sm:w-48">
                    <select name="id_kelas" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ request('id_kelas') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="w-full sm:w-36">
                <select name="hari" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Hari --</option>
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $h)
                        <option value="{{ $h }}" {{ request('hari') == $h ? 'selected' : '' }}>{{ $h }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-40">
                <select name="semester" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Semester --</option>
                    @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" {{ request('semester') == (string)$s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                    <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                    <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Semester Genap</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-700 text-white rounded-xl font-semibold transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                </button>
                @if(request('id_kelas') || request('hari') || request('semester'))
                    <a href="{{ route('web.jadwal.index', ['view' => $currentView]) }}" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-200 rounded-xl font-semibold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- View Switcher Tabs (Pill Design - Mutually Exclusive) -->
        <div class="flex items-center bg-slate-100 dark:bg-slate-900 p-1 rounded-xl self-start lg:self-auto shrink-0 border border-slate-200/60 dark:border-slate-800" role="tablist" aria-label="Pilihan Tampilan Jadwal">
            <button type="button" id="tab-btn-grid" onclick="setJadwalView('grid')" @click="viewMode = 'grid'"
                :class="viewMode === 'grid' ? 'bg-white dark:bg-indigo-600 text-indigo-700 dark:text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 font-medium hover:text-slate-900 dark:hover:text-white'"
                class="px-3.5 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5 {{ $currentView === 'grid' ? 'bg-white dark:bg-indigo-600 text-indigo-700 dark:text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 font-medium hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Grid Mingguan</span>
            </button>
            <button type="button" id="tab-btn-table" onclick="setJadwalView('table')" @click="viewMode = 'table'"
                :class="viewMode === 'table' ? 'bg-white dark:bg-indigo-600 text-indigo-700 dark:text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 font-medium hover:text-slate-900 dark:hover:text-white'"
                class="px-3.5 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5 {{ $currentView === 'table' ? 'bg-white dark:bg-indigo-600 text-indigo-700 dark:text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 font-medium hover:text-slate-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span>Tabel List</span>
            </button>
        </div>
    </div>

    <!-- VIEW MODE 1: WEEKLY TIMETABLE GRID (ONLY SHOWN WHEN viewMode === 'grid') -->
    <div id="jadwal-grid-container" x-show="viewMode === 'grid'" x-cloak class="space-y-4" style="{{ $currentView === 'grid' ? '' : 'display: none;' }}">
        <!-- Day Filter Tabs Bar (Useful on mobile & tablet) -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none print:hidden">
            <button @click="activeDayTab = 'all'" :class="activeDayTab === 'all' ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50'" class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition flex items-center gap-1.5">
                <span>Semua Hari</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-white/20 dark:bg-white/10">{{ $totalJadwal }}</span>
            </button>

            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                @php
                    $countDay = count($jadwalMingguan[$day] ?? []);
                    $isToday = ($day === $hariIni);
                @endphp
                <button @click="activeDayTab = '{{ $day }}'" :class="activeDayTab === '{{ $day }}' ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50'" class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition flex items-center gap-1.5">
                    <span>{{ $day }}</span>
                    @if($isToday)
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    @endif
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $countDay > 0 ? 'bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold' : 'bg-slate-100 dark:bg-slate-700 text-slate-400' }}">
                        {{ $countDay }}
                    </span>
                </button>
            @endforeach
        </div>

        <!-- 6-Day Columns Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 print:grid-cols-3 print:gap-3">
            @php
                $dayThemes = [
                    'Senin' => ['border' => 'border-sky-500', 'bg' => 'bg-sky-50 dark:bg-sky-950/40', 'badge' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/60 dark:text-sky-200', 'dot' => 'bg-sky-500'],
                    'Selasa' => ['border' => 'border-indigo-500', 'bg' => 'bg-indigo-50 dark:bg-indigo-950/40', 'badge' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-200', 'dot' => 'bg-indigo-500'],
                    'Rabu' => ['border' => 'border-emerald-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-950/40', 'badge' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200', 'dot' => 'bg-emerald-500'],
                    'Kamis' => ['border' => 'border-amber-500', 'bg' => 'bg-amber-50 dark:bg-amber-950/40', 'badge' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200', 'dot' => 'bg-amber-500'],
                    'Jumat' => ['border' => 'border-purple-500', 'bg' => 'bg-purple-50 dark:bg-purple-950/40', 'badge' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/60 dark:text-purple-200', 'dot' => 'bg-purple-500'],
                    'Sabtu' => ['border' => 'border-rose-500', 'bg' => 'bg-rose-50 dark:bg-rose-950/40', 'badge' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200', 'dot' => 'bg-rose-500'],
                ];
            @endphp

            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $day)
                @php
                    $dayJadwals = $jadwalMingguan[$day] ?? collect();
                    $isToday = ($day === $hariIni);
                    $theme = $dayThemes[$day] ?? $dayThemes['Senin'];
                @endphp
                <div x-show="activeDayTab === 'all' || activeDayTab === '{{ $day }}'" class="bg-slate-50/80 dark:bg-slate-900/70 border {{ $isToday ? 'border-indigo-400 dark:border-indigo-500 ring-2 ring-indigo-400/30' : 'border-slate-200/90 dark:border-slate-700' }} rounded-2xl p-3 flex flex-col min-h-105 transition duration-200 print:min-h-0 print:border-slate-300">
                    <!-- Day Header -->
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200 dark:border-slate-700/80">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $theme['dot'] }}"></span>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm tracking-tight">{{ $day }}</h3>
                            @if($isToday)
                                <span class="px-1.5 py-0.5 rounded-md bg-emerald-500 text-white text-[9px] font-bold uppercase tracking-wider print:hidden">
                                    Hari Ini
                                </span>
                            @endif
                        </div>
                        <span class="px-2 py-0.5 rounded-full {{ $theme['badge'] }} text-[10px] font-bold font-mono">
                            {{ count($dayJadwals) }} Kuliah
                        </span>
                    </div>

                    <!-- Cards Container -->
                    <div class="space-y-3 flex-1 overflow-y-auto">
                        @forelse($dayJadwals as $item)
                            <div class="bg-white dark:bg-slate-800 rounded-xl p-3.5 border border-slate-200/90 dark:border-slate-700/90 shadow-soft hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-500 transition-all flex flex-col justify-between space-y-2.5 group relative">
                                
                                <!-- Top: Time & Room -->
                                <div class="flex items-center justify-between gap-1 text-[11px] font-mono">
                                    <div class="flex items-center gap-1 font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 px-2 py-1 rounded-md border border-indigo-200/50 dark:border-indigo-800/50">
                                        <svg class="w-3 h-3 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</span>
                                    </div>
                                    <span class="text-[10px] font-semibold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 px-1.5 py-1 rounded border border-slate-200 dark:border-slate-600 truncate max-w-20" title="{{ $item->ruangan ?? 'Ruang Kuliah' }}">
                                        {{ $item->ruangan ?? 'R. Kuliah' }}
                                    </span>
                                </div>

                                <!-- Middle: Course Name & Code -->
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-xs leading-snug line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition" title="{{ $item->mapel?->nama_mapel }}">
                                        {{ $item->mapel?->nama_mapel ?? 'Mata Kuliah' }}
                                    </h4>
                                    <div class="flex items-center gap-1.5 text-[10px] text-slate-500 dark:text-slate-400 font-mono mt-1 flex-wrap">
                                        <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $item->mapel?->kode_mapel ?? '-' }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $item->mapel?->sks ?? $item->mapel?->sks_total ?? 2 }} SKS</span>
                                        @if($item->mapel?->semester || $item->mapel?->semester_rekomendasi)
                                             <span>&bull;</span>
                                            <span class="text-indigo-600 dark:text-indigo-400 font-semibold">Sem. {{ $item->mapel?->semester ?? $item->mapel?->semester_rekomendasi }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Lecturer & Class Details -->
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-700/80 space-y-1 text-[11px]">
                                    <!-- Lecturer -->
                                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-300">
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center gap-1 shrink-0">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            Dosen:
                                        </span>
                                        <span class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-27.5" title="{{ $item->guru?->nama_guru }}">
                                            {{ $item->guru?->nama_guru ?? '-' }}
                                        </span>
                                    </div>

                                    <!-- Class -->
                                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-300">
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center gap-1 shrink-0">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            Kelas:
                                        </span>
                                        <span class="font-semibold text-indigo-600 dark:text-indigo-400">
                                            {{ $item->kelas?->nama_kelas ?? '-' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Admin Actions (Edit/Delete) -->
                                @if(auth()->user() && auth()->user()->isAdmin())
                                    <div class="pt-2 border-t border-slate-100 dark:border-slate-700/80 flex items-center justify-end gap-3 text-[10px] print:hidden">
                                        <a href="{{ route('web.jadwal.edit', $item->id) }}" class="text-amber-600 dark:text-amber-400 hover:underline font-semibold flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </a>
                                        <form action="{{ route('web.jadwal.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal perkuliahan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 dark:text-rose-400 hover:underline font-semibold flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-12 text-center text-slate-400 dark:text-slate-500 space-y-1.5 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                                <svg class="w-7 h-7 text-slate-300 dark:text-slate-600 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-[11px] font-medium">Tidak ada perkuliahan</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- VIEW MODE 2: TABLE LIST (ONLY SHOWN WHEN viewMode === 'table') -->
    <div id="jadwal-table-container" x-show="viewMode === 'table'" x-cloak class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 shadow-soft overflow-hidden" style="{{ $currentView === 'table' ? '' : 'display: none;' }}">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200/80 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10px] font-semibold">
                        <th class="py-3.5 px-4 font-semibold">Hari &amp; Jam Kuliah</th>
                        <th class="py-3.5 px-4 font-semibold">Kelas Kuliah</th>
                        <th class="py-3.5 px-4 font-semibold">Mata Kuliah</th>
                        <th class="py-3.5 px-4 font-semibold">SKS / Semester</th>
                        <th class="py-3.5 px-4 font-semibold">Dosen Pengampu</th>
                        <th class="py-3.5 px-4 font-semibold">Ruang Perkuliahan</th>
                        @if(auth()->user() && auth()->user()->isAdmin())
                            <th class="py-3.5 px-4 font-semibold text-right print:hidden">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/80">
                    @forelse($jadwals as $jadwal)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60 mr-2 font-mono">
                                    {{ $jadwal->hari }}
                                </span>
                                <span class="font-mono text-slate-800 dark:text-slate-200 font-semibold">
                                    {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold">
                                    {{ $jadwal->kelas?->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $jadwal->mapel?->nama_mapel ?? 'Mata Kuliah' }}</div>
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">{{ $jadwal->mapel?->kode_mapel ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-mono text-xs font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $jadwal->mapel?->sks ?? $jadwal->mapel?->sks_total ?? 2 }} SKS
                                </div>
                                <div class="text-[10px] text-indigo-600 dark:text-indigo-400 font-medium">
                                    Semester {{ $jadwal->mapel?->semester ?? $jadwal->mapel?->semester_rekomendasi ?? '-' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800 dark:text-slate-200">
                                {{ $jadwal->guru?->nama_guru ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-[11px] font-mono border border-slate-200 dark:border-slate-600">
                                    {{ $jadwal->ruangan ?? 'Ruang Kuliah' }}
                                </span>
                            </td>
                            @if(auth()->user() && auth()->user()->isAdmin())
                                <td class="py-3.5 px-4 text-right print:hidden">
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        <a href="{{ route('web.jadwal.edit', $jadwal->id) }}"
                                            class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 hover:bg-amber-100 dark:hover:bg-amber-900/60 border border-amber-200/70 dark:border-amber-800/70 px-2.5 py-1 rounded-lg transition" title="Edit Jadwal">
                                            <svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </a>
                                        <form action="{{ route('web.jadwal.destroy', $jadwal->id) }}" method="POST" class="inline-block"
                                            onsubmit="return confirm('Hapus jadwal perkuliahan {{ $jadwal->mapel?->nama_mapel }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 text-[11px] font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/60 hover:text-rose-700 dark:hover:text-rose-300 border border-rose-200/70 dark:border-rose-800/70 px-2 py-1 rounded-lg transition" title="Hapus Jadwal">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ (auth()->user() && auth()->user()->isAdmin()) ? 7 : 6 }}" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                Belum ada jadwal perkuliahan yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jadwals->hasPages())
            <div class="p-4 border-t border-slate-200/80 dark:border-slate-700 print:hidden">
                {{ $jadwals->links() }}
            </div>
        @endif
    </div>

</div>

<!-- View Switcher Script -->
<script>
    function setJadwalView(mode) {
        // 1. Update Alpine reactivity state if available
        const root = document.querySelector('[x-data]');
        if (window.Alpine && root && root._x_dataStack && root._x_dataStack[0]) {
            root._x_dataStack[0].viewMode = mode;
        }

        // 2. Guaranteed zero-latency DOM display toggle
        const gridEl = document.getElementById('jadwal-grid-container');
        const tableEl = document.getElementById('jadwal-table-container');
        if (gridEl && tableEl) {
            if (mode === 'grid') {
                gridEl.style.display = '';
                tableEl.style.display = 'none';
            } else {
                gridEl.style.display = 'none';
                tableEl.style.display = '';
            }
        }

        // 3. Update tab buttons active classes
        const btnGrid = document.getElementById('tab-btn-grid');
        const btnTable = document.getElementById('tab-btn-table');
        const activeClasses = ['bg-white', 'dark:bg-indigo-600', 'text-indigo-700', 'dark:text-white', 'shadow-sm', 'font-bold'];
        const inactiveClasses = ['text-slate-600', 'dark:text-slate-400', 'font-medium'];

        if (btnGrid && btnTable) {
            if (mode === 'grid') {
                btnGrid.classList.add(...activeClasses);
                btnGrid.classList.remove(...inactiveClasses);
                btnTable.classList.remove(...activeClasses);
                btnTable.classList.add(...inactiveClasses);
            } else {
                btnTable.classList.add(...activeClasses);
                btnTable.classList.remove(...inactiveClasses);
                btnGrid.classList.remove(...activeClasses);
                btnGrid.classList.add(...inactiveClasses);
            }
        }

        // 4. Update hidden input in filter form
        const viewInput = document.getElementById('filter-view-input');
        if (viewInput) viewInput.value = mode;

        // 5. Update browser URL query parameter without page reload
        try {
            const url = new URL(window.location);
            url.searchParams.set('view', mode);
            window.history.replaceState({}, '', url);
        } catch(e) {}
    }
</script>

<!-- Print Styles -->
<style>
@media print {
    aside, header, footer, .print\:hidden {
        display: none !important;
    }
    body {
        background-color: white !important;
        color: black !important;
    }
    .space-y-6 {
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>
@endsection
