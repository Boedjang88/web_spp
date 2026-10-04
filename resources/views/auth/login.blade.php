<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Web SPP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full">
        <!-- Logo & Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 text-white rounded-2xl shadow-lg shadow-blue-500/20 text-2xl mb-3">
                💳
            </div>
            <h1 class="text-2xl font-bold text-slate-900">Web SPP Sekolah</h1>
            <p class="text-sm text-slate-500 mt-1">Masuk untuk mengelola dan mencatat transaksi SPP</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">

            @if(session('error'))
                <div class="mb-5 bg-rose-50 border-l-4 border-rose-500 p-3.5 rounded text-xs text-rose-700 font-medium">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 bg-emerald-50 border-l-4 border-emerald-500 p-3.5 rounded text-xs text-emerald-700 font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@sekolah.id') }}" required autofocus
                        class="w-full px-3.5 py-2.5 bg-slate-50 border @error('email') border-rose-400 @else border-slate-200 @enderror rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"
                        placeholder="contoh: admin@sekolah.id">
                    @error('email')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                    <input type="password" id="password" name="password" value="password123" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border @error('password') border-rose-400 @else border-slate-200 @enderror rounded-lg text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"
                        placeholder="••••••••">
                    @error('password')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mr-2">
                        Ingat Saya
                    </label>
                    <a href="{{ route('cek.index') }}" class="text-blue-600 hover:underline font-medium">Cek Tagihan Publik</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow transition duration-150">
                    Masuk ke Sistem
                </button>
            </form>

            <!-- Quick Account Selector Demo -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider text-center mb-2">Akun Default Demo</p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button type="button" onclick="document.getElementById('email').value='admin@sekolah.id';document.getElementById('password').value='password123';"
                        class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition">
                        <span class="font-bold text-slate-800 block">Admin</span>
                        <span class="text-[10px] text-slate-500 block">admin@sekolah.id</span>
                    </button>
                    <button type="button" onclick="document.getElementById('email').value='petugas@sekolah.id';document.getElementById('password').value='password123';"
                        class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-left transition">
                        <span class="font-bold text-slate-800 block">Petugas</span>
                        <span class="text-[10px] text-slate-500 block">petugas@sekolah.id</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
