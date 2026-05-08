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
                    <label class="block text-slate-600 text-xs font-bold uppercase mb-2">Password</label>
                    <input type="password" name="password" class="w-full border-2 border-slate-200 rounded-xl p-3 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 focus:outline-none font-mono text-slate-800 transition-all bg-slate-50" placeholder="••••••••" required>
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

</body>
</html>