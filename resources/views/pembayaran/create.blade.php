@extends('layouts.app')

@section('title', 'Entri Pembayaran SPP')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Entri Pembayaran SPP</h1>
            <p class="text-xs text-slate-500">Catat penerimaan pembayaran SPP bulanan atau sekaligus multi-bulan</p>
        </div>
        <a href="{{ route('web.pembayaran.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('web.pembayaran.store') }}" class="space-y-5" id="pembayaranForm">
            @csrf

            <!-- Pilih Siswa -->
            <div>
                <label for="id_siswa" class="block text-xs font-semibold text-slate-700 mb-1">Pilih Siswa</label>
                <select id="id_siswa" name="id_siswa" required onchange="onSiswaChanged(this)"
                    class="w-full px-3.5 py-2.5 text-xs border @error('id_siswa') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">-- Cari dan Pilih Siswa --</option>
                    @foreach($siswas as $s)
                        <option value="{{ $s->id }}" 
                            data-nominal="{{ $s->spp?->nominal ?? 0 }}"
                            data-tahun="{{ $s->spp?->tahun ?? date('Y') }}"
                            data-kelas="{{ $s->kelas?->nama_kelas ?? '-' }}"
                            {{ (old('id_siswa', $selectedSiswa?->id) == $s->id) ? 'selected' : '' }}>
                            {{ $s->nama }} (NISN: {{ $s->nisn }}) - Kelas {{ $s->kelas?->nama_kelas }} - SPP Rp {{ number_format($s->spp?->nominal ?? 0, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                @error('id_siswa')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tanggal Bayar -->
                <div>
                    <label for="tgl_bayar" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Transaksi</label>
                    <input type="date" id="tgl_bayar" name="tgl_bayar" value="{{ old('tgl_bayar', date('Y-m-d')) }}" required
                        class="w-full px-3.5 py-2 text-xs border @error('tgl_bayar') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('tgl_bayar')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun Dibayar -->
                <div>
                    <label for="tahun_dibayar" class="block text-xs font-semibold text-slate-700 mb-1">Tahun Anggaran SPP</label>
                    <input type="number" id="tahun_dibayar" name="tahun_dibayar" value="{{ old('tahun_dibayar', date('Y')) }}" required
                        class="w-full px-3.5 py-2 text-xs border @error('tahun_dibayar') border-rose-400 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('tahun_dibayar')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Pilih Bulan (Multi-Select Checkboxes) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-semibold text-slate-700">Pilih Bulan Pembayaran (Bisa Bayar Sekaligus Banyak Bulan)</label>
                    <div class="space-x-2">
                        <button type="button" onclick="toggleAllMonths(true)" class="text-[11px] text-blue-600 hover:underline font-semibold">Pilih Semua</button>
                        <span class="text-slate-300">|</span>
                        <button type="button" onclick="toggleAllMonths(false)" class="text-[11px] text-slate-500 hover:underline font-semibold">Kosongkan</button>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    @foreach($daftarBulan as $bln)
                        <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-slate-200 hover:border-blue-400 cursor-pointer text-xs select-none transition">
                            <input type="checkbox" name="bulan_dibayar[]" value="{{ $bln }}" onchange="recalculateTotal()"
                                class="month-checkbox rounded text-blue-600 focus:ring-blue-500"
                                {{ (is_array(old('bulan_dibayar')) && in_array($bln, old('bulan_dibayar'))) || old('bulan_dibayar') == $bln || (empty(old('bulan_dibayar')) && $bln == 'Juli') ? 'checked' : '' }}>
                            <span class="font-medium text-slate-800">{{ $bln }}</span>
                        </label>
                    @endforeach
                </div>
                @error('bulan_dibayar')
                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kalkulasi Estimasi Pembayaran Live Card -->
            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl flex items-center justify-between">
                <div>
                    <span class="text-[11px] text-blue-700 font-semibold uppercase tracking-wider block">Total Estimasi Pembayaran:</span>
                    <div id="displayTotal" class="text-xl font-black text-blue-900 mt-0.5">Rp 0</div>
                    <span id="displayCount" class="text-[11px] text-blue-600 font-medium">1 bulan dipilih</span>
                </div>
                <div class="text-right">
                    <span class="text-slate-400 block text-[10px]">Petugas:</span>
                    <span class="font-bold text-slate-800 text-xs">{{ auth()->user()->name ?? 'Petugas' }}</span>
                </div>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <a href="{{ route('web.pembayaran.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 transition flex items-center gap-1.5">
                    <span>💳</span> Proses & Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentNominal = 0;

    function onSiswaChanged(selectElem) {
        const selected = selectElem.options[selectElem.selectedIndex];
        if (selected && selected.value) {
            currentNominal = parseInt(selected.getAttribute('data-nominal') || 0);
            const tahun = selected.getAttribute('data-tahun');
            if (tahun) {
                document.getElementById('tahun_dibayar').value = tahun;
            }
        } else {
            currentNominal = 0;
        }
        recalculateTotal();
    }

    function toggleAllMonths(check) {
        document.querySelectorAll('.month-checkbox').forEach(cb => cb.checked = check);
        recalculateTotal();
    }

    function recalculateTotal() {
        const checkedCount = document.querySelectorAll('.month-checkbox:checked').length;
        const totalRupiah = checkedCount * currentNominal;

        document.getElementById('displayTotal').innerText = 'Rp ' + totalRupiah.toLocaleString('id-ID');
        document.getElementById('displayCount').innerText = checkedCount + ' bulan dipilih (@ Rp ' + currentNominal.toLocaleString('id-ID') + '/bln)';
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        const siswaSelect = document.getElementById('id_siswa');
        if (siswaSelect) onSiswaChanged(siswaSelect);
    });
</script>
@endpush
@endsection
