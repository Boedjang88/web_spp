@extends('layouts.app')

@section('title', 'Detail Siswa - ' . $siswa->nama)

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('web.siswa.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Data Siswa</a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-slate-900">{{ $siswa->nama }}</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">{{ $siswa->nama }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('web.pembayaran.create', ['id_siswa' => $siswa->id]) }}" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5">
                <span>➕</span> Catat Pembayaran
            </a>
            <a href="{{ route('web.siswa.suratTagihan', $siswa->id) }}" target="_blank" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5">
                <span>📄</span> Surat Tagihan (PDF/Print)
            </a>
            <a href="{{ route('web.siswa.edit', $siswa->id) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                Edit Data
            </a>
        </div>
    </div>

    <!-- Student Info & Tagihan Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Info Card (1 Col) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3">Profil Biodata Siswa</h2>
            
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-slate-400 block text-[11px]">NISN</span>
                    <span class="font-mono font-bold text-slate-800 text-sm">{{ $siswa->nisn }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">NIS</span>
                    <span class="font-medium text-slate-800">{{ $siswa->nis }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Kelas & Jurusan</span>
                    <span class="font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">{{ $siswa->kelas?->nama_kelas }} ({{ $siswa->kelas?->kompetensi_keahlian }})</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Tarif SPP Terikat</span>
                    <span class="font-semibold text-slate-800">Rp {{ number_format($siswa->spp?->nominal ?? 0, 0, ',', '.') }} / bulan (Tahun {{ $siswa->spp?->tahun }})</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">No. Telepon / WhatsApp</span>
                    <span class="font-medium text-slate-800">{{ $siswa->no_telp }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Alamat</span>
                    <p class="text-slate-700 leading-relaxed">{{ $siswa->alamat }}</p>
                </div>
            </div>
        </div>

        <!-- Tagihan & Riwayat Card (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Status Tunggakan Banner -->
            @if($tunggakan['total_bulan'] > 0)
                <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-rose-700">Terdapat Tunggakan SPP</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-200 text-rose-800">
                            {{ $tunggakan['total_bulan'] }} Bulan Belum Lunas
                        </span>
                    </div>
                    <div class="text-2xl font-black text-rose-900">
                        Rp {{ number_format($tunggakan['total_rupiah'], 0, ',', '.') }}
                    </div>
                    <div class="mt-3 pt-3 border-t border-rose-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-[11px] font-semibold text-rose-700 block mb-1">Rincian Bulan Belum Dibayar:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($tunggakan['list_bulan'] as $bulanNunggak)
                                    <span class="bg-white border border-rose-300 text-rose-800 text-[11px] font-medium px-2 py-0.5 rounded-md">
                                        {{ $bulanNunggak }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        @php
                            $phoneClean = preg_replace('/[^0-9]/', '', $siswa->no_telp);
                            if (str_starts_with($phoneClean, '0')) { $phoneClean = '62' . substr($phoneClean, 1); }
                            $waMsg = "Halo Bpk/Ibu wali murid dari *{$siswa->nama}* (NISN: {$siswa->nisn}),\n\n"
                                   . "Kami menginformasikan tagihan SPP ananda sebesar *Rp " . number_format($tunggakan['total_rupiah'], 0, ',', '.') . "* untuk periode bulan: *" . implode(', ', $tunggakan['list_bulan']) . "*.\n"
                                   . "Mohon untuk segera melakukan pelunasan melalui loket sekolah atau mengecek tagihan di website.\n\n"
                                   . "Terima kasih.\n_- Tata Usaha SMK Web SPP-_";
                        @endphp
                        <a href="https://wa.me/{{ $phoneClean }}?text={{ urlencode($waMsg) }}" target="_blank"
                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm inline-flex items-center gap-1.5 flex-shrink-0">
                            <span>📱</span> Kirim Tagihan WA
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Status Pembayaran</span>
                        <div class="text-xl font-bold text-emerald-900 mt-1">LUNAS SAMPAI BULAN INI ✅</div>
                        <p class="text-xs text-emerald-700 mt-0.5">Tidak ada tunggakan SPP yang jatuh tempo untuk siswa ini.</p>
                    </div>
                </div>
            @endif

            <!-- Riwayat Pembayaran Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-900 text-sm mb-4">Riwayat Pembayaran Siswa</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider">
                                <th class="pb-3 font-semibold">Periode SPP</th>
                                <th class="pb-3 font-semibold">Tgl Bayar</th>
                                <th class="pb-3 font-semibold">Petugas</th>
                                <th class="pb-3 font-semibold">Nominal</th>
                                <th class="pb-3 font-semibold text-right">Kwitansi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($siswa->pembayarans as $bayar)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3 font-bold text-slate-900">{{ $bayar->bulan_dibayar }} {{ $bayar->tahun_dibayar }}</td>
                                    <td class="py-3 text-slate-600">{{ $bayar->tgl_bayar }}</td>
                                    <td class="py-3 text-slate-600">{{ $bayar->petugas?->name ?? 'Petugas' }}</td>
                                    <td class="py-3 font-semibold text-emerald-600">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('web.pembayaran.cetak', $bayar->id) }}" target="_blank" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-medium inline-flex items-center gap-1">
                                            🖨️ Cetak
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">Belum ada riwayat pembayaran untuk siswa ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
