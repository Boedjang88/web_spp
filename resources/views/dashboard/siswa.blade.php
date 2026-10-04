@extends('layouts.app')

@section('title', 'Portal Siswa')

@section('content')
<div class="space-y-6">

    <!-- Header Banner Siswa (Calm & Soothing) -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-blue-950 rounded-2xl p-6 md:p-8 text-white shadow-soft relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-5 border border-slate-800">
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/10 text-blue-200 border border-white/10">
                    🎓 Portal Mandiri Siswa &bull; NISN: {{ $siswa->nisn ?? '-' }}
                </span>
                <span class="text-xs text-slate-300">Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-white">Hai, {{ $siswa->nama ?? auth()->user()->name }}!</h1>
            <p class="text-slate-300 text-xs md:text-sm mt-1 max-w-xl leading-relaxed">
                Pantau jadwal belajar, transkrip nilai, kehadiran, dan tagihan SPP mandiri Anda.
            </p>
        </div>
        @if($siswa)
            <div class="relative z-10 flex flex-wrap items-center gap-2">
                <a href="{{ route('web.nilai.rapor', $siswa->id) }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2.5 rounded-xl text-xs shadow-sm transition inline-flex items-center gap-1.5">
                    <span>📄</span> Cetak E-Rapor
                </a>
                <a href="{{ route('web.siswa.kartuUjian', $siswa->id) }}" target="_blank" class="bg-white/10 hover:bg-white/15 text-white font-semibold px-4 py-2.5 rounded-xl text-xs backdrop-blur transition inline-flex items-center gap-1.5 border border-white/10">
                    <span>🪪</span> Kartu Ujian
                </a>
            </div>
        @endif
    </div>

    <!-- Quick Metrics Summary Cards (4 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Status SPP -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Status Keuangan SPP</span>
            @if($tunggakan['total_bulan'] === 0)
                <div class="text-2xl font-bold text-emerald-600">LUNAS</div>
                <p class="text-[11px] text-emerald-600 mt-1">Tidak ada tunggakan berjalan</p>
            @else
                <div class="text-2xl font-bold text-rose-600">Rp {{ number_format($tunggakan['total_rupiah'], 0, ',', '.') }}</div>
                <p class="text-[11px] text-rose-500 mt-1">{{ $tunggakan['total_bulan'] }} bulan belum dibayar</p>
            @endif
        </div>

        <!-- Presensi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Tingkat Kehadiran</span>
            <div class="text-2xl font-bold text-slate-900">{{ $presensiSummary['persentase'] }}%</div>
            <p class="text-[11px] text-slate-500 mt-1">{{ $presensiSummary['hadir'] }} Hadir &bull; {{ $presensiSummary['izin'] + $presensiSummary['sakit'] }} Izin/Sakit &bull; {{ $presensiSummary['alpa'] }} Alpa</p>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Jadwal Belajar ({{ $hariIni }})</span>
            <div class="text-2xl font-bold text-slate-900">{{ $jadwalSiswa->count() }} Mapel</div>
            <p class="text-[11px] text-slate-500 mt-1">Sesi belajar hari ini</p>
        </div>

        <!-- Tarif SPP -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-soft">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">Tarif SPP Bulanan</span>
            <div class="text-2xl font-bold text-slate-900">Rp {{ number_format($siswa->spp->nominal ?? 0, 0, ',', '.') }}</div>
            <p class="text-[11px] text-slate-500 mt-1">Tahun Ajaran {{ $siswa->spp->tahun ?? '-' }}</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Jadwal Pelajaran Hari Ini & Transkrip Nilai (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Jadwal Hari Ini -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="font-bold text-slate-900 text-sm">🗓️ Jadwal Pelajaran Hari Ini ({{ $hariIni }})</h2>
                        <p class="text-[11px] text-slate-400">Jadwal mata pelajaran kelas Anda hari ini.</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($jadwalSiswa as $j)
                        <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/60 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                    📚
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-900 text-xs sm:text-sm">{{ $j->mapel->nama_mapel ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">Guru: {{ $j->guru->nama_guru ?? '-' }} &bull; Ruang: {{ $j->ruangan ?? 'Kelas' }}</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-mono font-semibold text-indigo-700 shadow-2xs">
                                {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            Tidak ada jadwal pelajaran untuk hari {{ $hariIni }}.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Transkrip Nilai Terkini -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="font-bold text-slate-900 text-sm">📝 Transkrip &amp; Rekap Nilai</h2>
                        <p class="text-[11px] text-slate-400">Hasil evaluasi belajar dan capaian KKM.</p>
                    </div>
                    @if($siswa)
                        <a href="{{ route('web.nilai.rapor', $siswa->id) }}" target="_blank" class="text-xs text-indigo-600 font-semibold hover:underline">Lihat Rapor Lengkap &rarr;</a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-100 text-[10px] uppercase font-semibold text-slate-400">
                            <tr>
                                <th class="px-4 py-2.5">Mata Pelajaran</th>
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
                                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $n->mapel->nama_mapel ?? '-' }}</td>
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
                                        Belum ada data nilai yang diinput oleh dewan guru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right: SPP & Kwitansi History (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Tunggakan Box -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="font-bold text-slate-900 text-sm">💳 Status Tagihan SPP</h2>
                    @if($siswa)
                        <a href="{{ route('web.siswa.suratTagihan', $siswa->id) }}" target="_blank" class="text-xs text-indigo-600 font-semibold hover:underline">Surat Tagihan &rarr;</a>
                    @endif
                </div>

                @if($tunggakan['total_bulan'] > 0)
                    <div class="p-4 bg-rose-50/70 border border-rose-200/80 rounded-xl space-y-2">
                        <div class="text-xs font-semibold text-rose-800">Daftar Bulan Belum Dibayar:</div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($tunggakan['list_bulan'] as $bln)
                                <span class="px-2 py-0.5 bg-white border border-rose-300 text-rose-700 rounded text-[11px] font-medium">
                                    {{ $bln }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="p-4 bg-emerald-50/70 border border-emerald-200/80 rounded-xl text-center text-xs text-emerald-800 font-medium">
                        🎉 Pembayaran SPP Anda lunas hingga periode berjalan. Terima kasih!
                    </div>
                @endif
            </div>

            <!-- Riwayat Kwitansi Pembayaran Terakhir -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h2 class="font-bold text-slate-900 text-sm">🧾 Riwayat Pembayaran SPP</h2>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($riwayatPembayaran as $p)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div>
                                <div class="font-semibold text-slate-900">SPP {{ $p->bulan_dibayar }} {{ $p->tahun_dibayar }}</div>
                                <div class="text-[10px] text-slate-400">Dibayar pada {{ \Carbon\Carbon::parse($p->tgl_bayar)->format('d M Y') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-emerald-700">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</div>
                                <a href="{{ route('web.pembayaran.cetak', $p->id) }}" target="_blank" class="text-[10px] text-indigo-600 font-semibold hover:underline">
                                    Unduh Kwitansi &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            Belum ada riwayat pembayaran yang tercatat.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
