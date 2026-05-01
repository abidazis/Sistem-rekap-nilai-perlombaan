<div wire:poll.10s> 
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">🏆 LEADERBOARD LIVE</h1>
            <p class="text-slate-500">Pantauan hasil perolehan nilai murni secara realtime.</p>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- TOMBOL CETAK KLASEMEN SELURUHNYA -->
            <button onclick="window.open('/cetak-klasemen/{{ $selected_lomba_id }}', '_blank')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow whitespace-nowrap flex items-center gap-2">
                🖨️ Cetak Rekap Akhir
            </button>

            <select wire:model.live="selected_lomba_id" class="w-full md:w-64 border-2 border-slate-300 rounded-lg p-2 font-bold text-slate-700">
                @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->nama_lomba }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-slate-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-900 text-white uppercase text-sm font-bold tracking-wider">
                    <tr>
                        <th class="p-5 text-center w-20">Rank</th>
                        <th class="p-5 w-24 text-center">No</th>
                        <th class="p-5">Nama Sekolah / Tim</th>
                        
                        <!-- KOLOM KATEGORI DINAMIS -->
                        @if(isset($kategoris))
                            @foreach($kategoris as $kat)
                                <th class="p-5 text-center border-l border-slate-700">
                                    {{ $kat->nama_kategori }}
                                </th>
                            @endforeach
                        @endif
                        
                        <th class="p-5 text-center text-red-400 border-l border-slate-700">Minus</th>
                        <th class="p-5 text-center text-yellow-400 text-xl border-l border-slate-700">TOTAL SKOR</th>
                        <!-- TAMBAHAN HEADER AKSI -->
                        <th class="p-5 text-center border-l border-slate-700 w-24">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ranking_peserta as $index => $p)
                        @php
                            $rank = $index + 1;
                            $rowClass = '';
                            $badgeClass = 'bg-slate-100 text-slate-600';
                            
                            if($rank == 1) { 
                                $rowClass = 'bg-yellow-50 border-l-4 border-yellow-400'; 
                                $badgeClass = 'bg-yellow-400 text-yellow-900 shadow-lg scale-110';
                            }
                            elseif($rank == 2) { 
                                $rowClass = 'bg-gray-50 border-l-4 border-gray-300'; 
                                $badgeClass = 'bg-gray-300 text-gray-800 shadow';
                            }
                            elseif($rank == 3) { 
                                $rowClass = 'bg-orange-50 border-l-4 border-orange-300'; 
                                $badgeClass = 'bg-orange-300 text-orange-900 shadow';
                            }
                        @endphp

                        <tr class="hover:bg-blue-50 transition duration-300 {{ $rowClass }}">
                            <td class="p-5 text-center">
                                <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center font-black text-lg {{ $badgeClass }}">
                                    {{ $rank }}
                                </div>
                            </td>
                            <td class="p-5 text-center font-bold text-slate-500">#{{ $p->no_urut }}</td>
                            <td class="p-5">
                                <div class="font-bold text-lg text-slate-800">{{ $p->nama_sekolah }}</div>
                                <div class="text-sm text-slate-500">{{ $p->nama_danton ?? '-' }}</div>
                                @if($p->status_tampil == 'selesai')
                                    <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-wider">Selesai</span>
                                @endif
                            </td>

                            <!-- RENDER NILAI MURNI KATEGORI (Tanpa Desimal) -->
                            @if(isset($kategoris))
                                @foreach($kategoris as $kat)
                                    <td class="p-5 text-center font-bold text-slate-600 border-l border-gray-100 bg-slate-50/50">
                                        {{ number_format($p->skor_kategori[$kat->id] ?? 0, 0, ',', '.') }}
                                    </td>
                                @endforeach
                            @endif

                            <!-- RENDER DENDA -->
                            <td class="p-5 text-center font-bold text-red-500 border-l border-gray-100 bg-red-50/30">
                                -{{ $p->total_minus }}
                            </td>
                            
                            <!-- RENDER TOTAL SKOR (Tanpa Desimal) -->
                            <td class="p-5 text-center border-l border-gray-100 bg-yellow-50/30">
                                <span class="text-2xl font-black text-slate-800 tracking-tighter">
                                    {{ number_format($p->total_skor, 0, ',', '.') }}
                                </span>
                            </td>
                            
                            <!-- TOMBOL CETAK PER PESERTA (Posisi yang benar) -->
                            <td class="p-5 text-center border-l border-gray-100 bg-white">
                                <button onclick="window.open('/cetak-peserta/{{ $p->id }}', '_blank')" class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold py-1.5 px-3 rounded shadow" title="Cetak Rincian Nilai">
                                    📄 Print
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-10 text-center text-gray-400">
                                Belum ada data nilai masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4 text-center text-xs text-slate-400">
        Halaman ini melakukan refresh otomatis setiap 10 detik.
    </div>
</div>