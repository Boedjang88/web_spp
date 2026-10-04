@extends('layouts.app')

@section('title', 'Smart KRS & Rencana Studi Online')

@section('content')
<div class="space-y-5">

    <!-- Top Header Banner -->
    <div class="bg-zinc-900 dark:bg-zinc-950 p-5 rounded-xl border border-zinc-800 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-1.5 font-mono">
                <span class="px-2 py-0.5 bg-zinc-800 text-zinc-300 border border-zinc-700 text-[10px] font-semibold rounded uppercase tracking-wider">
                    SMART KRS ENGINE &bull; LOCK VERIFIED
                </span>
                <span class="text-xs text-zinc-400">T.A. {{ $activeYear?->nama_tahun }} ({{ $activeYear?->semester }})</span>
            </div>
            <h1 class="text-lg font-bold text-white tracking-tight">Kartu Rencana Studi (KRS) Mahasiswa</h1>
            <p class="text-xs text-zinc-400 mt-0.5">Pengisian KRS berkecepatan tinggi dengan proteksi kuota &amp; bentrok jadwal otomatis.</p>
        </div>

        <!-- SKS Meter Indicator -->
        <div class="w-full md:w-auto bg-zinc-950 border border-zinc-800 rounded-lg p-3.5 flex items-center justify-around sm:justify-start gap-5 font-mono">
            <div>
                <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider block">Batas SKS</span>
                <span class="text-base font-bold text-zinc-200">{{ $krs?->max_sks_diizinkan ?? 24 }} <span class="text-xs text-zinc-500">SKS</span></span>
            </div>
            <div class="w-px h-7 bg-zinc-800"></div>
            <div>
                <span class="text-[10px] text-zinc-500 font-semibold uppercase tracking-wider block">SKS Terambil</span>
                <span class="text-base font-bold text-zinc-100">{{ $krs?->total_sks_diambil ?? 0 }} <span class="text-xs text-zinc-500">SKS</span></span>
            </div>
        </div>
    </div>

    <!-- Financial Lock Warning if Unpaid -->
    @if(!$isCleared)
    <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-zinc-800 text-zinc-300 border border-zinc-700 flex items-center justify-center flex-shrink-0 font-mono font-bold text-xs">
            !
        </div>
        <div>
            <h3 class="font-bold text-xs text-zinc-100">Pengisian KRS Terkunci (Syarat Keuangan Belum Terpenuhi)</h3>
            <p class="text-xs text-zinc-400 mt-1 leading-relaxed">
                Akses KRS terkunci karena status pembayaran UKT semester {{ $activeYear?->nama_tahun }} belum diselesaikan. Pembayaran via Virtual Account akan membuka kuncian secara otomatis.
            </p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Left Column: Enrolled Courses in KRS (7 Cols) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-5">
                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3 mb-4">
                    <div>
                        <h2 class="font-bold text-xs uppercase tracking-wider text-zinc-900 dark:text-zinc-100">Rencana Studi Terpilih</h2>
                        <span class="text-[11px] text-zinc-500">Kelas perkuliahan yang telah diambil</span>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-semibold uppercase border border-zinc-300 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
                        Status: {{ $krs?->status_krs ?? 'Draft' }}
                    </span>
                </div>

                @if($krs && $krs->details->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs font-mono">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 uppercase text-[10px] border-b border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <th class="p-2.5">Kode &amp; Mata Kuliah</th>
                                <th class="p-2.5">Kelas</th>
                                <th class="p-2.5">SKS</th>
                                <th class="p-2.5">Jadwal &amp; Ruangan</th>
                                <th class="p-2.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($krs->details as $detail)
                            @php
                                $kelas = $detail->kelasKuliah;
                                $mk = $kelas?->mataKuliah;
                                $jadwal = $kelas?->jadwalKuliahs->first();
                            @endphp
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                                <td class="p-2.5 font-sans">
                                    <span class="font-mono text-[10px] font-bold text-zinc-900 dark:text-zinc-100 block">{{ $mk?->kode_mk }}</span>
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100 text-xs">{{ $mk?->nama_mk }}</span>
                                </td>
                                <td class="p-2.5 font-bold text-zinc-800 dark:text-zinc-200">{{ $kelas?->nama_kelas }}</td>
                                <td class="p-2.5 text-zinc-700 dark:text-zinc-300">{{ $mk?->sks_total }} SKS</td>
                                <td class="p-2.5 text-[11px] text-zinc-500 font-sans">
                                    {{ $jadwal ? $jadwal->hari . ', ' . substr($jadwal->jam_mulai, 0, 5) . '-' . substr($jadwal->jam_selesai, 0, 5) : '-' }}
                                    <span class="block text-zinc-400 font-mono text-[10px]">{{ $jadwal?->ruangan?->nama_ruangan }}</span>
                                </td>
                                <td class="p-2.5 text-right font-sans">
                                    @if($krs->status_krs !== 'Disetujui')
                                    <form action="{{ route('siakad.krs.destroy', $detail->id) }}" method="POST" onsubmit="return confirm('Batalkan mata kuliah ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-zinc-900 dark:text-zinc-100 hover:underline font-semibold text-xs py-0.5 px-2 rounded bg-zinc-100 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 transition">
                                            Batal
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-zinc-500 text-[10px] font-mono">Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-10 text-zinc-500 font-mono">
                    <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 block">Belum ada mata kuliah yang dipilih</span>
                    <p class="text-[11px] text-zinc-500 mt-0.5">Pilih kelas kuliah dari panel penawaran di samping.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Available Classes for Registration (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-5">
                <div class="border-b border-zinc-100 dark:border-zinc-800 pb-3 mb-4 flex items-center justify-between font-mono">
                    <div>
                        <h2 class="font-bold text-xs uppercase tracking-wider text-zinc-900 dark:text-zinc-100">Penawaran Kelas</h2>
                        <span class="text-[11px] text-zinc-500">Pilih kelas kuliah semester ini</span>
                    </div>
                    <span class="text-[10px] text-zinc-500 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded border border-zinc-300 dark:border-zinc-700">
                        {{ $availableClasses->count() }} Kelas
                    </span>
                </div>

                <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1 custom-scrollbar">
                    @forelse($availableClasses as $classItem)
                    @php
                        $isFull = $classItem->total_terisi >= $classItem->kuota_maksimal;
                        $jadwal = $classItem->jadwalKuliahs->first();
                    @endphp
                    <div class="p-3.5 rounded-lg border {{ $isFull ? 'border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/30 opacity-60' : 'border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-400 dark:hover:border-zinc-600' }} transition flex flex-col justify-between gap-2.5">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1 font-mono">
                                <span class="text-[10px] font-bold text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded border border-zinc-300 dark:border-zinc-700">
                                    {{ $classItem->mataKuliah?->kode_mk }} &bull; {{ $classItem->nama_kelas }}
                                </span>
                                <span class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300">
                                    {{ $classItem->total_terisi }}/{{ $classItem->kuota_maksimal }} Kuota
                                </span>
                            </div>
                            <h3 class="font-bold text-xs text-zinc-900 dark:text-zinc-100 mt-1">{{ $classItem->mataKuliah?->nama_mk }}</h3>
                            <div class="text-[11px] text-zinc-500 mt-1 flex flex-wrap gap-x-3 gap-y-1 font-mono">
                                <span>{{ $classItem->mataKuliah?->sks_total }} SKS</span>
                                <span>&bull;</span>
                                <span>{{ $jadwal ? $jadwal->hari . ' (' . substr($jadwal->jam_mulai, 0, 5) . '-' . substr($jadwal->jam_selesai, 0, 5) . ')' : 'Jadwal TBD' }}</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                            <span class="text-[10px] text-zinc-500 font-mono truncate max-w-[170px]">
                                {{ $jadwal?->dosen?->nama_guru ?? 'Dosen Pengampu' }}
                            </span>
                            @if($isCleared && !$isFull)
                            <form action="{{ route('siakad.krs.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id_kelas_kuliah" value="{{ $classItem->id }}">
                                <input type="hidden" name="id_tahun_akademik" value="{{ $activeYear?->id }}">
                                <button type="submit" class="px-3 py-1 bg-zinc-900 dark:bg-zinc-100 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 rounded text-xs font-semibold font-mono transition">
                                    + Ambil Kelas
                                </button>
                            </form>
                            @else
                            <button disabled class="px-3 py-1 bg-zinc-200 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-600 rounded text-xs font-semibold font-mono cursor-not-allowed">
                                {{ $isFull ? 'Penuh' : 'Terkunci' }}
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-zinc-500 text-xs font-mono">
                        Tidak ada kelas yang dibuka pada semester ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
