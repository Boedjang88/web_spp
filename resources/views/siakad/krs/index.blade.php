@extends('layouts.app')

@section('title', 'Smart KRS & Rencana Studi Online')

@section('content')
<div class="space-y-6">

    <!-- Top Header Banner (Deep Ocean Navy & Royal Blue) -->
    <div class="bg-gradient-to-r from-blue-900 via-slate-900 to-indigo-950 p-6 md:p-8 rounded-2xl border border-blue-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-5 shadow-xl relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex flex-wrap items-center gap-2 mb-2 font-mono">
                <span class="px-2.5 py-0.5 bg-blue-500/20 text-blue-200 border border-blue-400/30 text-[10px] font-bold rounded-full uppercase tracking-wider">
                    SMART KRS ENGINE &bull; LOCK VERIFIED
                </span>
                <span class="text-xs text-blue-200/80">T.A. {{ $activeYear?->nama_tahun }} ({{ $activeYear?->semester }})</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Kartu Rencana Studi (KRS) Online</h1>
            <p class="text-xs text-blue-200/80 mt-1">Pengisian KRS berkecepatan tinggi dengan proteksi kuota &amp; bentrok jadwal otomatis.</p>
        </div>

        <!-- SKS Meter Indicator -->
        <div class="relative z-10 w-full md:w-auto bg-[#0b132b] border border-blue-900/80 rounded-xl p-4 flex items-center justify-around sm:justify-start gap-6 font-mono shadow-inner">
            <div>
                <span class="text-[10px] text-blue-300/70 font-semibold uppercase tracking-wider block">Batas SKS</span>
                <span class="text-xl font-bold text-white">{{ $krs?->max_sks_diizinkan ?? 24 }} <span class="text-xs text-blue-300">SKS</span></span>
            </div>
            <div class="w-px h-8 bg-blue-900/80"></div>
            <div>
                <span class="text-[10px] text-blue-300/70 font-semibold uppercase tracking-wider block">SKS Terambil</span>
                <span class="text-xl font-bold text-emerald-400">{{ $krs?->total_sks_diambil ?? 0 }} <span class="text-xs text-blue-300">SKS</span></span>
            </div>
        </div>
    </div>

    <!-- Filter Bar: Semester & Program Studi / Jurusan -->
    <div class="bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-4 shadow-sm">
        <form method="GET" action="{{ route('siakad.krs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Filter Semester</label>
                <select name="semester_filter" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Semester</option>
                    @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" {{ request('semester_filter') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-blue-100 mb-1 font-mono">Filter Program Studi / Jurusan</label>
                <select name="prodi_filter" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#0b132b] border border-slate-200 dark:border-slate-700 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jurusan</option>
                    <option value="IF" {{ request('prodi_filter') == 'IF' ? 'selected' : '' }}>Teknik Informatika (S1)</option>
                    <option value="SI" {{ request('prodi_filter') == 'SI' ? 'selected' : '' }}>Sistem Informasi (S1)</option>
                    <option value="BD" {{ request('prodi_filter') == 'BD' ? 'selected' : '' }}>Bisnis Digital (S1)</option>
                    <option value="TK" {{ request('prodi_filter') == 'TK' ? 'selected' : '' }}>Teknik Komputer (D3)</option>
                </select>
            </div>
            <div class="flex items-end">
                <a href="{{ route('siakad.krs.index') }}" class="w-full py-2 px-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-xl text-center transition">
                    Reset Filter
                </a>
            </div>
        </form>
    </div>

    <!-- Financial Lock Warning if Unpaid -->
    @if(!$isCleared)
    <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-4 flex items-start gap-3 text-amber-200">
        <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/40 flex items-center justify-center shrink-0 font-mono font-bold text-xs">
            !
        </div>
        <div>
            <h3 class="font-bold text-xs text-amber-300">Pengisian KRS Terkunci (Syarat Keuangan Belum Terpenuhi)</h3>
            <p class="text-xs text-amber-200/90 mt-1 leading-relaxed">
                Akses KRS terkunci karena status pembayaran UKT semester {{ $activeYear?->nama_tahun }} belum diselesaikan. Pembayaran via Virtual Account Bank akan membuka kuncian secara otomatis.
            </p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Enrolled Courses in KRS (7 Cols) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                    <div>
                        <h2 class="font-bold text-xs uppercase tracking-wider text-slate-900 dark:text-white">Rencana Studi Terpilih</h2>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Kelas perkuliahan yang telah disetujui</span>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[10px] font-mono font-bold uppercase border border-blue-500/30 bg-blue-500/10 text-blue-600 dark:text-blue-300">
                        Status: {{ $krs?->status_krs ?? 'Draft' }}
                    </span>
                </div>

                @if($krs && $krs->details->count() > 0)
                <div class="overflow-x-auto rounded-xl border border-slate-200/80 dark:border-slate-700/60">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 uppercase text-[10px] font-semibold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="py-3 px-3.5">Kode &amp; Mata Kuliah</th>
                                <th class="py-3 px-3.5">Kelas</th>
                                <th class="py-3 px-3.5">SKS</th>
                                <th class="py-3 px-3.5">Jadwal &amp; Ruang</th>
                                <th class="py-3 px-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($krs->details as $detail)
                            @php
                                $kelas = $detail->kelasKuliah;
                                $mk = $kelas?->mataKuliah;
                                $jadwal = $kelas?->jadwalKuliahs->first();
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-blue-900/20 transition">
                                <td class="py-3 px-3.5 font-sans">
                                    <span class="font-mono text-[10px] font-bold text-blue-600 dark:text-blue-400 block">{{ $mk?->kode_mk }}</span>
                                    <span class="font-bold text-slate-900 dark:text-white text-xs">{{ $mk?->nama_mk }}</span>
                                </td>
                                <td class="py-3 px-3.5 font-bold text-slate-800 dark:text-slate-200">{{ $kelas?->nama_kelas }}</td>
                                <td class="py-3 px-3.5 font-semibold text-slate-700 dark:text-slate-300">{{ $mk?->sks_total }} SKS</td>
                                <td class="py-3 px-3.5 text-[11px] text-slate-600 dark:text-slate-400">
                                    {{ $jadwal ? $jadwal->hari . ', ' . substr($jadwal->jam_mulai, 0, 5) . '-' . substr($jadwal->jam_selesai, 0, 5) : '-' }}
                                    <span class="block text-slate-400 font-mono text-[10px]">{{ $jadwal?->ruangan?->nama_ruangan }}</span>
                                </td>
                                <td class="py-3 px-3.5 text-right">
                                    @if($krs->status_krs !== 'Disetujui')
                                    <form action="{{ route('siakad.krs.destroy', $detail->id) }}" method="POST" onsubmit="return confirm('Batalkan mata kuliah ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 dark:text-rose-400 hover:underline font-bold text-xs py-1 px-2.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 transition">
                                            Batal
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-emerald-600 dark:text-emerald-400 font-bold text-[10px] font-mono uppercase bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-10 text-slate-400 font-mono">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Belum ada mata kuliah yang dipilih</span>
                    <p class="text-[11px] text-slate-500 mt-0.5">Pilih kelas kuliah dari penawaran di samping.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Available Classes for Registration (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 p-5 shadow-sm">
                <div class="border-b border-slate-100 dark:border-slate-800 pb-3 mb-4 flex items-center justify-between font-mono">
                    <div>
                        <h2 class="font-bold text-xs uppercase tracking-wider text-slate-900 dark:text-white">Penawaran Kelas Kuliah</h2>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400">Pilih kelas semester aktif</span>
                    </div>
                    <span class="text-[10px] text-blue-600 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/60 px-2.5 py-0.5 rounded-full font-bold border border-blue-200 dark:border-blue-800">
                        {{ $availableClasses->count() }} Kelas
                    </span>
                </div>

                <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1 custom-scrollbar">
                    @forelse($availableClasses as $classItem)
                    @php
                        $isFull = $classItem->total_terisi >= $classItem->kuota_maksimal;
                        $jadwal = $classItem->jadwalKuliahs->first();
                    @endphp
                    <div class="p-4 rounded-xl border {{ $isFull ? 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30 opacity-60' : 'border-slate-200/80 dark:border-slate-700/60 bg-white dark:bg-[#0b132b] hover:border-blue-500' }} transition flex flex-col justify-between gap-3">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5 font-mono">
                                <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 rounded border border-blue-200 dark:border-blue-800">
                                    {{ $classItem->mataKuliah?->kode_mk }} &bull; {{ $classItem->nama_kelas }}
                                </span>
                                <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">
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
                                class="w-full py-2 px-3 bg-blue-600 hover:bg-blue-500 disabled:bg-slate-300 dark:disabled:bg-slate-800 disabled:text-slate-500 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                <span>Ambil Kelas Ini</span>
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-400 text-xs font-mono">
                        Tidak ada penawaran kelas perkuliahan yang sesuai dengan filter.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
