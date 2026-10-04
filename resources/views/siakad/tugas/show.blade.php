@extends('layouts.app')

@section('title', 'Detail Tugas - ' . $assignment->judul)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Breadcrumb & Back button -->
    <div class="flex items-center gap-3">
        <a href="{{ route('siakad.tugas.index') }}" class="p-2.5 rounded-xl bg-white border border-slate-200/80 text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition shadow-soft">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-brand-600">
                {{ $assignment->kelasKuliah?->mataKuliah?->nama_mk ?? 'Mata Kuliah' }} &bull; {{ $assignment->kelasKuliah?->nama_kelas ?? 'Kelas' }}
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $assignment->judul }}</h1>
        </div>
    </div>

    <!-- Assignment Meta Box -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-soft text-slate-900 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <span class="text-xs text-slate-400 block font-medium">Dosen Pengampu</span>
                <span class="text-sm font-semibold text-slate-900">{{ $assignment->kelasKuliah?->dosen?->nama_dosen ?? 'Dosen Pengampu' }}</span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block font-medium">Tenggat Waktu</span>
                <span class="text-sm font-bold text-brand-600">
                    {{ $assignment->effective_deadline ? $assignment->effective_deadline->format('d M Y, H:i') : 'Tanpa Batas' }} WIB
                </span>
            </div>
            <div>
                <span class="text-xs text-slate-400 block font-medium">Bobot Penilaian</span>
                <span class="text-sm font-bold text-emerald-600">{{ $assignment->bobot_persen ?? 10 }}% ({{ $assignment->komponen_penilaian ?? 'TUGAS' }})</span>
            </div>
        </div>

        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Instruksi &amp; Deskripsi Tugas</h3>
            <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-xl border border-slate-200/80 font-medium">
                {{ $assignment->deskripsi ?? 'Silakan kumpulkan berkas laporan/solusi tugas sesuai petunjuk perkuliahan.' }}
            </div>
        </div>
    </div>

    <!-- Submission Section -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-soft text-slate-900 space-y-5">
        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            <span>Status &amp; Pengumpulkan Berkas</span>
        </h2>

        @if($submission)
            <!-- Submitted Information -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-600">Status Pengumpulan:</span>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                        Tugas Berhasil Terkumpul
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs pt-2 border-t border-slate-200/80">
                    <div>
                        <span class="text-slate-400 block">Waktu Submit:</span>
                        <span class="text-slate-800 font-mono font-semibold">{{ $submission->submitted_at ? $submission->submitted_at->format('d M Y, H:i:s') : '-' }} WIB</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Nama Berkas:</span>
                        <span class="text-slate-800 font-semibold">{{ $submission->original_filename }}</span>
                    </div>
                </div>

                @if($submission->hash_receipt)
                    <div class="pt-2 border-t border-slate-200/80">
                        <span class="text-[11px] text-slate-500 block mb-1 font-semibold">Bukti Penerimaan SHA-256 (Hash Receipt):</span>
                        <div class="p-2.5 rounded-lg bg-white border border-slate-200 font-mono text-[11px] text-brand-700 break-all select-all font-semibold">
                            {{ $submission->hash_receipt }}
                        </div>
                    </div>
                @endif

                @if(!is_null($submission->nilai))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 mt-3 space-y-1">
                        <div class="text-xs uppercase tracking-wider font-bold text-emerald-700">Hasil Penilaian Dosen</div>
                        <div class="text-2xl font-black text-slate-900">Nilai: {{ $submission->nilai }} / 100</div>
                        <p class="text-xs text-emerald-800 mt-1">Catatan: {{ $submission->catatan_dosen ?? $submission->feedback ?? 'Tugas dinilai dengan baik.' }}</p>
                    </div>
                @endif
            </div>
        @else
            <!-- Upload Form -->
            <form action="{{ route('siakad.tugas.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Unggah Berkas Solusi (PDF, DOCX, ZIP - Maks. 10MB)</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-slate-300 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100/80 hover:border-brand-500 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-2 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <p class="mb-1 text-xs text-slate-700"><span class="font-bold text-brand-600">Klik untuk memilih berkas</span> atau seret berkas ke sini</p>
                                <p class="text-[10px] text-slate-400">Security Gateway: Memverifikasi binary header &amp; anti-webshell</p>
                            </div>
                            <input type="file" name="file_tugas" required class="hidden" onchange="document.getElementById('fileChosen').innerText = this.files[0]?.name || ''">
                        </label>
                    </div>
                    <div id="fileChosen" class="text-xs text-brand-600 font-bold mt-2"></div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan / Komentar Tambahan (Opsional)</label>
                    <textarea name="catatan" rows="3" placeholder="Tuliskan catatan untuk dosen jika diperlukan..."
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-brand-500 transition font-medium"></textarea>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold shadow-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Kirim &amp; Kumpulkan Tugas</span>
                </button>
            </form>
        @endif
    </div>
</div>
@endsection
