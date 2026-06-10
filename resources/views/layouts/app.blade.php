<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PANDARA System</title>
    <script src="{{ asset('js/tailwind.js') }}"></script>
    @livewireStyles
    
    <style>
        /* Sembunyikan scrollbar untuk Chrome, Safari dan Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Sembunyikan scrollbar untuk IE, Edge dan Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-800 overflow-x-hidden">

    <div x-data="{ sidebarOpen: true }" class="min-h-screen">
        
        <aside 
            :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="bg-slate-900 text-white transition-all duration-300 ease-in-out fixed inset-y-0 left-0 z-50 flex flex-col shadow-2xl border-r border-slate-800"
        >
            <div class="h-16 flex items-center justify-center border-b border-slate-800 bg-[#1e293b]/50 whitespace-nowrap">
                <div x-show="sidebarOpen" class="text-center w-full transition-opacity duration-300">
                    <h1 class="text-xl font-black tracking-widest text-yellow-400">PANDARA</h1>
                </div>
                <div x-show="!sidebarOpen" class="text-2xl font-black text-yellow-400 transition-opacity duration-300">
                    P
                </div>
            </div>

            <nav class="flex-1 py-4 flex flex-col gap-2 overflow-y-auto no-scrollbar">
                
                <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center mx-3 px-3 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}" :class="!sidebarOpen ? 'justify-center' : ''">
                    <span class="text-xl">🏠</span>
                    <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm whitespace-nowrap">Dashboard</span>
                </a>
                <a href="/rekap-juara" wire:navigate class="flex items-center mx-3 px-3 py-3 rounded-lg transition-colors {{ request()->is('rekap-juara') ? 'bg-yellow-500 text-slate-900 font-bold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}" :class="!sidebarOpen ? 'justify-center' : ''">
                    <span class="text-xl">🏆</span>
                    <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm whitespace-nowrap">LEADERBOARD</span>
                </a>

                @if(auth()->check() && str_contains(strtolower(auth()->user()->posisi), 'admin'))
                    <div x-show="sidebarOpen" class="mt-4 px-6 text-[10px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Master Data</div>
                    <div x-show="!sidebarOpen" class="mt-4 text-center text-slate-600 text-xs">•••</div>

                    <a href="{{ route('master.event') }}" wire:navigate class="flex items-center mx-3 px-3 py-3 rounded-lg transition-colors {{ request()->routeIs('master.event') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}" :class="!sidebarOpen ? 'justify-center' : ''">
                        <span class="text-xl">📅</span>
                        <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm whitespace-nowrap">Master Event</span>
                    </a>
                    
                    <a href="{{ route('master.kategori') }}" wire:navigate class="flex items-center mx-3 px-3 py-3 rounded-lg transition-colors {{ request()->routeIs('master.kategori') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}" :class="!sidebarOpen ? 'justify-center' : ''">
                        <span class="text-xl">📂</span>
                        <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm whitespace-nowrap">Kategori Lomba</span>
                    </a>

                    <a href="{{ route('master.format') }}" wire:navigate class="flex items-center mx-3 px-3 py-3 rounded-lg transition-colors {{ request()->routeIs('master.format') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}" :class="!sidebarOpen ? 'justify-center' : ''">
                        <span class="text-xl">📝</span>
                        <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm whitespace-nowrap">Format Nilai</span>
                    </a>

                    <a href="{{ route('master.peserta') }}" wire:navigate class="flex items-center mx-3 px-3 py-3 rounded-lg transition-colors {{ request()->routeIs('master.peserta') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}" :class="!sidebarOpen ? 'justify-center' : ''">
                        <span class="text-xl">👮‍♂️</span>
                        <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm whitespace-nowrap">Master Peserta</span>
                    </a>

                    <a href="{{ route('master.juri') }}" wire:navigate class="flex items-center mx-3 px-3 py-3 rounded-lg transition-colors {{ request()->routeIs('master.juri') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}" :class="!sidebarOpen ? 'justify-center' : ''">
                        <span class="text-xl">⚖️</span>
                        <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm whitespace-nowrap">Master Juri</span>
                    </a>
                @endif
                <div x-show="sidebarOpen" class="mt-4 px-6 text-[10px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Operasional</div>
                <div x-show="!sidebarOpen" class="mt-4 text-center text-slate-600 text-xs">•••</div>

                <a href="/input-nilai" wire:navigate class="flex items-center mx-3 px-3 py-3 rounded-lg transition-colors {{ request()->is('input-nilai') ? 'bg-green-600 text-white font-bold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}" :class="!sidebarOpen ? 'justify-center' : ''">
                    <span class="text-xl">✍️</span>
                    <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm whitespace-nowrap">INPUT NILAI</span>
                </a>
                <a href="/input-juara-spesial" class="flex items-center mx-3 px-3 py-3 rounded-lg transition-colors {{ request()->is('input-juara-spesial') ? 'bg-green-600 text-white font-bold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}" :class="!sidebarOpen ? 'justify-center' : ''">
                    <span class="text-xl">✍️</span>
                    <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm whitespace-nowrap">INPUT JUARA SPESIAL</span>
                </a>

                @if(auth()->check() && str_contains(strtolower(auth()->user()->posisi), 'admin'))
                    <a href="/input-denda" wire:navigate class="flex items-center mx-3 px-3 py-3 rounded-lg transition-colors {{ request()->is('input-denda') ? 'bg-red-600 text-white font-bold shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}" :class="!sidebarOpen ? 'justify-center' : ''">
                        <span class="text-xl">⚠️</span>
                        <span x-show="sidebarOpen" class="ml-3 font-semibold text-sm whitespace-nowrap">Pengurangan Nilai</span>
                    </a>
                @endif

                {{-- <div x-show="sidebarOpen" class="mt-4 px-6 text-[10px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Hasil Lomba</div>
                <div x-show="!sidebarOpen" class="mt-4 text-center text-slate-600 text-xs">•••</div> --}}
            </nav>
        </aside>

        <div 
            :class="sidebarOpen ? 'ml-64' : 'ml-20'"
            class="flex flex-col min-h-screen transition-all duration-300 ease-in-out"
        >
            <header class="bg-white h-16 shadow-sm border-b border-slate-200 sticky top-0 z-40 px-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-red-100 hover:text-red-700 transition focus:outline-none shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h2 class="text-lg font-black text-slate-800 hidden sm:block tracking-tight">
                        @if(auth()->check() && str_contains(strtolower(auth()->user()->posisi), 'admin'))
                            Administrator Panel
                        @else
                            Tim Rekap Panel
                        @endif
                    </h2>
                </div>
                
                <div class="flex items-center gap-4">
                    @auth
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-slate-800">{{ auth()->user()->nama }}</p>
                            <p class="text-[10px] text-green-600 font-bold uppercase tracking-wider">● Online ({{ auth()->user()->posisi }})</p>
                        </div>
                        <div class="h-9 w-9 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold shadow-md">
                            {{ substr(auth()->user()->nama, 0, 1) }}
                        </div>
                        
                        <form method="POST" action="{{ route('logout') }}" class="ml-2 border-l pl-4 border-slate-200">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 hover:underline">
                                Logout
                            </button>
                        </form>
                    @endauth
                </div>
            </header>

            <main class="flex-1 p-6 md:p-8">
                {{ $slot }}
            </main>
        </div>
        
    </div>

    @livewireScripts
</body>
</html>