<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal - SIAKAD Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        zinc: {
                            950: '#09090b',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #09090b; color: #fafafa; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-zinc-950 min-h-screen flex items-center justify-center p-4 antialiased">

    <div class="max-w-md w-full">
        <!-- Logo & Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-zinc-900 border border-zinc-800 text-white rounded-xl text-xs font-mono font-bold mb-3">
                SIAKAD
            </div>
            <h1 class="text-xl font-bold tracking-tight text-white">SIAKAD ENTERPRISE</h1>
            <p class="text-xs text-zinc-400 mt-1 font-mono">Universitas &bull; Portal Akademik &amp; Keuangan</p>
        </div>

        <!-- Login Card -->
        <div class="bg-zinc-900 rounded-xl border border-zinc-800 p-6 md:p-8">

            @if(session('error'))
                <div class="mb-4 bg-zinc-950 border border-zinc-800 p-3 rounded-lg text-xs text-zinc-300 font-mono">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-zinc-950 border border-zinc-800 p-3 rounded-lg text-xs text-zinc-300 font-mono">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-zinc-300 mb-1 font-mono">Alamat Email / NPM / NIDN</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@siakad.ac.id') }}" required autofocus
                        class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-700 rounded-lg text-xs text-white focus:outline-none focus:border-zinc-500 font-mono transition"
                        placeholder="nama@siakad.ac.id">
                    @error('email')
                        <p class="text-xs text-zinc-400 mt-1 font-mono">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-zinc-300 mb-1 font-mono">Kata Sandi</label>
                    <input type="password" id="password" name="password" value="password123" required
                        class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-700 rounded-lg text-xs text-white focus:outline-none focus:border-zinc-500 font-mono transition"
                        placeholder="••••••••">
                    @error('password')
                        <p class="text-xs text-zinc-400 mt-1 font-mono">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Public Check -->
                <div class="flex items-center justify-between text-xs pt-1 font-mono">
                    <label class="flex items-center text-zinc-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-zinc-700 bg-zinc-950 text-zinc-100 focus:ring-0 mr-2">
                        Ingat Saya
                    </label>
                    <a href="{{ route('cek.index') }}" class="text-zinc-300 hover:underline">Cek Mandiri &rarr;</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-2.5 px-4 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 text-xs font-semibold font-mono uppercase tracking-wider rounded-lg transition">
                    Masuk Portal SIAKAD
                </button>
            </form>

            <!-- Quick Account Selector Demo -->
            <div class="mt-6 pt-5 border-t border-zinc-800">
                <span class="text-[10px] uppercase tracking-wider text-zinc-500 font-mono font-semibold block mb-2">Akun Uji Coba:</span>
                <div class="grid grid-cols-3 gap-2 text-[10px] font-mono">
                    <button type="button" onclick="fillLogin('admin@siakad.ac.id')" class="p-2 rounded bg-zinc-950 hover:bg-zinc-800 border border-zinc-800 text-zinc-300 transition text-center">
                        <strong class="block text-white">Superadmin</strong>
                    </button>
                    <button type="button" onclick="fillLogin('dosen@siakad.ac.id')" class="p-2 rounded bg-zinc-950 hover:bg-zinc-800 border border-zinc-800 text-zinc-300 transition text-center">
                        <strong class="block text-white">Dosen</strong>
                    </button>
                    <button type="button" onclick="fillLogin('mahasiswa@siakad.ac.id')" class="p-2 rounded bg-zinc-950 hover:bg-zinc-800 border border-zinc-800 text-zinc-300 transition text-center">
                        <strong class="block text-white">Mahasiswa</strong>
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center text-[10px] text-zinc-500 font-mono mt-4">
            Universitas SIAKAD Enterprise &bull; System v2.6
        </div>
    </div>

    <script>
        function fillLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password123';
        }
    </script>
</body>
</html>
