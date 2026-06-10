<div class="animate-fade-in-down">
    @php
        // Mengambil data dinamis langsung untuk dashboard
        $lomba_aktif = \App\Models\Lomba::latest()->first();
        $total_peserta = $lomba_aktif ? \App\Models\Peserta::where('lomba_id', $lomba_aktif->id)->count() : 0;
        $peserta_selesai = $lomba_aktif ? \App\Models\Peserta::where('lomba_id', $lomba_aktif->id)->where('status_tampil', 'selesai')->count() : 0;
        $total_juri = $lomba_aktif ? \App\Models\Juri::where('lomba_id', $lomba_aktif->id)->count() : 0;
        
        // Menghitung persentase progress
        $progress = $total_peserta > 0 ? round(($peserta_selesai / $total_peserta) * 100) : 0;
    @endphp

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">PANDARA COMMAND CENTER</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Sistem Rekapitulasi Penilaian Paskibra Real-time</p>
        </div>
        <div class="bg-slate-900 text-white px-5 py-2 rounded-lg shadow-lg flex items-center gap-3">
            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
            <span class="font-bold text-sm tracking-wider">SYSTEM ONLINE</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-6 rounded-2xl shadow-lg text-white transform transition hover:-translate-y-1">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-blue-200 text-xs font-bold uppercase tracking-wider mb-1">Event Aktif</div>
                    <div class="text-xl font-black leading-tight">{{ $lomba_aktif ? strtoupper($lomba_aktif->nama_lomba) : 'BELUM ADA EVENT' }}</div>
                </div>
                <div class="bg-white/20 p-2 rounded-lg text-2xl">🏆</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 transform transition hover:-translate-y-1">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Total Peserta</div>
                    <div class="text-3xl font-black text-slate-800">{{ $total_peserta }} <span class="text-sm text-slate-400 font-medium">Tim</span></div>
                </div>
                <div class="bg-emerald-100 text-emerald-600 p-2 rounded-lg text-2xl">👥</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 transform transition hover:-translate-y-1">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Juri Bertugas</div>
                    <div class="text-3xl font-black text-slate-800">{{ $total_juri }} <span class="text-sm text-slate-400 font-medium">Orang</span></div>
                </div>
                <div class="bg-purple-100 text-purple-600 p-2 rounded-lg text-2xl">⚖️</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 transform transition hover:-translate-y-1">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Progress Tampil</div>
                    <div class="text-2xl font-black text-slate-800">{{ $peserta_selesai }} / {{ $total_peserta }}</div>
                </div>
                <div class="text-orange-500 font-black text-xl">{{ $progress }}%</div>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 mt-2">
                <div class="bg-orange-500 h-2.5 rounded-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="font-black text-slate-800 text-lg flex items-center gap-2">
                    <span>🚀</span> SHORTCUT OPERASIONAL
                </h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <a href="/rekap-juara" class="group flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🥇</div>
                        <div>
                            <h4 class="font-bold text-slate-800">Leaderboard Live</h4>
                            <p class="text-xs text-slate-500 mt-1">Pantau klasemen realtime</p>
                        </div>
                    </div>
                    <span class="text-slate-400 group-hover:text-blue-500 font-bold">&rarr;</span>
                </a>

                <a href="/input-juara-spesial" class="group flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-purple-500 hover:bg-purple-50 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🌟</div>
                        <div>
                            <h4 class="font-bold text-slate-800">Juara Tersendiri</h4>
                            <p class="text-xs text-slate-500 mt-1">Input Best Vafor, Kostum, dll</p>
                        </div>
                    </div>
                    <span class="text-slate-400 group-hover:text-purple-500 font-bold">&rarr;</span>
                </a>

                <a href="{{ route('input.nilai') ?? '#' }}" class="group flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">✍️</div>
                        <div>
                            <h4 class="font-bold text-slate-800">Panel Juri</h4>
                            <p class="text-xs text-slate-500 mt-1">Masuk ke form input nilai</p>
                        </div>
                    </div>
                    <span class="text-slate-400 group-hover:text-emerald-500 font-bold">&rarr;</span>
                </a>

                <a href="{{ route('master.event') ?? '#' }}" class="group flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-slate-500 hover:bg-slate-100 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-slate-200 text-slate-600 rounded-lg flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">⚙️</div>
                        <div>
                            <h4 class="font-bold text-slate-800">Master Event</h4>
                            <p class="text-xs text-slate-500 mt-1">Konfigurasi Lomba & Kategori</p>
                        </div>
                    </div>
                    <span class="text-slate-400 group-hover:text-slate-600 font-bold">&rarr;</span>
                </a>

            </div>
        </div>

        <div class="bg-slate-900 rounded-2xl shadow-lg border border-slate-700 overflow-hidden text-white flex flex-col">
            <div class="p-6 border-b border-slate-700 bg-slate-800/50">
                <h3 class="font-black text-slate-200 text-lg flex items-center gap-2">
                    <span>📡</span> STATUS JARINGAN
                </h3>
            </div>
            <div class="p-6 flex-1 flex flex-col justify-center">
                <div class="text-center mb-6">
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-2">URL Akses Client (WIFI)</div>
                    <div class="bg-slate-950 p-3 rounded-lg border border-slate-700 font-mono text-emerald-400 font-bold text-lg">
                        {{ env('APP_URL', 'http://127.0.0.1:8000') }}
                    </div>
                    <p class="text-[10px] text-slate-500 mt-2">*Berikan link di atas ke Juri yang terhubung ke jaringan Hotspot/WIFI yang sama.</p>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400">Database Status</span>
                        <span class="font-bold text-emerald-400 flex items-center gap-1"><div class="w-2 h-2 bg-emerald-400 rounded-full"></div> Connected</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400">Environment</span>
                        <span class="font-bold text-blue-400 uppercase">{{ env('APP_ENV', 'Local') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>