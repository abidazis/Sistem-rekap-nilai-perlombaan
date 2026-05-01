<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-red-700 tracking-tight">⚠️ INPUT PELANGGARAN & DENDA</h1>
            <p class="text-slate-500">Catat pengurangan poin akibat pelanggaran pasukan di lapangan.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 font-bold shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- PANEL KIRI: FORM INPUT -->
        <div class="md:col-span-1 bg-white rounded-xl shadow border border-slate-200 p-6 self-start">
            <form wire:submit.prevent="simpan">
                
                <div class="mb-4">
                    <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Event Lomba</label>
                    <select wire:model.live="selected_lomba_id" class="w-full border-2 border-slate-300 rounded p-2 focus:border-red-500">
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->nama_lomba }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Pilih Peserta</label>
                    <select wire:model.live="selected_peserta_id" class="w-full bg-yellow-50 font-bold border-2 border-yellow-300 rounded p-2 focus:border-red-500">
                        <option value="">-- Cari Peserta --</option>
                        @foreach($pesertas as $p)
                            <option value="{{ $p->id }}">#{{ $p->no_urut }} - {{ $p->nama_sekolah }}</option>
                        @endforeach
                    </select>
                </div>

                <hr class="my-4 border-slate-200">

                @if($selected_peserta_id)
                    <div class="mb-4">
                        <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Jenis Pelanggaran</label>
                        <select wire:model="jenis_pelanggaran" class="w-full border-2 border-slate-300 rounded p-2" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Injak Garis Pembatas">Injak Garis Pembatas</option>
                            <option value="Atribut Jatuh">Atribut Jatuh</option>
                            <option value="Kelebihan / Kekurangan Waktu">Kelebihan / Kekurangan Waktu</option>
                            <option value="Lainnya">Lainnya...</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-red-600 text-xs font-black uppercase mb-2">Poin Minus (Angka Saja)</label>
                        <input type="number" wire:model="poin_minus" class="w-full border-2 border-red-300 bg-red-50 text-red-800 font-black text-xl rounded p-2" placeholder="Contoh: 10" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Catatan (Opsional)</label>
                        <textarea wire:model="keterangan" class="w-full border-2 border-slate-300 rounded p-2 text-sm" placeholder="Contoh: Topi danton jatuh di menit 3..." rows="2"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg">
                        ➖ BERIKAN PENALTI
                    </button>
                @else
                    <div class="text-center p-4 text-slate-400 text-sm">
                        Silahkan pilih peserta terlebih dahulu.
                    </div>
                @endif
            </form>
        </div>

        <!-- PANEL KANAN: HISTORY DENDA PESERTA -->
        <div class="md:col-span-2 bg-white rounded-xl shadow border border-slate-200 overflow-hidden self-start">
            <div class="bg-slate-800 p-4">
                <h3 class="text-white font-bold uppercase tracking-wider text-sm">Daftar Pelanggaran Peserta Terpilih</h3>
            </div>
            <div class="p-0">
                <table class="w-full text-left">
                    <thead class="bg-slate-100 text-slate-500 text-xs uppercase font-bold border-b">
                        <tr>
                            <th class="p-3 w-10">No</th>
                            <th class="p-3">Pelanggaran & Catatan</th>
                            <th class="p-3 text-center text-red-600">Minus</th>
                            <th class="p-3 text-center">Batal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if($selected_peserta_id)
                            @forelse($denda_list as $index => $denda)
                                <tr class="hover:bg-red-50 transition">
                                    <td class="p-3 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                    <td class="p-3">
                                        <div class="font-bold text-slate-700">{{ $denda->jenis_pelanggaran }}</div>
                                        <div class="text-xs text-slate-500">{{ $denda->keterangan ?? '-' }}</div>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="bg-red-100 text-red-700 font-black px-3 py-1 rounded-full text-sm">
                                            -{{ $denda->poin_minus }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <button wire:click="hapus({{ $denda->id }})" class="text-slate-400 hover:text-red-600" title="Hapus Denda">
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-slate-400 font-medium">
                                        ✨ Peserta ini bersih dari pelanggaran.
                                    </td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400">
                                    Pilih peserta di form sebelah kiri untuk melihat riwayat denda.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>