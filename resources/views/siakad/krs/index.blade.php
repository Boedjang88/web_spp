@extends('layouts.app')

@section('title', 'Smart KRS & Rencana Studi Online')

@section('content')
<div class="space-y-6">

    <!-- Top Header Banner -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-soft flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-3 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 text-[11px] font-bold rounded-full uppercase">
                    🚀 Next-Gen Smart KRS
                </span>
                <span class="text-xs text-slate-400 font-mono">Tahun Akademik: {{ $activeYear?->nama_tahun }} ({{ $activeYear?->semester }})</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900">Kartu Rencana Studi (KRS) Mahasiswa</h1>
            <p class="text-xs text-slate-500 mt-0.5">Sistem pendaftaran mata kuliah berkecepatan tinggi dengan proteksi kuota & bentrok jadwal.</p>
        </div>

        <!-- SKS Meter Indicator -->
        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 flex items-center gap-6">
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Batas SKS (IPS Lalu)</span>
                <span class="text-lg font-black text-slate-800">{{ $krs?->max_sks_diizinkan ?? 24 }} <span class="text-xs font-normal text-slate-400">SKS</span></span>
            </div>
            <div class="w-px h-8 bg-slate-200"></div>
            <div>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Total SKS Terambil</span>
                <span class="text-lg font-black text-indigo-600">{{ $krs?->total_sks_diambil ?? 0 }} <span class="text-xs font-normal text-slate-400">SKS</span></span>
            </div>
        </div>
    </div>

    <!-- Financial Lock Warning if Unpaid -->
    @if(!$isCleared)
    <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl flex-shrink-0">
            🔒
        </div>
        <div>
            <h3 class="font-bold text-sm text-rose-900">Pengisian KRS Terkunci (Syarat Keuangan Belum Terpenuhi)</h3>
            <p class="text-xs text-rose-700 mt-1 leading-relaxed">
                Anda belum dapat memilih kelas mata kuliah karena status pembayaran UKT/SPP semester {{ $activeYear?->nama_tahun }} belum lunas. Pembayaran melalui Virtual Account Bank H2H akan otomatis membuka akses KRS dalam tempo &lt; 1 detik.
            </p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Enrolled Courses in KRS (7 Cols) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div>
                        <h2 class="font-bold text-sm text-slate-900">📋 Rencana Studi Terpilih</h2>
                        <span class="text-[11px] text-slate-400">Daftar kelas yang sudah Anda amankan kursinya</span>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $krs?->status_krs === 'Disetujui' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        Status: {{ $krs?->status_krs ?? 'Draft' }}
                    </span>
                </div>

                @if($krs && $krs->details->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-slate-400 font-semibold uppercase text-[10px]">
                            <tr>
                                <th class="p-3">Kode & Mata Kuliah</th>
                                <th class="p-3">Kelas</th>
                                <th class="p-3">SKS</th>
                                <th class="p-3">Jadwal & Ruangan</th>
                                <th class="p-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($krs->details as $detail)
                            @php
                                $kelas = $detail->kelasKuliah;
                                $mk = $kelas?->mataKuliah;
                                $jadwal = $kelas?->jadwalKuliahs->first();
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-3">
                                    <span class="font-mono text-[10px] font-bold text-indigo-600 block">{{ $mk?->kode_mk }}</span>
                                    <span class="font-bold text-slate-900">{{ $mk?->nama_mk }}</span>
                                </td>
                                <td class="p-3 font-bold text-slate-700">{{ $kelas?->nama_kelas }}</td>
                                <td class="p-3 font-semibold text-slate-700">{{ $mk?->sks_total }}</td>
                                <td class="p-3 text-[11px] text-slate-500">
                                    {{ $jadwal ? $jadwal->hari . ', ' . substr($jadwal->jam_mulai, 0, 5) . '-' . substr($jadwal->jam_selesai, 0, 5) : '-' }}
                                    <span class="block text-slate-400 font-mono text-[10px]">{{ $jadwal?->ruangan?->nama_ruangan }}</span>
                                </td>
                                <td class="p-3 text-right">
                                    @if($krs->status_krs !== 'Disetujui')
                                    <form action="{{ route('siakad.krs.destroy', $detail->id) }}" method="POST" onsubmit="return confirm('Batalkan mata kuliah ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold text-xs p-1.5 rounded-lg hover:bg-rose-50 transition">
                                            Batal
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-slate-400 text-[10px]">Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-10 text-slate-400">
                    <span class="text-3xl block mb-2">📚</span>
                    <span class="text-xs font-semibold">Belum ada mata kuliah yang dipilih.</span>
                    <p class="text-[11px] mt-0.5">Pilih kelas kuliah yang tersedia pada panel sebelah kanan.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Available Classes for Registration (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-5">
                <div class="border-b border-slate-100 pb-3 mb-4">
                    <h2 class="font-bold text-sm text-slate-900">⚡ Pilihan Kelas Ditawarkan</h2>
                    <span class="text-[11px] text-slate-400">Klik "Pilih" untuk mendaftarkan kursi dengan sistem real-time lock</span>
                </div>

                <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1">
                    @forelse($availableClasses as $classItem)
                    @php
                        $isFull = $classItem->total_terisi >= $classItem->kuota_maksimal;
                        $jadwal = $classItem->jadwalKuliahs->first();
                    @endphp
                    <div class="p-4 rounded-2xl border {{ $isFull ? 'border-slate-200 bg-slate-50/50 opacity-60' : 'border-slate-200/80 bg-white hover:border-indigo-300' }} transition flex flex-col justify-between gap-3">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="font-mono text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">
                                    {{ $classItem->mataKuliah?->kode_mk }} &bull; Kelas {{ $classItem->nama_kelas }}
                                </span>
                                <span class="text-[11px] font-bold {{ $isFull ? 'text-rose-600' : 'text-emerald-700' }}">
                                    Kursi: {{ $classItem->total_terisi }}/{{ $classItem->kuota_maksimal }}
                                </span>
                            </div>
                            <h3 class="font-bold text-xs text-slate-900">{{ $classItem->mataKuliah?->nama_mk }}</h3>
                            <div class="text-[11px] text-slate-500 mt-1 flex flex-wrap gap-x-4 gap-y-1">
                                <span>⏱️ {{ $classItem->mataKuliah?->sks_total }} SKS</span>
                                <span>📍 {{ $jadwal ? $jadwal->hari . ' (' . substr($jadwal->jam_mulai, 0, 5) . '-' . substr($jadwal->jam_selesai, 0, 5) . ')' : 'Jadwal TBD' }}</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 truncate max-w-[180px]">
                                👨‍🏫 {{ $jadwal?->dosen?->nama_guru ?? 'Dosen Pengampu' }}
                            </span>
                            @if($isCleared && !$isFull)
                            <form action="{{ route('siakad.krs.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id_kelas_kuliah" value="{{ $classItem->id }}">
                                <input type="hidden" name="id_tahun_akademik" value="{{ $activeYear?->id }}">
                                <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                                    + Ambil Kelas
                                </button>
                            </form>
                            @else
                            <button disabled class="px-3 py-1.5 bg-slate-200 text-slate-400 rounded-xl text-xs font-semibold cursor-not-allowed">
                                {{ $isFull ? 'Penuh' : 'Terkunci' }}
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-slate-400 text-xs">
                        Tidak ada kelas yang dibuka pada semester ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
