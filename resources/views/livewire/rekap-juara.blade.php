<div wire:poll.10s> 

    {{-- 1. Header & Filter Lengkap --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">🏆 LEADERBOARD LIVE</h1>
            <p class="text-slate-500 text-sm">Pantauan hasil perolehan nilai murni secara realtime.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-end">
            @if(auth()->check() && auth()->user()->role === 'admin')
                <button onclick="window.open('/cetak-kategori/{{ $selected_lomba_id }}/{{ $selected_tingkat ?? 'SMP' }}', '_blank')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 rounded-lg shadow flex items-center gap-1">
                    🏅 Kategori
                </button>
                <button onclick="window.open('/cetak-pengumuman-pdf/{{ $selected_lomba_id }}/{{ $selected_tingkat ?? 'SMP' }}', '_blank')" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-3 rounded-lg shadow flex items-center gap-1">
                    📑 Lembar MC
                </button>
                <button onclick="window.open('/cetak-pengumuman-excel/{{ $selected_lomba_id }}/{{ $selected_tingkat ?? 'SMP' }}', '_blank')" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 px-3 rounded-lg shadow flex items-center gap-1">
                    📊 Export Excel
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

            <select wire:model.live="mode_tampilan" class="border-2 border-purple-400 bg-purple-50 rounded-lg p-2 text-sm font-black text-purple-800 focus:ring-2 focus:ring-purple-300">
                <option value="utama">🏆 KLASEMEN UTAMA</option>
                <option value="umum">👑 KANDIDAT UMUM</option>
                <optgroup label="PER KATEGORI MURNI">
                    @foreach($semua_kategori as $kat)
                        <option value="{{ $kat->id }}">🔥 BEST {{ $kat->nama_kategori }}</option>
                    @endforeach
                </optgroup>
            </select>

        </div>
    </div>

    {{-- 2. Tabel Utama --}}
    <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-900 text-white uppercase text-[11px] font-bold tracking-wider">
                    <tr>
                        <th class="p-4 text-center w-16">Rank</th>
                        <th class="p-4 w-16 text-center">No</th>
                        <th class="p-4">Nama Sekolah / Tim</th>
                        <th class="p-4 text-center">WAKTU</th>
                        
                        @foreach($kolom_kategori_tampil as $kat)
                            <th class="p-4 text-center border-l border-slate-700 bg-slate-800">
                                {{ $kat->nama_kategori }}
                            </th>
                        @endforeach
                        
                        @if($mode_tampilan == 'utama' || $mode_tampilan == 'umum')
                            <th class="p-4 text-center text-red-400 border-l border-slate-700">Minus</th>
                        @endif
                        
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

                            <td class="p-4 text-center">
                                @php
                                    $durasi = $p->durasi_tampil_detik ?? 0;
                                    $menit = floor($durasi / 60);
                                    $detik = $durasi % 60;
                                @endphp
                                
                                @if($durasi > 0)
                                    <div class="font-black text-slate-700 bg-slate-100 rounded-md px-2 py-1 inline-block border border-slate-200 shadow-sm text-sm" title="{{ $durasi }} Detik">
                                        ⏱️ {{ sprintf('%02d:%02d', $menit, $detik) }}
                                    </div>
                                @else
                                    <span class="text-slate-300 font-bold">-</span>
                                @endif
                            </td>

                            @foreach($kolom_kategori_tampil as $kat)
                                <td class="p-4 text-center font-bold text-slate-600 border-l border-gray-50 text-sm">
                                    {{ number_format($p->skor_kategori[$kat->id] ?? 0, 0, ',', '.') }}
                                </td>
                            @endforeach

                            @if($mode_tampilan == 'utama' || $mode_tampilan == 'umum')
                                <td class="p-4 text-center font-bold text-red-500 border-l border-gray-50 text-sm">
                                    -{{ $p->total_minus }}
                                </td>
                            @endif
                            
                            <td class="p-4 text-center border-l border-gray-100 bg-slate-50/30">
                                <span class="text-lg font-black text-slate-900">
                                    {{ number_format($p->total_skor, 0, ',', '.') }}
                                </span>
                            </td>
                            
                            <td class="p-4 text-center border-l border-gray-100">
                                <button onclick="window.open('/cetak-peserta/{{ $p->id }}', '_blank')" class="text-slate-400 hover:text-blue-600 transition" title="Cetak Kartu Nilai">
                                    🖨️
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            @php 
                                $colspan = count($kolom_kategori_tampil) + ($mode_tampilan == 'utama' || $mode_tampilan == 'umum' ? 5 : 4); 
                            @endphp
                            <td colspan="{{ $colspan }}" class="p-10 text-center text-slate-400 italic">
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
</div>