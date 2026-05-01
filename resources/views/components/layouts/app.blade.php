<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PANDARA System</title>
    <script src="{{ asset('js/tailwind.js') }}"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="min-h-screen flex flex-col md:flex-row">
        
        <aside class="w-full md:w-64 bg-slate-900 text-white flex-shrink-0">
            <div class="p-6 border-b border-slate-800">
                <h1 class="text-2xl font-bold tracking-wider text-yellow-400">PANDARA</h1>
                <p class="text-xs text-slate-400">System Rekap Nilai</p>
            </div>

            <nav class="mt-6 px-4 space-y-2">
                <a href="{{ route('dashboard') }}" wire:navigate class="block px-4 py-2 rounded transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    🏠 Dashboard
                </a>

                <div class="text-xs font-bold text-slate-500 uppercase mt-4 mb-2 px-2">Master Data</div>
                
                <a href="{{ route('master.event') }}" wire:navigate class="block px-4 py-2 rounded transition {{ request()->routeIs('master.event') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    📅 Master Event
                </a>

                <a href="{{ route('master.kategori') }}" wire:navigate class="block px-4 py-2 rounded transition {{ request()->routeIs('master.kategori') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    📂 Kategori Lomba
                </a>

                <a href="{{ route('master.format') }}" wire:navigate class="block px-4 py-2 rounded transition {{ request()->routeIs('master.format') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800' }}">
                    📝 Format Nilai
                </a>

                <div class="text-xs font-bold text-slate-500 uppercase mt-4 mb-2 px-2">Operasional</div>

                <a href="{{ route('input.nilai') }}" wire:navigate class="block px-4 py-2 rounded transition {{ request()->routeIs('input.nilai') ? 'bg-green-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                    ✍️ INPUT NILAI
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-6 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

</body>
</html>