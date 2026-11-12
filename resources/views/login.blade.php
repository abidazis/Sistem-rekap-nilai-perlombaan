<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PANDARA System</title>
    <script src="{{ asset('js/tailwind.js') }}"></script>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">
    
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden max-w-md w-full animate-fade-in-down">
        
        <div class="bg-slate-800 p-8 text-center border-b-4 border-yellow-500">
            <h1 class="text-4xl font-black tracking-widest text-yellow-400 mb-2">PANDARA</h1>
            <p class="text-slate-400 text-sm font-bold tracking-wide uppercase">Sistem Penilaian Cerdas</p>
        </div>
        
        <div class="p-8">
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm text-sm font-bold flex items-center gap-2">
                    <span>❌</span> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="mb-5">
                    <label class="block text-slate-600 text-xs font-bold uppercase mb-2">Username Akses</label>
                    <input type="text" name="username" class="w-full border-2 border-slate-200 rounded-xl p-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none font-mono text-slate-800 transition-all bg-slate-50" placeholder="Ketik username Anda" required autofocus value="{{ old('username') }}">
                </div>
                
                <div class="mb-8">
                    <label for="password" class="block text-slate-600 text-xs font-bold uppercase mb-2">Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" autocomplete="current-password" class="w-full border-2 border-slate-200 rounded-xl p-3 pr-12 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none font-mono text-slate-800 transition-all bg-slate-50" placeholder="••••••••" required>
                        <button type="button" id="togglePassword" aria-label="Tampilkan password" title="Tampilkan password" class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 hover:text-blue-600 focus:outline-none focus:text-blue-600 transition-colors">
                            <!-- Ikon mata terbuka (password tersembunyi) -->
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <!-- Ikon mata dicoret (password terlihat) -->
                            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-blue-700 to-blue-600 hover:from-blue-800 hover:to-blue-700 text-white font-black py-4 px-4 rounded-xl shadow-lg shadow-blue-500/30 transition-all transform hover:-translate-y-1 tracking-wider text-lg">
                    🔒 MASUK KE SISTEM
                </button>
            </form>
        </div>
        
        <div class="bg-slate-50 text-center p-4 border-t border-slate-100">
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">&copy; 2026 PANDARA System Developer</p>
        </div>
    </div>

    <script>
        (function () {
            const toggle = document.getElementById('togglePassword');
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            toggle.addEventListener('click', function () {
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                eyeOpen.classList.toggle('hidden', show);
                eyeClosed.classList.toggle('hidden', !show);
                toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
                toggle.setAttribute('title', show ? 'Sembunyikan password' : 'Tampilkan password');
            });
        })();
    </script>

</body>
</html>