@extends('layouts.app')

@section('title', 'Persetujuan KRS Mahasiswa (Dosen PA)')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-blue-900 via-slate-900 to-indigo-950 p-6 rounded-2xl border border-blue-900/50 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2 mb-1.5 font-mono">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/20 text-blue-200 border border-blue-400/30">DOSEN PEMBIMBING AKADEMIK (PA)</span>
                <span class="text-xs text-blue-200/80">T.A. {{ $activeTa?->nama_tahun }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Persetujuan KRS Mahasiswa Bimbingan</h1>
            <p class="text-xs text-blue-200/80 mt-1">Verifikasi dan berikan persetujuan rencana studi (KRS) mahasiswa bimbingan akademik Anda.</p>
        </div>
    </div>

    <!-- KRS List Table -->
    <div class="bg-white dark:bg-[#1c2541] rounded-2xl border border-slate-200/80 dark:border-blue-900/50 shadow-sm p-6 space-y-4">
        <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase font-mono tracking-wider">Daftar Pengajuan KRS Mahasiswa</h2>

        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold uppercase text-[10px] border-b border-slate-200 dark:border-slate-700">
                        <th class="py-3.5 px-4 w-12 text-center border-r border-slate-200 dark:border-slate-700">No</th>
                        <th class="py-3.5 px-4 border-r border-slate-200 dark:border-slate-700">NIM / Nama Mahasiswa</th>
                        <th class="py-3.5 px-4 border-r border-slate-200 dark:border-slate-700">Kelas / Prodi</th>
                        <th class="py-3.5 px-4 border-r border-slate-200 dark:border-slate-700">Mata Kuliah Terpilih</th>
                        <th class="py-3.5 px-4 border-r border-slate-200 dark:border-slate-700 text-center">Status KRS</th>
                        <th class="py-3.5 px-4 text-center">Aksi Verifikasi Dosen PA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($krsList as $idx => $krsItem)
                        <tr class="hover:bg-slate-50 dark:hover:bg-blue-900/20 transition">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-mono border-r border-slate-100 dark:border-slate-800">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-4 border-r border-slate-100 dark:border-slate-800">
                                <span class="font-mono font-bold text-blue-600 dark:text-blue-400 block text-xs">{{ $krsItem->siswa?->nim ?? $krsItem->siswa?->nisn }}</span>
                                <span class="font-bold text-slate-900 dark:text-white text-xs">{{ $krsItem->siswa?->nama }}</span>
                            </td>
                            <td class="py-3.5 px-4 border-r border-slate-100 dark:border-slate-800">
                                <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $krsItem->siswa?->kelas?->nama_kelas }}</span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ $krsItem->siswa?->kelas?->kompetensi_keahlian }}</span>
                            </td>
                            <td class="py-3.5 px-4 border-r border-slate-100 dark:border-slate-800">
                                <div class="space-y-1">
                                    @foreach($krsItem->details as $d)
                                        <span class="inline-block px-2 py-0.5 bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 rounded text-[11px] font-mono text-blue-700 dark:text-blue-300">
                                            {{ $d->kelasKuliah?->mataKuliah?->nama_mk }} ({{ $d->kelasKuliah?->mataKuliah?->sks_total }} SKS)
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center border-r border-slate-100 dark:border-slate-800">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono uppercase {{ $krsItem->status_krs === 'Disetujui' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 border border-emerald-200 dark:border-emerald-800' : ($krsItem->status_krs === 'Ditolak' ? 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 border border-rose-200 dark:border-rose-800' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 border border-amber-200 dark:border-amber-800') }}">
                                    {{ $krsItem->status_krs }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($krsItem->status_krs !== 'Disetujui')
                                    <form action="{{ route('siakad.dosen.krs.approve', $krsItem->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition shadow-xs">
                                            Setujui KRS
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-emerald-600 font-bold font-mono">&check; Disetujui</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 font-mono">Belum ada pengajuan KRS dari mahasiswa bimbingan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $krsList->links() }}
        </div>
    </div>

</div>
@endsection
