@extends('layouts.app')

@section('title', 'Portal Mandiri Mahasiswa & Siswa')

@section('content')
<div class="space-y-6">

    <!-- Header Banner Siswa (Calm & Professional) -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-brand-900 rounded-2xl p-6 sm:p-8 text-white shadow-soft relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-5 border border-slate-800">
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 text-brand-200 border border-white/10">
                    <svg class="w-4 h-4 inline-block text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg> Portal Mahasiswa &bull; ID: {{ $siswa->nisn ?? $siswa->nim ?? '-' }}
                </span>
                <span class="text-xs text-slate-300 font-medium">Status: {{ $siswa->status_kelulusan ?? 'Aktif' }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-white">Selamat Datang, {{ $siswa->nama ?? auth()->user()->name }}!</h1>
            <p class="text-slate-300 text-xs md:text-sm mt-1 max-w-xl leading-relaxed">
                Kelola rencana studi Smart KRS, presensi GPS, pengumpulan tugas LMS, dan billing pembayaran UKT Anda.
            </p>
        </div>
        @if($siswa)
            <div class="relative z-10 flex flex-wrap items-center gap-2">
                <a href="{{ route('siakad.krs.index') }}" class="bg-brand-600 hover:bg-brand-500 text-white font-semibold px-4 py-2.5 rounded-xl text-xs shadow-sm transition inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Smart KRS</span>
                </a>
                <a href="{{ route('siakad.ukt.index') }}" class="bg-white/10 hover:bg-white/15 text-white font-semibold px-4 py-2.5 rounded-xl text-xs backdrop-blur transition inline-flex items-center gap-1.5 border border-white/10">
                    <svg class="w-4 h-4 inline-block text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Billing UKT</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Quick Shortcuts Grid for Portal Features -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <a href="{{ route('siakad.presensi.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-soft hover:border-brand-300 hover:shadow-card transition flex flex-col items-center text-center group">
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-2 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-900 group-hover:text-brand-600 transition">Presensi GPS</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Check-in Perkuliahan</span>
        </a>

        <a href="{{ route('siakad.tugas.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-soft hover:border-brand-300 hover:shadow-card transition flex flex-col items-center text-center group">
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-2 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-900 group-hover:text-brand-600 transition">Tugas &amp; LMS</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Kumpulkan Tugas</span>
        </a>

        <a href="{{ route('siakad.ukt.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-soft hover:border-brand-300 hover:shadow-card transition flex flex-col items-center text-center group">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-600 transition">Pembayaran UKT</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Cetak Kwitansi H2H</span>
        </a>

        <a href="{{ route('siakad.krs.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-soft hover:border-brand-300 hover:shadow-card transition flex flex-col items-center text-center group">
            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-2 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-900 group-hover:text-brand-600 transition">Smart KRS</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Rencana Studi</span>
        </a>

        <a href="{{ route('siakad.biodata.edit') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-soft hover:border-brand-300 hover:shadow-card transition flex flex-col items-center text-center group">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-2 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-900 group-hover:text-amber-600 transition">Biodata &amp; PDP</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Data Diri &amp; Consent</span>
        </a>

        <a href="{{ route('siakad.analytics.performance') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-soft hover:border-brand-300 hover:shadow-card transition flex flex-col items-center text-center group">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-2 group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <span class="text-xs font-bold text-slate-900 group-hover:text-purple-600 transition">Grafik Performa</span>
            <span class="text-[10px] text-slate-400 mt-0.5">Tren IPK &amp; IPS</span>
        </a>
    </div>

    <!-- Quick Metrics Summary Cards (4 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Status SPP / UKT -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Status Keuangan UKT</span>
            @if($tunggakan['total_bulan'] === 0)
                <div class="text-2xl font-black text-emerald-600">LUNAS</div>
                <p class="text-[11px] text-emerald-600 font-medium mt-1">Tidak ada tunggakan berjalan</p>
            @else
                <div class="text-2xl font-black text-rose-600">Rp {{ number_format($tunggakan['total_rupiah'], 0, ',', '.') }}</div>
                <p class="text-[11px] text-rose-500 font-medium mt-1">{{ $tunggakan['total_bulan'] }} periode belum lunas</p>
            @endif
        </div>

        <!-- Presensi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Tingkat Kehadiran</span>
            <div class="text-2xl font-black text-slate-900">{{ $presensiSummary['persentase'] }}%</div>
            <p class="text-[11px] text-slate-500 mt-1 font-medium">{{ $presensiSummary['hadir'] }} Hadir &bull; {{ $presensiSummary['izin'] + $presensiSummary['sakit'] }} Izin/Sakit &bull; {{ $presensiSummary['alpa'] }} Alpa</p>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Jadwal Perkuliahan ({{ $hariIni }})</span>
            <div class="text-2xl font-black text-slate-900">{{ $jadwalSiswa->count() }} Kelas</div>
            <p class="text-[11px] text-slate-500 mt-1 font-medium">Sesi kuliah hari ini</p>
        </div>

        <!-- SKS Terambil -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Status Rencana Studi</span>
            <div class="text-2xl font-black text-brand-600">Disetujui</div>
            <p class="text-[11px] text-slate-500 mt-1 font-medium">Semester 2025/2026 Ganjil</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Jadwal Perkuliahan Hari Ini & Transkrip (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Jadwal Hari Ini -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <h2 class="font-bold text-slate-900 text-sm">Jadwal Perkuliahan Hari Ini ({{ $hariIni }})</h2>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($jadwalSiswa as $j)
                        <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/70 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-brand-50 border border-brand-200 text-brand-700 font-bold flex items-center justify-center text-xs flex-shrink-0">
                                    <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ $j->mapel->nama_mapel ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Dosen: {{ $j->guru->nama_guru ?? '-' }} &bull; Ruang: {{ $j->ruangan ?? 'Kelas' }}</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-mono font-bold text-brand-600 shadow-2xs">
                                {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs font-medium">
                            Tidak ada jadwal perkuliahan untuk hari {{ $hariIni }}.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Transkrip Nilai Terkini -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <h2 class="font-bold text-slate-900 text-sm">Transkrip &amp; Rekap Nilai</h2>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase font-bold text-slate-400">
                            <tr>
                                <th class="px-4 py-2.5">Mata Kuliah</th>
                                <th class="px-4 py-2.5 text-center">Tugas</th>
                                <th class="px-4 py-2.5 text-center">UTS</th>
                                <th class="px-4 py-2.5 text-center">UAS</th>
                                <th class="px-4 py-2.5 text-center">Akhir</th>
                                <th class="px-4 py-2.5 text-center">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($nilaiSiswa as $n)
                                <tr>
                                    <td class="px-4 py-3 font-bold text-slate-900">{{ $n->mapel->nama_mapel ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">{{ $n->nilai_tugas }}</td>
                                    <td class="px-4 py-3 text-center">{{ $n->nilai_uts }}</td>
                                    <td class="px-4 py-3 text-center">{{ $n->nilai_uas }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-900">{{ $n->nilai_akhir }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $n->status === 'Lulus' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                            {{ $n->predikat }} ({{ $n->status }})
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-slate-400">
                                        Belum ada data nilai yang diinput.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right: Status Keuangan & Kwitansi (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Status Billing Box -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <h2 class="font-bold text-slate-900 text-sm">Status Billing UKT</h2>
                    </div>
                    <a href="{{ route('siakad.ukt.index') }}" class="text-xs text-brand-600 font-bold hover:underline">Detail UKT &rarr;</a>
                </div>

                @if($tunggakan['total_bulan'] > 0)
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl space-y-2">
                        <div class="text-xs font-bold text-rose-900">Periode Belum Lunas:</div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($tunggakan['list_bulan'] as $bln)
                                <span class="px-2 py-0.5 bg-white border border-rose-300 text-rose-700 rounded text-[11px] font-bold">
                                    {{ $bln }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-center text-xs text-emerald-800 font-medium flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Pembayaran UKT Anda lunas hingga periode semester berjalan. Terima kasih!</span>
                    </div>
                @endif
            </div>

            <!-- Riwayat Kwitansi Pembayaran Terakhir -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        <h2 class="font-bold text-slate-900 text-sm">Riwayat Pembayaran UKT</h2>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($riwayatPembayaran as $p)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div>
                                <div class="font-bold text-slate-900">UKT {{ $p->bulan_dibayar }} {{ $p->tahun_dibayar }}</div>
                                <div class="text-[10px] text-slate-400">Dibayar pada {{ \Carbon\Carbon::parse($p->tgl_bayar)->format('d M Y') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-emerald-600 font-mono">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</div>
                                <a href="{{ route('web.pembayaran.cetak', $p->id) }}" target="_blank" class="text-[10px] text-brand-600 font-bold hover:underline">
                                    Unduh Kwitansi &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            Belum ada riwayat transaksi pembayaran UKT yang tercatat.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
