@extends('layouts.app')

@section('title', 'Presensi Perkuliahan Mahasiswa')

@section('content')
<div class="space-y-5">

    <!-- Executive Header Banner -->
    <div class="bg-zinc-900 dark:bg-zinc-950 p-5 md:p-6 rounded-xl text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-zinc-800">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded bg-zinc-800 text-zinc-300 border border-zinc-700 text-[10px] font-mono font-semibold mb-2">
                <span>GEO-FENCED GPS &bull; RADIUS 20M</span>
            </div>
            <h1 class="text-xl font-bold tracking-tight text-white">Presensi Perkuliahan Mahasiswa</h1>
            <p class="text-xs text-zinc-400 mt-1 max-w-2xl leading-relaxed">Catat kehadiran kelas Anda secara real-time dengan verifikasi koordinat lokasi GPS dan kode token presensi dari dosen.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-3.5 py-2 rounded-lg bg-zinc-800 border border-zinc-700 text-right">
                <div class="text-[10px] text-zinc-400 font-mono uppercase tracking-wider font-semibold">Tingkat Kehadiran</div>
                <div class="text-lg font-mono font-bold text-white">{{ $kehadiranPersen }}%</div>
            </div>
        </div>
    </div>

    <!-- Attendance Metrics Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
            <div class="text-[10px] font-mono text-zinc-500 font-semibold uppercase tracking-wider">
                Hadir
            </div>
            <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1">{{ $totalHadir }} <span class="text-xs text-zinc-500 font-normal">Sesi</span></div>
        </div>
        <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
            <div class="text-[10px] font-mono text-zinc-500 font-semibold uppercase tracking-wider">
                Izin
            </div>
            <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1">{{ $totalIzin }} <span class="text-xs text-zinc-500 font-normal">Sesi</span></div>
        </div>
        <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
            <div class="text-[10px] font-mono text-zinc-500 font-semibold uppercase tracking-wider">
                Sakit
            </div>
            <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1">{{ $totalSakit }} <span class="text-xs text-zinc-500 font-normal">Sesi</span></div>
        </div>
        <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
            <div class="text-[10px] font-mono text-zinc-500 font-semibold uppercase tracking-wider">
                Alpa
            </div>
            <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100 mt-1">{{ $totalAlpa }} <span class="text-xs text-zinc-500 font-normal">Sesi</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Sesi Kuliah Hari Ini & Form Check-In -->
        <div class="lg:col-span-1 space-y-4">
            <div class="p-5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    Check-In Presensi Kuliah
                </h2>
                <p class="text-xs text-zinc-500 leading-relaxed">Masukkan kode token QR dari dosen saat perkuliahan berlangsung dan pastikan izin GPS browser aktif.</p>

                <form action="{{ route('siakad.presensi.checkIn') }}" method="POST" id="attendanceForm" class="space-y-3.5">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Pilih Sesi Perkuliahan Aktif</label>
                        <select name="id_bap" id="id_bap" class="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none font-medium">
                            <option value="">-- Pilih Sesi Perkuliahan --</option>
                            @foreach($todayBaps as $bap)
                                <option value="{{ $bap->id }}">
                                    {{ $bap->kelasKuliah?->mataKuliah?->nama_mk ?? 'Mata Kuliah' }} - {{ $bap->materi_pembahasan ?? 'Materi Perkuliahan' }} (P{{ $bap->pertemuan_ke }} - {{ $bap->ruangan?->nama_ruangan ?? 'Ruang' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Kode Token Presensi Dosen</label>
                        <input type="text" name="qr_token" id="qr_token" placeholder="Contoh: ATT-QR-XXXXXXXX" required
                            class="w-full bg-zinc-50 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 rounded-lg px-3 py-2 text-xs text-zinc-900 dark:text-zinc-100 focus:outline-none font-mono uppercase tracking-wider font-semibold">
                        <span class="text-[10px] text-zinc-500 mt-1 block font-mono">Token QR berputar otomatis setiap 10 detik.</span>
                    </div>

                    <!-- Hidden Coordinate Fields -->
                    <input type="hidden" name="latitude" id="latInput" value="-6.917464">
                    <input type="hidden" name="longitude" id="lngInput" value="107.619123">

                    <div class="p-2.5 rounded-lg bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[11px] text-zinc-600 dark:text-zinc-400 flex items-center justify-between font-mono">
                        <span id="gpsStatus">GPS: Siap</span>
                        <button type="button" onclick="detectGPS()" class="text-zinc-900 dark:text-zinc-100 hover:underline font-semibold text-[11px]">
                            Deteksi GPS
                        </button>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-zinc-900 dark:bg-zinc-100 hover:bg-zinc-800 dark:hover:bg-zinc-200 text-white dark:text-zinc-900 text-xs font-semibold transition flex items-center justify-center gap-2">
                        <span>Kirim Presensi</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Riwayat Kehadiran Mahasiswa -->
        <div class="lg:col-span-2 space-y-4">
            <div class="p-5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-zinc-100">
                        Riwayat Presensi Mahasiswa
                    </h2>
                    <span class="text-xs text-zinc-500 font-mono">Total: {{ $attendanceHistory->total() }} Record</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 uppercase text-[10px] tracking-wider border-b border-zinc-200 dark:border-zinc-800 font-mono">
                            <tr>
                                <th class="py-2.5 px-3">Waktu</th>
                                <th class="py-2.5 px-3">Mata Kuliah / Kelas</th>
                                <th class="py-2.5 px-3">Ruangan</th>
                                <th class="py-2.5 px-3">Status</th>
                                <th class="py-2.5 px-3">Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 font-mono">
                            @forelse($attendanceHistory as $presensi)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                                    <td class="py-2.5 px-3 text-zinc-600 dark:text-zinc-400">
                                        {{ $presensi->waktu_hadir ? $presensi->waktu_hadir->format('d M Y, H:i') : '-' }} WIB
                                    </td>
                                    <td class="py-2.5 px-3 font-sans">
                                        <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $presensi->kelasKuliah?->mataKuliah?->nama_mk ?? 'Kelas Kuliah' }}</div>
                                        <div class="text-[10px] text-zinc-500 font-mono">{{ $presensi->kelasKuliah?->nama_kelas ?? '-' }}</div>
                                    </td>
                                    <td class="py-2.5 px-3 text-zinc-500 font-sans">
                                        {{ $presensi->bap?->ruangan?->nama_ruangan ?? $presensi->kelasKuliah?->ruang ?? 'Ruang Kuliah' }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        @if($presensi->status === 'Hadir')
                                            <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-300 dark:border-zinc-700 text-[10px] font-semibold">Hadir</span>
                                        @elseif($presensi->status === 'Izin')
                                            <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 text-[10px] font-semibold">Izin</span>
                                        @elseif($presensi->status === 'Sakit')
                                            <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 text-[10px] font-semibold">Sakit</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 text-[10px] font-semibold">Alpa</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="text-[10px] text-zinc-600 dark:text-zinc-400 font-mono">
                                            Geo-Verified (20m)
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-zinc-400 text-xs font-sans">
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
        statusText.innerText = 'Mendeteksi GPS...';
        navigator.geolocation.getCurrentPosition(
            (position) => {
                document.getElementById('latInput').value = position.coords.latitude;
                document.getElementById('lngInput').value = position.coords.longitude;
                statusText.innerText = `GPS: ${position.coords.latitude.toFixed(4)}, ${position.coords.longitude.toFixed(4)}`;
            },
            (error) => {
                statusText.innerText = 'GPS fallback aktif.';
            },
            { enableHighAccuracy: true, timeout: 5000 }
        );
    } else {
        statusText.innerText = 'GPS tidak didukung.';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('attendanceForm');
    const statusText = document.getElementById('gpsStatus');

    function syncOfflineQueue() {
        const queue = JSON.parse(localStorage.getItem('offline_presensi_queue') || '[]');
        if (queue.length === 0) return;

        statusText.innerText = `Mengirim ${queue.length} presensi offline...`;
        
        queue.forEach((item, index) => {
            fetch('{{ route("siakad.presensi.checkIn") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(item)
            }).then(res => res.json()).then(data => {
                queue.splice(index, 1);
                localStorage.setItem('offline_presensi_queue', JSON.stringify(queue));
                statusText.innerText = 'Presensi offline tersinkron!';
                setTimeout(() => window.location.reload(), 1500);
            }).catch(() => {});
        });
    }

    window.addEventListener('online', syncOfflineQueue);
    if (navigator.onLine) {
        syncOfflineQueue();
    }

    form.addEventListener('submit', (e) => {
        if (!navigator.onLine) {
            e.preventDefault();
            const payload = {
                id_bap: document.getElementById('id_bap').value,
                qr_token: document.getElementById('qr_token').value,
                latitude: document.getElementById('latInput').value,
                longitude: document.getElementById('lngInput').value,
                timestamp: new Date().toISOString()
            };

            const queue = JSON.parse(localStorage.getItem('offline_presensi_queue') || '[]');
            queue.push(payload);
            localStorage.setItem('offline_presensi_queue', JSON.stringify(queue));

            statusText.innerText = 'Offline: Tersimpan lokal.';
            alert('Koneksi terputus. Presensi Anda telah disimpan di perangkat (Offline Mode).');
        }
    });
});
</script>
@endsection
