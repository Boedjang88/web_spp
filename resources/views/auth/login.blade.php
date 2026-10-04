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

        @keyframes toastSlideIn {
            from { transform: translateY(-12px) scale(0.96); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }
        @keyframes toastFadeOut {
            from { transform: translateY(0) scale(1); opacity: 1; }
            to { transform: translateY(-12px) scale(0.96); opacity: 0; }
        }
        @keyframes modalEnter {
            from { transform: scale(0.94) translateY(10px); opacity: 0; }
            to { transform: scale(1) translateY(0); opacity: 1; }
        }
        @keyframes modalLeave {
            from { transform: scale(1) translateY(0); opacity: 1; }
            to { transform: scale(0.94) translateY(10px); opacity: 0; }
        }
        @keyframes modalBackdropEnter {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes modalBackdropLeave {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        @keyframes cardFadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-toast-in { animation: toastSlideIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-toast-out { animation: toastFadeOut 0.15s ease-in forwards; }
        .animate-modal-enter { animation: modalEnter 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-modal-leave { animation: modalLeave 0.15s ease-in forwards; }
        .animate-modal-backdrop { animation: modalBackdropEnter 0.2s ease-out forwards; }
        .animate-backdrop-leave { animation: modalBackdropLeave 0.15s ease-in forwards; }
        .animate-card-in { animation: cardFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* Global Fluid Micro-Interactions */
        a, button, input, select, textarea, [role="button"] {
            transition: color 0.15s ease, background-color 0.15s ease, border-color 0.15s ease, opacity 0.15s ease, box-shadow 0.15s ease, transform 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        }

        button:active, a.btn:active, [role="button"]:active {
            transform: scale(0.97);
        }
    </style>
</head>
<body class="bg-zinc-950 min-h-screen flex items-center justify-center p-4 antialiased">

    <div class="max-w-md w-full animate-card-in">
        <!-- Logo & Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-zinc-900 border border-zinc-800 text-white rounded-xl text-xs font-mono font-bold mb-3 shadow-sm">
                SIAKAD
            </div>
            <h1 class="text-xl font-bold tracking-tight text-white">SIAKAD ENTERPRISE</h1>
            <p class="text-xs text-zinc-400 mt-1 font-mono">Universitas &bull; Portal Akademik &amp; Keuangan</p>
        </div>

        <!-- Login Card -->
        <div class="bg-zinc-900 rounded-xl border border-zinc-800 p-6 md:p-8 shadow-2xl">

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
                        class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-700 rounded-lg text-xs text-white focus:outline-none focus:border-zinc-500 font-mono transition duration-150"
                        placeholder="nama@siakad.ac.id">
                    @error('email')
                        <p class="text-xs text-zinc-400 mt-1 font-mono">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-zinc-300 mb-1 font-mono">Kata Sandi</label>
                    <input type="password" id="password" name="password" value="password123" required
                        class="w-full px-3.5 py-2.5 bg-zinc-950 border border-zinc-700 rounded-lg text-xs text-white focus:outline-none focus:border-zinc-500 font-mono transition duration-150"
                        placeholder="••••••••">
                    @error('password')
                        <p class="text-xs text-zinc-400 mt-1 font-mono">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Public Check -->
                <div class="flex items-center justify-between text-xs pt-1 font-mono">
                    <label class="flex items-center text-zinc-400 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded border-zinc-700 bg-zinc-950 text-zinc-100 focus:ring-0 mr-2">
                        Ingat Saya
                    </label>
                    <a href="{{ route('cek.index') }}" class="text-zinc-300 hover:underline transition">Cek Mandiri &rarr;</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-2.5 px-4 bg-zinc-100 hover:bg-zinc-200 active:scale-[0.98] text-zinc-900 text-xs font-semibold font-mono uppercase tracking-wider rounded-lg transition-all duration-150">
                    Masuk Portal SIAKAD
                </button>
            </form>

            <!-- Quick Account Selector Demo -->
            <div class="mt-6 pt-5 border-t border-zinc-800">
                <span class="text-[10px] uppercase tracking-wider text-zinc-500 font-mono font-semibold block mb-2">Akun Uji Coba:</span>
                <div class="grid grid-cols-3 gap-2 text-[10px] font-mono">
                    <button type="button" onclick="fillLogin('admin@siakad.ac.id')" class="p-2 rounded bg-zinc-950 hover:bg-zinc-800 active:scale-[0.97] border border-zinc-800 text-zinc-300 transition-all duration-150 text-center">
                        <strong class="block text-white">Superadmin</strong>
                    </button>
                    <button type="button" onclick="fillLogin('dosen@siakad.ac.id')" class="p-2 rounded bg-zinc-950 hover:bg-zinc-800 active:scale-[0.97] border border-zinc-800 text-zinc-300 transition-all duration-150 text-center">
                        <strong class="block text-white">Dosen</strong>
                    </button>
                    <button type="button" onclick="fillLogin('mahasiswa@siakad.ac.id')" class="p-2 rounded bg-zinc-950 hover:bg-zinc-800 active:scale-[0.97] border border-zinc-800 text-zinc-300 transition-all duration-150 text-center">
                        <strong class="block text-white">Mahasiswa</strong>
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center text-[10px] text-zinc-500 font-mono mt-4">
            Universitas SIAKAD Enterprise &bull; System v2.6
        </div>
    </div>

    <!-- Floating Toast Notification Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-50 flex flex-col gap-2.5 max-w-md w-auto sm:w-96 pointer-events-none"></div>

    <!-- Global Modal Alert Dialog -->
    <div id="alertModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 backdrop-blur-sm p-4 transition-opacity duration-150 animate-modal-backdrop">
        <div id="modalDialogContent" class="bg-zinc-900 border border-zinc-800 text-zinc-100 rounded-xl max-w-md w-full p-5 shadow-2xl space-y-4 animate-modal-enter">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div id="modalIconContainer" class="w-8 h-8 rounded-lg bg-zinc-800 border border-zinc-700 flex items-center justify-center font-mono font-bold text-xs shrink-0">
                        !
                    </div>
                    <div>
                        <h3 id="modalTitle" class="font-bold text-sm text-zinc-100">Notifikasi Otentikasi</h3>
                        <span id="modalTypeBadge" class="text-[10px] font-mono text-zinc-500 uppercase tracking-wider">ALERT</span>
                    </div>
                </div>
                <button type="button" onclick="closeAlertModal()" class="text-zinc-400 hover:text-zinc-200 text-sm font-mono transition">&times;</button>
            </div>
            <div id="modalBody" class="text-xs text-zinc-300 leading-relaxed font-sans border-y border-zinc-800 py-3 max-h-60 overflow-y-auto">
                Pesan notifikasi sistem.
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeAlertModal()" class="px-4 py-2 bg-zinc-100 text-zinc-900 hover:bg-zinc-200 active:scale-[0.97] rounded-lg text-xs font-semibold font-mono transition-all duration-150">
                    Tutup &bull; OK
                </button>
            </div>
        </div>
    </div>

    <script>
        function fillLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password123';
        }

        function showAlertModal(title, message, type = 'error') {
            const modal = document.getElementById('alertModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            const modalIconContainer = document.getElementById('modalIconContainer');
            const modalTypeBadge = document.getElementById('modalTypeBadge');

            if (!modal) return;

            modalTitle.textContent = title || (type === 'error' ? 'Otentikasi Gagal' : 'Informasi Portal');
            modalBody.innerHTML = message;
            modalTypeBadge.textContent = type.toUpperCase();

            if (type === 'error') {
                modalIconContainer.className = 'w-8 h-8 rounded-lg bg-rose-950/60 border border-rose-800 text-rose-400 flex items-center justify-center font-mono font-bold text-xs shrink-0';
                modalIconContainer.textContent = '✕';
            } else if (type === 'success') {
                modalIconContainer.className = 'w-8 h-8 rounded-lg bg-emerald-950/60 border border-emerald-800 text-emerald-400 flex items-center justify-center font-mono font-bold text-xs shrink-0';
                modalIconContainer.textContent = '✓';
            } else {
                modalIconContainer.className = 'w-8 h-8 rounded-lg bg-zinc-800 border border-zinc-700 text-zinc-300 flex items-center justify-center font-mono font-bold text-xs shrink-0';
                modalIconContainer.textContent = '!';
            }

            const dialogBox = modal.querySelector('div');
            modal.classList.remove('hidden', 'animate-backdrop-leave');
            modal.classList.add('animate-modal-backdrop');
            if (dialogBox) {
                dialogBox.classList.remove('animate-modal-leave');
                dialogBox.classList.add('animate-modal-enter');
            }
        }

        function closeAlertModal() {
            const modal = document.getElementById('alertModal');
            if (!modal || modal.classList.contains('hidden')) return;

            const dialogBox = modal.querySelector('div');
            modal.classList.remove('animate-modal-backdrop');
            modal.classList.add('animate-backdrop-leave');
            if (dialogBox) {
                dialogBox.classList.remove('animate-modal-enter');
                dialogBox.classList.add('animate-modal-leave');
            }

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('animate-backdrop-leave');
                if (dialogBox) dialogBox.classList.remove('animate-modal-leave');
            }, 150);
        }

        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto p-4 rounded-xl shadow-2xl border flex items-start gap-3 bg-zinc-900 text-zinc-100 border-zinc-800 transition-all duration-150 backdrop-blur-md animate-toast-in`;

            let iconSymbol = type === 'error' ? '✕' : (type === 'success' ? '✓' : '!');
            let badgeBg = type === 'error' ? 'bg-rose-950/60 text-rose-300 border-rose-800' : (type === 'success' ? 'bg-emerald-950/60 text-emerald-300 border-emerald-800' : 'bg-zinc-800 text-zinc-300 border-zinc-700');

            toast.innerHTML = `
                <div class="w-7 h-7 rounded-lg ${badgeBg} border flex items-center justify-center font-mono text-xs font-bold shrink-0">
                    ${iconSymbol}
                </div>
                <div class="flex-1 min-w-0 font-sans">
                    <div class="font-bold text-xs text-zinc-100">${title}</div>
                    <div class="text-[11px] text-zinc-400 mt-0.5 leading-relaxed break-words">${message}</div>
                </div>
                <button onclick="dismissToast(this.parentElement)" class="text-zinc-400 hover:text-zinc-200 text-xs p-1 font-mono transition">&times;</button>
            `;

            container.appendChild(toast);
            setTimeout(() => { dismissToast(toast); }, 5000);
        }

        function dismissToast(element) {
            if (!element) return;
            element.classList.remove('animate-toast-in');
            element.classList.add('animate-toast-out');
            setTimeout(() => { element.remove(); }, 150);
        }

        // Double-posting prevention with loading indicator
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                const origText = submitBtn.innerHTML;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memproses...</span>
                `;
                setTimeout(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        submitBtn.innerHTML = origText;
                    }
                }, 10000);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('error'))
                showToast('error', 'Login Gagal', '{{ session('error') }}');
                showAlertModal('Otentikasi Gagal', '{{ session('error') }}', 'error');
            @endif

            @if(session('success'))
                showToast('success', 'Berhasil Logout', '{{ session('success') }}');
            @endif

            @if($errors->any())
                const errorMessages = @json($errors->all());
                const formattedList = errorMessages.map(msg => `&bull; ${msg}`).join('<br>');
                showToast('error', 'Input Kredensial Tidak Valid', errorMessages[0]);
                showAlertModal('Gagal Masuk Portal', `<div class="space-y-1"><strong>Kesalahan Input Kredensial:</strong><br>${formattedList}</div>`, 'error');
            @endif
        });
    </script>
</body>
</html>
