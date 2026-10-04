<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuesioner Kepuasan Pengguna Lulusan (Employer Feedback)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 sm:p-8 flex items-center justify-center">

    <div class="max-w-2xl w-full bg-slate-800 border border-slate-700 rounded-3xl p-6 sm:p-10 shadow-2xl space-y-6">
        
        <!-- Header -->
        <div class="border-b border-slate-700 pb-4">
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-500/20 text-blue-300 border border-blue-500/30">
                    Tracer Study &amp; Alumni CRM
                </span>
            </div>
            <h1 class="text-lg font-bold text-white">Evaluasi Kinerja Pengguna Lulusan (Alumni)</h1>
            <p class="text-xs text-slate-400 mt-1">
                Alumni yang Dinilai: <strong class="text-white">{{ $feedback->tracerStudy?->mahasiswa?->nama }}</strong>
            </p>
        </div>

        @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-semibold">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('employer.feedback.update', $feedback->access_token) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Nama Penilai / Atasan Langsung</label>
                    <input type="text" name="nama_penilai_atasan" value="{{ old('nama_penilai_atasan', $feedback->nama_penilai_atasan) }}" required
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-white focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Jabatan Penilai</label>
                    <input type="text" name="jabatan_penilai" value="{{ old('jabatan_penilai', $feedback->jabatan_penilai) }}" required
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-white focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Nama Instansi / Perusahaan</label>
                    <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $feedback->nama_perusahaan) }}" required
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-white focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Email Perusahaan</label>
                    <input type="email" name="email_perusahaan" value="{{ old('email_perusahaan', $feedback->email_perusahaan) }}" required
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-white focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <!-- Rubric Ratings (1-5) -->
            <div class="border-t border-slate-700 pt-4 space-y-3">
                <h3 class="font-bold text-slate-200 text-xs uppercase tracking-wider">Skala Penilaian Kualitas Alumni (1: Sangat Kurang s.d. 5: Sangat Baik)</h3>
                
                @php
                    $metrics = [
                        'skor_integritas_etika' => '1. Integritas, Etika Kerja, & Moralitas',
                        'skor_keahlian_bidang' => '2. Keahlian & Keterampilan Bidang Ilmu',
                        'skor_bahasa_asing' => '3. Kemampuan Berbahasa Asing / Internasional',
                        'skor_penggunaan_ti' => '4. Penggunaan Teknologi Informasi & Software',
                        'skor_komunikasi' => '5. Kemampuan Komunikasi & Presentasi',
                        'skor_kerjasama_tim' => '6. Kerjasama Tim & Kepemimpinan',
                        'skor_pengembangan_diri' => '7. Kemandirian & Pengembangan Diri',
                    ];
                @endphp

                @foreach($metrics as $field => $label)
                <div class="flex items-center justify-between p-2.5 bg-slate-900/60 rounded-xl border border-slate-700">
                    <span class="text-slate-300 text-xs">{{ $label }}</span>
                    <select name="{{ $field }}" class="bg-slate-800 border border-slate-600 rounded-lg px-2.5 py-1 text-xs text-white focus:outline-none">
                        @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ old($field, $feedback->$field) == $i ? 'selected' : '' }}>Skor {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                @endforeach
            </div>

            <div>
                <label class="block text-slate-300 font-semibold mb-1">Saran & Masukan Pengembangan Kurikulum</label>
                <textarea name="saran_kurikulum" rows="3" placeholder="Tuliskan saran untuk peningkatan kompetensi alumni..."
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-white focus:outline-none focus:border-blue-500">{{ old('saran_kurikulum', $feedback->saran_kurikulum) }}</textarea>
            </div>

            <div class="pt-3 text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-6 py-2.5 rounded-xl text-xs shadow-lg transition">
                    Simpan Evaluasi Stakeholder &rarr;
                </button>
            </div>
        </form>

    </div>

</body>
</html>
