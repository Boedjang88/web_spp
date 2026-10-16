@extends('layouts.app')

@section('title', 'Smart KRS & Rencana Studi Online')

@section('content')
<div class="space-y-6">

    <!-- Top Header Banner (Google Academic Workspace Style) -->
    <div class="bg-white dark:bg-workspace-surface-dark p-5 sm:p-6 rounded-2xl border border-slate-200/90 dark:border-slate-800 text-slate-900 dark:text-slate-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 shadow-2xs relative">
        <div class="relative z-10">
            <div class="flex flex-wrap items-center gap-2 mb-2 font-mono">
                <span class="px-2.5 py-0.5 bg-brand-50 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 border border-brand-200/80 dark:border-brand-800 text-[10px] font-bold rounded-full uppercase tracking-wider">
                    SMART KRS ENGINE &bull; LOCK VERIFIED
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-sans">T.A. {{ $activeYear?->nama_tahun }} ({{ $activeYear?->semester }})</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Kartu Rencana Studi (KRS) Online</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xl leading-relaxed">
                Pengisian rencana studi terpadu dengan proteksi batas SKS, prasyarat mata kuliah, kuota kelas, dan bentrok jadwal otomatis.
            </p>
        </div>

        <!-- SKS Meter Indicator -->
        <div class="relative z-10 w-full md:w-auto bg-slate-50 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 rounded-xl p-4 flex items-center justify-around sm:justify-start gap-6 font-mono shadow-2xs">
            <div>
                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider block">Batas SKS</span>
                <span class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">{{ $krs?->max_sks_diizinkan ?? 24 }} <span class="text-xs text-brand-600 dark:text-brand-400">SKS</span></span>
            </div>
            <div class="w-px h-8 bg-slate-200 dark:border-slate-700"></div>
            <div>
                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider block">SKS Terambil</span>
                <span class="text-lg sm:text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ $krs?->total_sks_diambil ?? 0 }} <span class="text-xs text-brand-600 dark:text-brand-400">SKS</span></span>
            </div>
        </div>
    </div>

    <!-- Filter Bar: Semester & Program Studi -->
    <x-card :padding="true">
        <form method="GET" action="{{ route('siakad.krs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
                <label for="semester_filter" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1 font-mono">Filter Semester</label>
                <select id="semester_filter" name="semester_filter" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:ring-2 focus:ring-brand-500">
                    <option value="">Semua Semester</option>
                    @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" {{ request('semester_filter') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="prodi_filter" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1 font-mono">Filter Program Studi</label>
                <select id="prodi_filter" name="prodi_filter" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:ring-2 focus:ring-brand-500">
                    <option value="">Semua Jurusan</option>
                    <option value="IF" {{ request('prodi_filter') == 'IF' ? 'selected' : '' }}>Teknik Informatika (S1)</option>
                    <option value="SI" {{ request('prodi_filter') == 'SI' ? 'selected' : '' }}>Sistem Informasi (S1)</option>
                    <option value="BD" {{ request('prodi_filter') == 'BD' ? 'selected' : '' }}>Bisnis Digital (S1)</option>
                    <option value="TK" {{ request('prodi_filter') == 'TK' ? 'selected' : '' }}>Teknik Komputer (D3)</option>
                </select>
            </div>
            <div class="flex items-end">
                <a href="{{ route('siakad.krs.index') }}" class="w-full py-2 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl text-center transition min-h-9.5 flex items-center justify-center">
                    Reset Filter
                </a>
            </div>
        </form>
    </x-card>

    <!-- Financial Lock Warning if Unpaid -->
    @if(!$isCleared)
    <x-alert type="warning" title="Pengisian KRS Terkunci (Syarat Keuangan Belum Terpenuhi)">
        Akses KRS terkunci karena status pembayaran UKT semester {{ $activeYear?->nama_tahun }} belum diselesaikan. Pembayaran via Virtual Account Bank akan membuka kuncian secara otomatis.
    </x-alert>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Enrolled Courses in KRS (7 Cols) -->
        <div class="lg:col-span-7 space-y-4">
            <x-card title="Rencana Studi Terpilih" subtitle="Kelas perkuliahan yang telah diambil semester ini" :padding="false">
                <x-slot:action>
                    <x-status-badge :status="$krs?->status_krs === 'Disetujui' ? 'success' : 'warning'">
                        Status: {{ $krs?->status_krs ?? 'Draft' }}
                    </x-status-badge>
                </x-slot:action>

                @if($krs && $krs->details->count() > 0)
                    <!-- Desktop Table View (>= 640px) -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 uppercase text-[10px] font-semibold border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="py-3 px-4">Kode &amp; Mata Kuliah</th>
                                    <th class="py-3 px-3">Kelas</th>
                                    <th class="py-3 px-3">SKS</th>
                                    <th class="py-3 px-3">Jadwal &amp; Ruang</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($krs->details as $detail)
                                @php
                                    $kelas = $detail->kelasKuliah;
                                    $mk = $kelas?->mataKuliah;
                                    $jadwal = $kelas?->jadwalKuliahs->first();
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                    <td class="py-3 px-4">
                                        <span class="font-mono text-[10px] font-bold text-brand-600 dark:text-brand-400 block">{{ $mk?->kode_mk }}</span>
                                        <span class="font-bold text-slate-900 dark:text-white text-xs">{{ $mk?->nama_mk }}</span>
                                    </td>
                                    <td class="py-3 px-3 font-bold text-slate-800 dark:text-slate-200">{{ $kelas?->nama_kelas }}</td>
                                    <td class="py-3 px-3 font-semibold text-slate-700 dark:text-slate-300">{{ $mk?->sks_total }} SKS</td>
                                    <td class="py-3 px-3 text-[11px] text-slate-600 dark:text-slate-400">
                                        {{ $jadwal ? $jadwal->hari . ', ' . substr($jadwal->jam_mulai, 0, 5) . '-' . substr($jadwal->jam_selesai, 0, 5) : '-' }}
                                        <span class="block text-slate-400 font-mono text-[10px]">{{ $jadwal?->ruangan?->nama_ruangan }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        @if($krs->status_krs !== 'Disetujui')
                                        <form action="{{ route('siakad.krs.destroy', $detail->id) }}" method="POST" onsubmit="return confirm('Batalkan mata kuliah ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 dark:text-rose-400 hover:underline font-bold text-xs py-1 px-2.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 transition min-h-7.5">
                                                Batal
                                            </button>
                                        </form>
                                        @else
                                        <span class="text-emerald-700 dark:text-emerald-400 font-bold text-[10px] font-mono uppercase bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">Terkunci</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Adaptive Cards (< 640px) -->
                    <div class="block sm:hidden divide-y divide-slate-100 dark:divide-slate-800 p-4 space-y-3">
                        @foreach($krs->details as $detail)
                        @php
                            $kelas = $detail->kelasKuliah;
                            $mk = $kelas?->mataKuliah;
                            $jadwal = $kelas?->jadwalKuliahs->first();
                        @endphp
                        <div class="pt-3 first:pt-0 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-mono text-[10px] font-bold text-brand-600 dark:text-brand-400 block">{{ $mk?->kode_mk }} &bull; Kelas {{ $kelas?->nama_kelas }}</span>
                                    <h3 class="font-bold text-slate-900 dark:text-white text-xs">{{ $mk?->nama_mk }}</h3>
                                </div>
                                <span class="px-2 py-0.5 rounded-md font-mono text-[10px] font-bold bg-brand-50 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 border border-brand-200 dark:border-brand-800 shrink-0">
                                    {{ $mk?->sks_total }} SKS
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">
                                Jadwal: <strong class="text-slate-700 dark:text-slate-300">{{ $jadwal ? $jadwal->hari . ', ' . substr($jadwal->jam_mulai, 0, 5) . '-' . substr($jadwal->jam_selesai, 0, 5) : 'Reguler' }}</strong> &bull; Ruang: <strong class="text-slate-700 dark:text-slate-300">{{ $jadwal?->ruangan?->nama_ruangan ?? 'Teori' }}</strong>
                            </div>
                            <div class="pt-1">
                                @if($krs->status_krs !== 'Disetujui')
                                <form action="{{ route('siakad.krs.destroy', $detail->id) }}" method="POST" onsubmit="return confirm('Batalkan mata kuliah ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-rose-600 dark:text-rose-400 font-bold text-xs py-2 px-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 transition min-h-9.5 flex items-center justify-center">
                                        Batalkan Mata Kuliah Ini
                                    </button>
                                </form>
                                @else
                                <div class="w-full text-center text-emerald-700 dark:text-emerald-400 font-bold text-xs py-1.5 px-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800">
                                    Terkunci (KRS Telah Disetujui Dosen PA)
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-slate-400 font-mono p-5">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Belum ada mata kuliah yang dipilih</span>
                        <p class="text-[11px] text-slate-500 mt-0.5">Pilih kelas kuliah dari penawaran di samping.</p>
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Right Column: Available Classes for Registration (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <x-card title="Penawaran Kelas Kuliah" subtitle="Pilih kelas kuliah semester aktif" :padding="false">
                <x-slot:action>
                    <span class="text-[10px] text-brand-600 dark:text-brand-300 bg-brand-50 dark:bg-brand-950/60 px-2.5 py-0.5 rounded-full font-bold border border-brand-200 dark:border-brand-800">
                        {{ $availableClasses->count() }} Kelas
                    </span>
                </x-slot:action>

                <div class="p-4 space-y-3 max-h-150 overflow-y-auto pr-1 custom-scrollbar">
                    @forelse($availableClasses as $classItem)
                    @php
                        $isFull = $classItem->total_terisi >= $classItem->kuota_maksimal;
                        $jadwal = $classItem->jadwalKuliahs->first();
                    @endphp
                    <div class="p-4 rounded-xl border {{ $isFull ? 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/40 opacity-60' : 'border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-950/50 hover:border-brand-500' }} transition flex flex-col justify-between gap-3 shadow-2xs">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5 font-mono">
                                <span class="text-[10px] font-bold text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-950/60 px-2 py-0.5 rounded border border-brand-200 dark:border-brand-800">
                                    {{ $classItem->mataKuliah?->kode_mk }} &bull; {{ $classItem->nama_kelas }}
                                </span>
                                <span class="text-[11px] font-bold {{ $isFull ? 'text-rose-600 dark:text-rose-400' : 'text-slate-700 dark:text-slate-300' }}">
                                    {{ $classItem->total_terisi }}/{{ $classItem->kuota_maksimal }} Kuota
                                </span>
                            </div>
                            <h3 class="font-bold text-xs text-slate-900 dark:text-white">{{ $classItem->mataKuliah?->nama_mk }}</h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                Dosen: <strong class="text-slate-700 dark:text-slate-300">{{ $classItem->dosen?->nama_dosen ?? $classItem->dosen?->nama ?? 'Dosen Pengampu' }}</strong>
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 font-mono">
                                {{ $jadwal ? $jadwal->hari . ', ' . substr($jadwal->jam_mulai, 0, 5) . '-' . substr($jadwal->jam_selesai, 0, 5) : 'Jadwal Reguler' }} &bull; {{ $jadwal?->ruangan?->nama_ruangan ?? 'Ruang Teori' }}
                            </p>
                        </div>

                        <form method="POST" action="{{ route('siakad.krs.store') }}">
                            @csrf
                            <input type="hidden" name="id_kelas_kuliah" value="{{ $classItem->id }}">
                            <button type="submit" {{ ($isFull || !$isCleared) ? 'disabled' : '' }}
                                class="w-full py-2.5 px-3 bg-brand-600 hover:bg-brand-500 disabled:bg-slate-200 dark:disabled:bg-slate-800 disabled:text-slate-400 dark:disabled:text-slate-500 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm min-h-10.5 cursor-pointer disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                <span>{{ $isFull ? 'Kelas Penuh' : 'Ambil Kelas Ini' }}</span>
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-400 text-xs font-mono">
                        Tidak ada penawaran kelas perkuliahan yang sesuai dengan filter.
                    </div>
                    @endforelse
                </div>
            </x-card>
        </div>

    </div>

</div>
@endsection
