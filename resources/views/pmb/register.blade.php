<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Mahasiswa Baru (PMB) - SIAKAD Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        navy: { 800: '#1c2541', 900: '#0b132b' }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b132b; color: #f8fafc; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-[#0b132b] min-h-screen py-10 px-4 antialiased text-slate-100 flex items-center justify-center">

    <div class="max-w-2xl w-full">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-br from-emerald-500 to-blue-600 text-white rounded-2xl text-sm font-bold mb-3 shadow-lg shadow-emerald-500/20">
                PMB
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white">PENDAFTARAN MAHASISWA BARU (PMB)</h1>
            <p class="text-xs text-blue-200/80 mt-1">Lengkapi Biodata Mandiri Anda untuk Memperoleh NIM &amp; Akses Portal Akademik</p>
        </div>

        <!-- Card Container -->
        <div class="bg-[#1c2541] rounded-2xl border border-blue-900/50 p-6 md:p-8 shadow-2xl backdrop-blur-xl relative overflow-hidden">

            @if($errors->any())
                <div class="mb-6 bg-rose-950/50 border border-rose-800 p-4 rounded-xl text-xs text-rose-200">
                    <strong class="block text-rose-300 font-bold mb-1">Mohon perbaiki kesalahan berikut:</strong>
                    <ul class="list-disc pl-4 space-y-1 font-mono">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('pmb.register.post') }}" class="space-y-5">
                @csrf

                <div class="border-b border-slate-700/60 pb-3 mb-4">
                    <h2 class="text-sm font-bold text-blue-400 uppercase tracking-wider font-mono">1. Akun Login &amp; Pilihan Program Studi</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Lengkap -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-blue-100 mb-1 font-mono">Nama Lengkap (Sesuai Ijazah)</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Muhammad Fauzan"
                            class="w-full px-4 py-2.5 bg-[#0b132b] border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                    </div>

                    <!-- Email Kampus/Personal -->
                    <div>
                        <label class="block text-xs font-semibold text-blue-100 mb-1 font-mono">Alamat Email Aktif</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="fauzan@student.ac.id"
                            class="w-full px-4 py-2.5 bg-[#0b132b] border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                    </div>

                    <!-- Program Studi -->
                    <div>
                        <label class="block text-xs font-semibold text-blue-100 mb-1 font-mono">Pilihan Program Studi (Jurusan)</label>
                        <select name="id_prodi" required
                            class="w-full px-4 py-2.5 bg-[#0b132b] border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500 transition">
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}" {{ old('id_prodi') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_prodi }} ({{ $p->jenjang }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-semibold text-blue-100 mb-1 font-mono">Kata Sandi (Password)</label>
                        <input type="password" name="password" required placeholder="Minimal 6 karakter"
                            class="w-full px-4 py-2.5 bg-[#0b132b] border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label class="block text-xs font-semibold text-blue-100 mb-1 font-mono">Konfirmasi Kata Sandi</label>
                        <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi"
                            class="w-full px-4 py-2.5 bg-[#0b132b] border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                    </div>
                </div>

                <div class="border-b border-slate-700/60 pb-3 pt-3 mb-4">
                    <h2 class="text-sm font-bold text-blue-400 uppercase tracking-wider font-mono">2. Biodata Diri &amp; Alamat Lengkap</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NIK -->
                    <div>
                        <label class="block text-xs font-semibold text-blue-100 mb-1 font-mono">Nomor Induk Kependudukan (NIK KTP/KK)</label>
                        <input type="text" name="nik" value="{{ old('nik') }}" placeholder="327501xxxxxxxxxx"
                            class="w-full px-4 py-2.5 bg-[#0b132b] border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                    </div>

                    <!-- No HP/WhatsApp -->
                    <div>
                        <label class="block text-xs font-semibold text-blue-100 mb-1 font-mono">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="no_telp" value="{{ old('no_telp') }}" required placeholder="081234567890"
                            class="w-full px-4 py-2.5 bg-[#0b132b] border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                    </div>

                    <!-- Asal Sekolah -->
                    <div>
                        <label class="block text-xs font-semibold text-blue-100 mb-1 font-mono">Asal Sekolah SMA/SMK/MA</label>
                        <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}" placeholder="SMA Negeri 1 Bandung"
                            class="w-full px-4 py-2.5 bg-[#0b132b] border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                    </div>

                    <!-- Nama Ibu Kandung -->
                    <div>
                        <label class="block text-xs font-semibold text-blue-100 mb-1 font-mono">Nama Ibu Kandung</label>
                        <input type="text" name="nama_ibu_kandung" value="{{ old('nama_ibu_kandung') }}" placeholder="Siti Rahmah"
                            class="w-full px-4 py-2.5 bg-[#0b132b] border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-blue-100 mb-1 font-mono">Alamat Tempat Tinggal Lengkap</label>
                        <textarea name="alamat" rows="2" required placeholder="Jl. Raya Dago No. 100, Kel. Coblong, Kota Bandung"
                            class="w-full px-4 py-2.5 bg-[#0b132b] border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">{{ old('alamat') }}</textarea>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Kirim Pendaftaran PMB &amp; Dapatkan NIM
                    </button>
                    <a href="{{ route('login') }}" class="py-3 px-4 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl text-center transition">
                        Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
