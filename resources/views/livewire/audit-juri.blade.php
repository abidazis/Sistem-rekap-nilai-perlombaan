<div class="space-y-6">
    <!-- HEADER & FILTER -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">🔍 AUDIT MATRIKS JURI</h2>
            <p class="text-sm text-slate-500">Analisis perbandingan total dan rata-rata nilai juri per event.</p>
        </div>
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <!-- Filter Event -->
            <select wire:model.live="lomba_id" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 font-bold">
                @foreach($lombas as $lomba)
                    <option value="{{ $lomba->id }}">{{ $lomba->nama_lomba ?? 'Event ' . $lomba->id }}</option>
                @endforeach
            </select>

            <!-- Filter Tingkat -->
            <select wire:model.live="tingkat_filter" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 font-bold">
                <option value="">Semua Tingkat</option>
                <option value="SMP">SMP</option>
                <option value="SMA">SMA</option>
            </select>

            <!-- Filter Juri -->
            <select wire:model.live="juri_filter" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 font-bold">
                <option value="">Semua Juri</option>
                @foreach($juris as $juri)
                    <option value="{{ $juri->id }}">{{ $juri->nama ?? 'Juri' }} - {{ $juri->posisi ?? '' }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- GRAFIK ANALISIS JURI DENGAN KONTEKS POSISI -->
    @if(count($filteredJuris) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4 border-b pb-2">📊 Grafik Akumulasi Nilai Juri</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($filteredJuris as $statKey => $stat)
            <div class="bg-slate-50 rounded-lg p-4 border border-slate-100 flex flex-col justify-between">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex flex-col">
                        <span class="font-black text-slate-800 uppercase">{{ $stat['nama'] }}</span>
                        <!-- Label Posisi Juri -->
                        <span class="text-[10px] font-bold text-slate-600 bg-slate-200 border border-slate-300 px-2 py-0.5 rounded mt-1 w-fit uppercase">
                            {{ $stat['posisi'] }}
                        </span>
                    </div>
                    <span class="text-xs font-black text-blue-700 bg-blue-100 border border-blue-200 px-2 py-1 rounded">Rata-rata: {{ $stat['rata_rata'] }}</span>
                </div>
                <!-- Bar Chart -->
                <div class="w-full bg-gray-200 rounded-full h-4 mb-2 mt-auto">
                    <div class="bg-blue-600 h-4 rounded-full transition-all duration-500" style="width: {{ $stat['persentase_bar'] ?? 0 }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-slate-500 font-semibold">
                    <span>Total: {{ number_format($stat['total_akumulasi'], 0, ',', '.') }}</span>
                    <span>Menilai: {{ $stat['jumlah_dinilai'] }} Tim</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- TABEL MATRIKS DENGAN STICKY COLUMN -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white text-xs uppercase tracking-wider">
                        <!-- Penggabungan No & Nama Sekolah + Class Sticky -->
                        <th class="p-4 whitespace-nowrap sticky left-0 z-20 bg-slate-900 shadow-[4px_0_10px_-3px_rgba(0,0,0,0.5)]">
                            Peserta / Sekolah
                        </th>
                        
                        @forelse($filteredJuris as $juri)
                            <th class="p-4 text-center whitespace-nowrap border-l border-slate-700">
                                <div class="font-bold text-sm">{{ $juri->nama ?? $juri->name }}</div>
                                <!-- Munculkan posisi di header tabel -->
                                <div class="text-[10px] text-yellow-400 font-normal uppercase tracking-wider mt-1">
                                    {{ $juri->posisi ?? 'Juri' }}
                                </div>
                            </th>
                        @empty
                            <th class="p-4 text-center whitespace-nowrap">Juri</th>
                        @endforelse
                        
                        <th class="p-4 text-center font-bold text-yellow-400 border-l border-slate-700 whitespace-nowrap">
                            Total Keseluruhan
                        </th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($pesertas as $index => $peserta)
                    <!-- Tambahkan class 'group' untuk efek hover yang sinkron -->
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition group">
                        
                        <!-- Sel Sticky dengan sinkronisasi background saat dihover -->
                        <td class="p-4 font-semibold text-blue-600 whitespace-nowrap sticky left-0 z-10 bg-white group-hover:bg-slate-50 border-r border-slate-200 shadow-[4px_0_10px_-3px_rgba(0,0,0,0.1)] transition-colors">
                            <span class="text-slate-400 font-normal mr-2">{{ $index + 1 }}.</span> {{ $peserta->nama_sekolah ?? 'Nama Tim' }}
                        </td>
                        
                        @foreach($filteredJuris as $juri)
                            <td class="p-4 text-center font-medium text-slate-600 border-l border-slate-100">
                                {{ $peserta->nilai_per_juri[$juri->id] ?? 0 }}
                            </td>
                        @endforeach
                        
                        <td class="p-4 text-center font-black text-slate-800 text-lg border-l border-slate-200 bg-slate-50">
                            {{ $peserta->total_keseluruhan }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="100%" class="text-center p-8 text-slate-500 font-medium">Belum ada data peserta/nilai untuk event ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>