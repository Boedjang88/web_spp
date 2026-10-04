@extends('layouts.app')

@section('title', 'Presensi Kehadiran Siswa')

@section('content')
<div class="space-y-5">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800">SIAKAD</span>
                <span class="text-xs text-slate-400">Kehadiran Harian</span>
            </div>
            <h1 class="text-xl font-black text-slate-900 mt-1">Presensi Kehadiran Siswa</h1>
            <p class="text-xs text-slate-500 mt-0.5">Catat kehadiran harian peserta didik per kelas secara cepat (Hadir, Izin, Sakit, Alpa)</p>
        </div>
    </div>

    <!-- Class & Date Selector Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <form method="GET" action="{{ route('web.presensi.index') }}" class="flex flex-col sm:flex-row items-end gap-3 text-xs">
            <div class="w-full sm:w-60">
                <label class="block font-semibold text-slate-700 mb-1">Pilih Kelas</label>
                <select name="id_kelas" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-48">
                <label class="block font-semibold text-slate-700 mb-1">Tanggal Presensi</label>
                <input type="date" name="tanggal" value="{{ $selectedTanggal }}" required
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
            </div>

            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md shadow-blue-600/20 transition">
                Buka Lembar Presensi
            </button>
        </form>
    </div>

    @if($selectedKelasId)
        <!-- Attendance Sheet Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <span class="font-bold text-slate-900 text-sm">Lembar Kehadiran Siswa ({{ $siswas->count() }} Siswa)</span>
                    <p class="text-slate-500 text-xs mt-0.5">Tanggal: <strong class="font-mono text-slate-700">{{ \Carbon\Carbon::parse($selectedTanggal)->translatedFormat('l, d F Y') }}</strong></p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="setSemuaStatus('Hadir')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold transition">
                        ✓ Set Semua Hadir
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('web.presensi.batch') }}">
                @csrf
                <input type="hidden" name="id_kelas" value="{{ $selectedKelasId }}">
                <input type="hidden" name="tanggal" value="{{ $selectedTanggal }}">

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/50 text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4 font-semibold">No</th>
                                <th class="py-3 px-4 font-semibold">NISN / NIS</th>
                                <th class="py-3 px-4 font-semibold">Nama Siswa</th>
                                <th class="py-3 px-4 font-semibold text-center">Status Kehadiran</th>
                                <th class="py-3 px-4 font-semibold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($siswas as $idx => $s)
                                @php
                                    $existingPresensi = $s->presensis->first();
                                    $currentStatus = $existingPresensi ? $existingPresensi->status : 'Hadir';
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3 px-4 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-3 px-4 font-mono font-semibold text-slate-600">{{ $s->nisn }}</td>
                                    <td class="py-3 px-4 font-bold text-slate-900">{{ $s->nama }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <input type="hidden" name="presensi[{{ $idx }}][id_siswa]" value="{{ $s->id }}">
                                        <div class="inline-flex items-center gap-3">
                                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                                <input type="radio" name="presensi[{{ $idx }}][status]" value="Hadir" {{ $currentStatus == 'Hadir' ? 'checked' : '' }} class="status-radio-Hadir text-emerald-600 focus:ring-emerald-500">
                                                <span class="font-bold text-emerald-700">Hadir</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                                <input type="radio" name="presensi[{{ $idx }}][status]" value="Izin" {{ $currentStatus == 'Izin' ? 'checked' : '' }} class="status-radio-Izin text-blue-600 focus:ring-blue-500">
                                                <span class="font-bold text-blue-700">Izin</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                                <input type="radio" name="presensi[{{ $idx }}][status]" value="Sakit" {{ $currentStatus == 'Sakit' ? 'checked' : '' }} class="status-radio-Sakit text-amber-600 focus:ring-amber-500">
                                                <span class="font-bold text-amber-700">Sakit</span>
                                            </label>
                                            <label class="inline-flex items-center gap-1 cursor-pointer">
                                                <input type="radio" name="presensi[{{ $idx }}][status]" value="Alpa" {{ $currentStatus == 'Alpa' ? 'checked' : '' }} class="status-radio-Alpa text-rose-600 focus:ring-rose-500">
                                                <span class="font-bold text-rose-700">Alpa</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <input type="text" name="presensi[{{ $idx }}][keterangan]" value="{{ $existingPresensi?->keterangan }}" placeholder="Catatan opsional..."
                                            class="w-full px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-slate-400">Belum ada siswa di kelas yang dipilih.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($siswas->count() > 0)
                    <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition">
                            💾 Simpan Presensi Kelas
                        </button>
                    </div>
                @endif
            </form>
        </div>
    @else
        <div class="p-12 text-center text-slate-400 bg-white rounded-2xl border border-dashed border-slate-200">
            <div class="text-4xl mb-2">📋</div>
            <p class="text-sm font-semibold text-slate-600">Silakan pilih kelas dan tanggal di atas</p>
            <p class="text-xs text-slate-400 mt-1">Daftar siswa akan ditampilkan untuk pengisian presensi harian.</p>
        </div>
    @endif

</div>

@push('scripts')
<script>
    function setSemuaStatus(status) {
        const radios = document.querySelectorAll('.status-radio-' + status);
        radios.forEach(radio => radio.checked = true);
    }
</script>
@endpush
@endsection
