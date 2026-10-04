@extends('layouts.app')

@section('title', 'Presensi Perkuliahan Mahasiswa')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 rounded-2xl border border-indigo-900/40 text-white shadow-sm">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Geo-Fenced &amp; Dynamic QR Attendance</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Presensi Perkuliahan</h1>
            <p class="text-sm text-slate-400 mt-0.5">Catat kehadiran kuliah Anda dengan verifikasi lokasi GPS (radius 20 meter) dan kode token dosen.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700/60 text-right">
                <div class="text-[11px] text-slate-400 uppercase tracking-wider font-semibold">Tingkat Kehadiran</div>
                <div class="text-xl font-bold {{ $kehadiranPersen >= 75 ? 'text-emerald-400' : 'text-rose-400' }}">{{ $kehadiranPersen }}%</div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Attendance Metrics Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 text-slate-200">
            <div class="text-xs text-emerald-400 font-semibold uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Hadir</span>
            </div>
            <div class="text-2xl font-bold mt-2">{{ $totalHadir }} <span class="text-xs text-slate-500 font-normal">Sesi</span></div>
        </div>
        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 text-slate-200">
            <div class="text-xs text-blue-400 font-semibold uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Izin</span>
            </div>
            <div class="text-2xl font-bold mt-2">{{ $totalIzin }} <span class="text-xs text-slate-500 font-normal">Sesi</span></div>
        </div>
        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 text-slate-200">
            <div class="text-xs text-amber-400 font-semibold uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Sakit</span>
            </div>
            <div class="text-2xl font-bold mt-2">{{ $totalSakit }} <span class="text-xs text-slate-500 font-normal">Sesi</span></div>
        </div>
        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 text-slate-200">
            <div class="text-xs text-rose-400 font-semibold uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span>Alpa</span>
            </div>
            <div class="text-2xl font-bold mt-2">{{ $totalAlpa }} <span class="text-xs text-slate-500 font-normal">Sesi</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Sesi Kuliah Hari Ini & Form Check-In -->
        <div class="lg:col-span-1 space-y-6">
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm text-slate-200">
                <h2 class="text-base font-bold text-white flex items-center gap-2 mb-3">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Scan / Input Presensi</span>
                </h2>
                <p class="text-xs text-slate-400 mb-4">Masukkan token QR dari dosen saat perkuliahan berlangsung dan pastikan izin GPS aktif.</p>

                <form action="{{ route('siakad.presensi.checkIn') }}" method="POST" id="attendanceForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Pilih Sesi Kuliah Aktif</label>
                        <select name="id_bap" id="id_bap" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 transition">
                            <option value="">-- Pilih Sesi Perkuliahan --</option>
                            @foreach($todayBaps as $bap)
                                <option value="{{ $bap->id }}">
                                    {{ $bap->kelasKuliah?->mataKuliah?->nama_mk ?? 'Mata Kuliah' }} - {{ $bap->materi_pembahasan ?? 'Materi Perkuliahan' }} (P{{ $bap->pertemuan_ke }} - {{ $bap->ruangan?->nama_ruangan ?? 'Ruang' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Kode Token Presensi Dosen</label>
                        <input type="text" name="qr_token" id="qr_token" placeholder="Contoh: ATT-QR-XXXXXXXX" required
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 transition font-mono uppercase tracking-wider">
                        <span class="text-[10px] text-slate-500 mt-1 block">Token berputar otomatis setiap 10 detik.</span>
                    </div>

                    <!-- Hidden Coordinate Fields -->
                    <input type="hidden" name="latitude" id="latInput" value="-6.917464">
                    <input type="hidden" name="longitude" id="lngInput" value="107.619123">

                    <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                        <span id="gpsStatus">Lokasi GPS: Menggunakan titik koordinat ruangan</span>
                        <button type="button" onclick="detectGPS()" class="text-indigo-400 hover:text-indigo-300 font-semibold underline text-[11px]">
                            Deteksi GPS
                        </button>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Kirim Presensi Sekarang</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Riwayat Kehadiran Mahasiswa -->
        <div class="lg:col-span-2 space-y-4">
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm text-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Riwayat Presensi Mahasiswa</span>
                    </h2>
                    <span class="text-xs text-slate-500">Total: {{ $attendanceHistory->total() }} Catatan</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-950/70 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="py-3 px-3">Waktu Presensi</th>
                                <th class="py-3 px-3">Mata Kuliah / Kelas</th>
                                <th class="py-3 px-3">Ruangan</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3">Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @forelse($attendanceHistory as $presensi)
                                <tr class="hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-3 font-mono text-slate-300">
                                        {{ $presensi->waktu_hadir ? $presensi->waktu_hadir->format('d M Y, H:i') : '-' }} WIB
                                    </td>
                                    <td class="py-3 px-3 font-medium text-white">
                                        {{ $presensi->kelasKuliah?->mataKuliah?->nama_mk ?? 'Kelas Kuliah' }}
                                        <div class="text-[10px] text-slate-400">{{ $presensi->kelasKuliah?->nama_kelas ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3 text-slate-400">
                                        {{ $presensi->bap?->ruangan?->nama_ruangan ?? $presensi->kelasKuliah?->ruang ?? 'Ruang Kuliah' }}
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($presensi->status === 'Hadir')
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-semibold">Hadir</span>
                                        @elseif($presensi->status === 'Izin')
                                            <span class="px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] font-semibold">Izin</span>
                                        @elseif($presensi->status === 'Sakit')
                                            <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-semibold">Sakit</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-[10px] font-semibold">Alpa</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400 font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            <span>Geo-Verified</span>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-500 text-xs">
                                        Belum ada data presensi yang tercatat pada semester ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $attendanceHistory->links() }}
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function detectGPS() {
    const statusText = document.getElementById('gpsStatus');
    if (navigator.geolocation) {
        statusText.innerText = 'Mendeteksi koordinat GPS...';
        navigator.geolocation.getCurrentPosition(
            (position) => {
                document.getElementById('latInput').value = position.coords.latitude;
                document.getElementById('lngInput').value = position.coords.longitude;
                statusText.innerText = `GPS Terkunci: ${position.coords.latitude.toFixed(6)}, ${position.coords.longitude.toFixed(6)}`;
            },
            (error) => {
                statusText.innerText = 'GPS browser dinonaktifkan, menggunakan fallback koordinat kampus.';
            },
            { enableHighAccuracy: true, timeout: 5000 }
        );
    } else {
        statusText.innerText = 'Geolocation tidak didukung browser.';
    }
}
</script>
@endsection
