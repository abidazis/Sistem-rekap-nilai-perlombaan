<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-red-700 tracking-tight flex items-center gap-2">
                <span>⚠️</span> INPUT PENGURANGAN NILAI
            </h1>
            <p class="text-slate-500 mt-1">Catat denda poin akibat pelanggaran pasukan di lapangan sesuai buku pedoman.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 font-bold shadow-sm rounded-r-lg flex items-center gap-3">
            <span class="text-xl">✅</span> {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- PANEL KIRI: FORM INPUT SMART -->
        <div class="md:col-span-1 bg-white rounded-xl shadow-lg border border-slate-200 p-6 self-start relative overflow-hidden">
            <!-- Pita Hiasan -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-orange-400"></div>

            <form wire:submit.prevent="simpan">
                
                <div class="mb-5">
                    <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Pilih Event Lomba</label>
                    <select wire:model.live="selected_lomba_id" class="w-full border-2 border-slate-200 rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:border-red-500 transition-colors cursor-pointer">
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->nama_lomba }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Pilih Peserta (Target Denda)</label>
                    <select wire:model.live="selected_peserta_id" class="w-full bg-yellow-50 font-bold border-2 border-yellow-400 text-yellow-900 rounded-lg p-3 focus:border-red-500 focus:ring focus:ring-red-200 transition-all cursor-pointer shadow-sm">
                        <option value="">-- Ketik / Cari Peserta --</option>
                        @foreach($pesertas as $p)
                            <option value="{{ $p->id }}">#{{ $p->no_urut }} - {{ $p->nama_sekolah }}</option>
                        @endforeach
                    </select>
                </div>

                <hr class="my-6 border-slate-200 border-dashed">

                @if($selected_peserta_id)
                    <!-- DROPDOWN OTOMATIS SESUAI PEDOMAN -->
                    <div class="mb-5">
                        <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Kategori Pelanggaran</label>
                        <select wire:model.live="jenis_pelanggaran" class="w-full border-2 border-slate-300 rounded-lg p-3 font-bold text-slate-700 focus:border-red-500 focus:ring focus:ring-red-200 transition-all cursor-pointer shadow-sm" required>
                            <option value="">-- Pilih Sesuai Pedoman --</option>
                            @if(isset($daftar_pelanggaran))
                                @foreach($daftar_pelanggaran as $nama => $poin)
                                    <option value="{{ $nama }}">
                                        {{ $nama }} {{ $poin ? '(-'.$poin.')' : '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- INPUT ANGKA SMART -->
                    <div class="mb-5">
                        <label class="block text-red-600 text-xs font-black uppercase mb-2 flex justify-between">
                            <span>Total Poin Minus</span>
                            <span class="text-slate-400 font-normal">Hanya Angka</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-red-500 font-black text-2xl">-</span>
                            </div>
                            <input type="number" wire:model="poin_minus" class="w-full border-2 border-red-300 bg-red-50 text-red-700 font-black text-3xl rounded-lg py-3 pl-10 pr-4 focus:outline-none focus:border-red-600 focus:ring focus:ring-red-200 transition-all text-center shadow-inner" placeholder="0" required>
                        </div>
                        
                        <div class="mt-3 bg-blue-50 border border-blue-100 rounded-md p-3 flex gap-2 items-start">
                            <span class="text-blue-500">💡</span>
                            <p class="text-xs text-blue-800 leading-relaxed">
                                <span class="font-bold">Info:</span> Angka terisi otomatis. Jika pelanggaran bersifat kelipatan (contoh: -10/orang, -1/detik), silakan <b>kalikan dan ubah angkanya</b> secara manual di atas.
                            </p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Catatan Detail (Opsional)</label>
                        <textarea wire:model="keterangan" class="w-full border-2 border-slate-200 rounded-lg p-3 text-sm focus:border-red-500 transition-colors" placeholder="Contoh: 3 orang melewati batas (3 x 10) = 30 poin..." rows="2"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-black py-4 px-4 rounded-xl shadow-lg shadow-red-500/30 transform hover:-translate-y-0.5 transition-all flex justify-center items-center gap-2 text-lg tracking-wide">
                        <span>➖</span> EKSEKUSI PENALTI
                    </button>
                @else
                    <div class="bg-slate-50 rounded-lg border border-slate-200 border-dashed p-8 text-center flex flex-col items-center justify-center">
                        <span class="text-4xl mb-3 opacity-50">👈</span>
                        <p class="text-slate-500 font-medium text-sm">Silakan pilih peserta di atas terlebih dahulu untuk mulai menginput denda.</p>
                    </div>
                @endif
            </form>
        </div>

        <!-- PANEL KANAN: HISTORY DENDA PESERTA -->
        <div class="md:col-span-2 bg-white rounded-xl shadow border border-slate-200 overflow-hidden self-start">
            <div class="bg-slate-800 p-4 border-b-4 border-slate-900 flex justify-between items-center">
                <h3 class="text-white font-bold uppercase tracking-wider text-sm flex items-center gap-2">
                    <span>📋</span> Riwayat Pelanggaran Peserta
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b">
                        <tr>
                            <th class="p-4 w-12 text-center">No</th>
                            <th class="p-4">Jenis & Catatan Pelanggaran</th>
                            <th class="p-4 text-center text-red-600 w-28">Minus</th>
                            <th class="p-4 text-center w-24">Batal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if($selected_peserta_id)
                            @forelse($denda_list as $index => $denda)
                                <tr class="hover:bg-red-50/50 transition duration-200">
                                    <td class="p-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                    <td class="p-4">
                                        <div class="font-bold text-slate-800 text-sm mb-1">{{ $denda->jenis_pelanggaran }}</div>
                                        @if($denda->keterangan)
                                            <div class="text-xs text-slate-500 flex items-start gap-1">
                                                <span class="opacity-70">📝</span> {{ $denda->keterangan }}
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 italic">- Tidak ada catatan tambahan -</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="bg-red-100 text-red-700 font-black px-3 py-1.5 rounded-lg text-sm border border-red-200 inline-block shadow-sm">
                                            -{{ $denda->poin_minus }}
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button wire:click="hapus({{ $denda->id }})" class="p-2 bg-white border border-slate-200 rounded text-slate-400 hover:text-white hover:bg-red-500 hover:border-red-500 transition-all shadow-sm" title="Hapus Denda">
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-12 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-50 text-green-500 mb-4 text-2xl">
                                            ✨
                                        </div>
                                        <p class="text-slate-500 font-bold">Peserta ini bersih dari pelanggaran!</p>
                                        <p class="text-slate-400 text-sm mt-1">Belum ada denda yang tercatat.</p>
                                    </td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="4" class="p-12 text-center">
                                    <span class="text-4xl opacity-20 mb-4 block">🔍</span>
                                    <p class="text-slate-400 font-medium">Pilih peserta di form sebelah kiri<br>untuk melihat riwayat denda mereka.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>