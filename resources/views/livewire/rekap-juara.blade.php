<div wire:poll.10s> {{-- 1. Pengaturan Admin (Hanya muncul jika user admin) --}}
    @if(auth()->check() && str_contains(strtolower(auth()->user()->posisi), 'admin'))
    <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500 mb-8 animate-fade-in-down">
        <h3 class="font-black text-slate-700 text-lg mb-4 flex items-center gap-2">
            <span>⚙️</span> PENGATURAN KLASEMEN & TIE-BREAKER
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Format Predikat Juara</label>
                <select wire:model="format_juara" class="w-full border-2 border-slate-200 rounded-lg p-3 font-bold text-slate-700">
                    <option value="all_harapan">Format All Trophy</option>
                    <option value="standard">Format Standar (Utama, Harapan, Madya, Bina)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Prioritas Tie-Breaker</label>
                <div class="flex flex-col gap-2 bg-slate-50 p-3 rounded-lg border border-slate-200">
                    @if(count($kategoris) > 0)
                        @foreach($kategoris as $k)
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" wire:model="tie_breakers" value="{{ $k->id }}" class="w-5 h-5 rounded border-gray-300 text-blue-600">
                                <span class="font-bold text-sm text-slate-700">Adu Nilai {{ $k->nama_kategori }}</span>
                            </label>
                        @endforeach
                    @else
                        <span class="text-xs text-slate-400 italic">Pilih Lomba dulu untuk melihat kategori</span>
                    @endif
                </div>
            </div>
        </div>
        <button wire:click="simpanPengaturan" class="mt-5 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">
            💾 Simpan Pengaturan
        </button>
        @if (session()->has('message_setting'))
            <span class="ml-3 text-sm font-bold text-green-600">✅ {{ session('message_setting') }}</span>
        @endif
    </div>
    @endif

    {{-- 2. Header & Filter --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">🏆 LEADERBOARD LIVE</h1>
            <p class="text-slate-500 text-sm">Pantauan hasil perolehan nilai murni secara realtime.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-end">
            @if(auth()->check() && str_contains(strtolower(auth()->user()->posisi), 'admin'))
                <button onclick="window.open('/cetak-kategori/{{ $selected_lomba_id }}/{{ $selected_tingkat ?? 'SMP' }}', '_blank')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 rounded-lg shadow flex items-center gap-1">
                    🏅 Kategori
                </button>
                <button onclick="document.getElementById('modalUtama').classList.remove('hidden')" class="bg-yellow-500 hover:bg-yellow-600 text-slate-900 text-xs font-bold py-2 px-3 rounded-lg shadow flex items-center gap-1">
                    🏆 Juara Utama
                </button>
                <button onclick="window.open('/cetak-pengumuman-pdf/{{ $selected_lomba_id }}/{{ $selected_tingkat ?? 'SMP' }}', '_blank')" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-3 rounded-lg shadow flex items-center gap-1">
                    📑 Lembar MC
                </button>
            @endif

            <select wire:model.live="selected_lomba_id" class="border-2 border-slate-300 rounded-lg p-2 text-sm font-bold text-slate-700">
                @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->nama_lomba }}</option>
                @endforeach
            </select>

            <select wire:model.live="selected_tingkat" class="border-2 border-blue-400 bg-blue-50 rounded-lg p-2 text-sm font-black text-blue-800">
                <option value="SD">SD</option>
                <option value="SMP">SMP</option>
                <option value="SMA">SMA</option>
                <option value="UMUM">UMUM</option>
            </select>
        </div>
    </div>

    {{-- 3. Tabel Utama --}}
    <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-900 text-white uppercase text-[11px] font-bold tracking-wider">
                    <tr>
                        <th class="p-4 text-center w-16">Rank</th>
                        <th class="p-4 w-16 text-center">No</th>
                        <th class="p-4">Nama Sekolah / Tim</th>
                        
                        @foreach($kategoris as $kat)
                            <th class="p-4 text-center border-l border-slate-700 bg-slate-800">
                                {{ $kat->nama_kategori }}
                            </th>
                        @endforeach
                        
                        <th class="p-4 text-center text-red-400 border-l border-slate-700">Minus</th>
                        <th class="p-4 text-center text-yellow-400 text-base border-l border-slate-700">TOTAL</th>
                        <th class="p-4 text-center border-l border-slate-700 w-20">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ranking_peserta as $index => $p)
                        @php
                            $rank = $index + 1;
                            $rowClass = $rank == 1 ? 'bg-yellow-50/50' : ($rank == 2 ? 'bg-gray-50/50' : ($rank == 3 ? 'bg-orange-50/50' : ''));
                            $badgeClass = $rank == 1 ? 'bg-yellow-400 text-yellow-900' : ($rank == 2 ? 'bg-slate-300 text-slate-800' : ($rank == 3 ? 'bg-orange-300 text-orange-900' : 'bg-slate-100 text-slate-500'));
                        @endphp

                        <tr class="hover:bg-blue-50/50 transition {{ $rowClass }}">
                            <td class="p-4 text-center">
                                <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center font-black text-sm {{ $badgeClass }}">
                                    {{ $rank }}
                                </div>
                            </td>
                            <td class="p-4 text-center font-bold text-slate-400 text-sm">#{{ $p->no_urut }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800 uppercase text-sm">{{ $p->nama_sekolah }}</div>
                                @if($p->status_tampil == 'selesai')
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-green-100 text-green-700">SELESAI</span>
                                @endif
                            </td>

                            {{-- Loop Nilai Kategori --}}
                            @foreach($kategoris as $kat)
                                <td class="p-4 text-center font-bold text-slate-600 border-l border-gray-50 text-sm">
                                    {{ number_format($p->skor_kategori[$kat->id] ?? 0, 0, ',', '.') }}
                                </td>
                            @endforeach

                            <td class="p-4 text-center font-bold text-red-500 border-l border-gray-50 text-sm">
                                -{{ $p->total_minus }}
                            </td>
                            
                            <td class="p-4 text-center border-l border-gray-100 bg-slate-50/30">
                                <span class="text-lg font-black text-slate-900">
                                    {{ number_format($p->total_skor, 0, ',', '.') }}
                                </span>
                            </td>
                            
                            <td class="p-4 text-center border-l border-gray-100">
                                <button onclick="window.open('/cetak-peserta/{{ $p->id }}', '_blank')" class="text-slate-400 hover:text-blue-600 transition">
                                    🖨️
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($kategoris) + 5 }}" class="p-10 text-center text-slate-400 italic">
                                Belum ada data peserta untuk tingkat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4 text-center text-[10px] text-slate-400 mb-10">
        Auto-refresh aktif (10s) • Pandara System v2.0
    </div>

    {{-- 4. Modal Juara Utama --}}
    <div id="modalUtama" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm border-t-4 border-yellow-500">
            <h3 class="text-lg font-black text-slate-800 mb-1">🏆 Setup Juara Utama</h3>
            <p class="text-xs text-slate-500 mb-4">Pilih kategori untuk akumulasi Juara Utama.</p>
            
            <form action="/cetak-utama/{{ $selected_lomba_id }}" target="_blank" method="GET">
                <input type="hidden" name="tingkat" value="{{ $selected_tingkat }}">
                <div class="flex flex-col gap-2 mb-6 max-h-60 overflow-y-auto">
                    @foreach($kategoris as $kat)
                        <label class="flex items-center gap-3 p-2 bg-slate-50 rounded-lg hover:bg-blue-50 cursor-pointer border border-slate-100">
                            <input type="checkbox" name="kategori[]" value="{{ $kat->id }}" class="w-4 h-4 text-blue-600 rounded" checked>
                            <span class="font-bold text-xs text-slate-700">{{ $kat->nama_kategori }}</span>
                        </label>
                    @endforeach
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalUtama').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-slate-400">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg shadow-md">Cetak Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>