<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-red-700 tracking-tight flex items-center gap-2">
                <span>⚠️</span> {{ $is_master_mode ? 'MASTER PEDOMAN DENDA' : 'INPUT PENGURANGAN NILAI' }}
            </h1>
            <p class="text-slate-500 mt-1">
                {{ $is_master_mode ? 'Atur daftar pelanggaran dan poin minus sesuai juklak/juknis event ini.' : 'Catat denda poin akibat pelanggaran pasukan di lapangan sesuai buku pedoman.' }}
            </p>
        </div>
        <button wire:click="toggleMasterMode" class="w-full md:w-auto px-6 py-3 {{ $is_master_mode ? 'bg-slate-700' : 'bg-red-800' }} text-white font-bold rounded-xl shadow-lg hover:scale-105 transition-all flex items-center justify-center gap-2">
            {{ $is_master_mode ? '🔙 Kembali ke Input Denda' : '⚙️ Kelola Pedoman Event' }}
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 font-bold shadow-sm rounded-r-lg flex items-center gap-3">
            <span class="text-xl">✅</span> {{ session('message') }}
        </div>
    @endif

    @if($is_master_mode)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-down">
            <div class="md:col-span-1 bg-white p-6 rounded-xl shadow-lg border border-slate-200 self-start">
                <h3 class="font-bold text-slate-700 mb-4 border-b pb-2">Tambah Aturan Baru</h3>
                <form wire:submit.prevent="simpanMaster">
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Pelanggaran</label>
                        <input type="text" wire:model="nama_pelanggaran" class="w-full border-2 border-slate-200 rounded-lg p-2.5 focus:border-blue-500" placeholder="Contoh: Injak Garis">
                    </div>
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Poin Minus</label>
                        <input type="number" wire:model="poin_master" class="w-full border-2 border-slate-200 rounded-lg p-2.5 focus:border-blue-500" placeholder="Contoh: 10">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-3 rounded-lg shadow transition">
                        ➕ SIMPAN ATURAN
                    </button>
                </form>
            </div>

            <div class="md:col-span-2 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-100 text-slate-600 text-xs uppercase font-black">
                        <tr>
                            <th class="p-4">Daftar Pelanggaran Pedoman</th>
                            <th class="p-4 text-center">Poin</th>
                            <th class="p-4 text-center">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $pedomanList = \App\Models\PedomanDenda::where('lomba_id', $selected_lomba_id)->get(); @endphp
                        @forelse($pedomanList as $p)
                            <tr>
                                <td class="p-4 font-bold text-slate-700">{{ $p->nama_pelanggaran }}</td>
                                <td class="p-4 text-center">
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full font-black">-{{ $p->poin_minus }}</span>
                                </td>
                                <td class="p-4 text-center">
                                    <button wire:click="hapusMaster({{ $p->id }})" class="text-slate-300 hover:text-red-600 transition">🗑️</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-10 text-center text-slate-400 italic">Belum ada pedoman diatur untuk event ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in">
            
            <div class="md:col-span-1 bg-white rounded-xl shadow-lg border border-slate-200 p-6 self-start relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-orange-400"></div>

                <form wire:submit.prevent="simpan">
                    <div class="mb-5">
                        <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Pilih Event Lomba</label>
                        <select wire:model.live="selected_lomba_id" class="w-full border-2 border-slate-200 rounded-lg p-2.5 bg-slate-50 focus:bg-white focus:border-red-500 transition-colors cursor-pointer font-bold">
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->nama_lomba }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Pilih Peserta (Target Denda)</label>
                        <select wire:model.live="selected_peserta_id" class="w-full bg-yellow-50 font-bold border-2 border-yellow-400 text-yellow-900 rounded-lg p-3 focus:border-red-500 transition-all cursor-pointer shadow-sm">
                            <option value="">-- Ketik / Cari Peserta --</option>
                            @foreach($pesertas as $p)
                                <option value="{{ $p->id }}">#{{ $p->no_urut }} - {{ $p->nama_sekolah }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr class="my-6 border-slate-200 border-dashed">

                    @if($selected_peserta_id)
                        <div class="mb-5">
                            <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Kategori Pelanggaran (Dari Pedoman)</label>
                            <select wire:model.live="selected_pedoman_id" class="w-full border-2 border-slate-300 rounded-lg p-3 font-bold text-slate-700 focus:border-red-500 transition-all cursor-pointer shadow-sm" required>
                                <option value="">-- Pilih Sesuai Pedoman --</option>
                                @foreach(\App\Models\PedomanDenda::where('lomba_id', $selected_lomba_id)->get() as $pd)
                                    <option value="{{ $pd->id }}">{{ $pd->nama_pelanggaran }} (-{{ $pd->poin_minus }})</option>
                                @endforeach
                                <option value="manual">Lainnya (Isi Manual)</option>
                            </select>
                        </div>

                        <div class="mb-5">
                            <label class="block text-red-600 text-xs font-black uppercase mb-2 flex justify-between">
                                <span>Total Poin Minus</span>
                                <span class="text-slate-400 font-normal text-[10px]">Hanya Angka</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-red-500 font-black text-2xl">-</span>
                                </div>
                                <input type="number" wire:model="poin_minus" class="w-full border-2 border-red-300 bg-red-50 text-red-700 font-black text-3xl rounded-lg py-3 pl-10 text-center shadow-inner focus:border-red-600" placeholder="0" required>
                            </div>
                            
                            <div class="mt-3 bg-blue-50 border border-blue-100 rounded-md p-3 flex gap-2 items-start">
                                <span class="text-blue-500 text-xs">💡</span>
                                <p class="text-[10px] text-blue-800 leading-tight">
                                    <span class="font-bold">Info:</span> Angka terisi otomatis dari pedoman. Jika pelanggaran bersifat kelipatan (contoh: 3 orang melanggar), silakan <b>kalikan dan ubah angkanya</b> secara manual di atas.
                                </p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Catatan Detail (Opsional)</label>
                            <textarea wire:model="keterangan" class="w-full border-2 border-slate-200 rounded-lg p-3 text-sm focus:border-red-500 transition-colors" placeholder="Contoh: 3 orang memakai atribut tidak sesuai..." rows="2"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-black py-4 rounded-xl shadow-lg transform hover:-translate-y-0.5 transition-all flex justify-center items-center gap-2 text-lg">
                            <span>➖</span> EKSEKUSI PENALTI
                        </button>
                    @else
                        <div class="bg-slate-50 rounded-lg border border-slate-200 border-dashed p-8 text-center flex flex-col items-center justify-center">
                            <span class="text-4xl mb-3 opacity-50">👈</span>
                            <p class="text-slate-500 font-medium text-sm">Pilih peserta di atas terlebih dahulu untuk mulai menginput denda.</p>
                        </div>
                    @endif
                </form>
            </div>

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
                                    <tr class="hover:bg-red-50 transition duration-200">
                                        <td class="p-4 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                                        <td class="p-4">
                                            <div class="font-bold text-slate-800 text-sm mb-1">{{ $denda->jenis_pelanggaran }}</div>
                                            @if($denda->keterangan)
                                                <div class="text-[11px] text-slate-500">📝 {{ $denda->keterangan }}</div>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center">
                                            <div class="bg-red-100 text-red-700 font-black px-3 py-1 rounded-lg text-sm">-{{ $denda->poin_minus }}</div>
                                        </td>
                                        <td class="p-4 text-center">
                                            <button wire:click="hapus({{ $denda->id }})" class="p-2 border rounded hover:bg-red-500 hover:text-white transition">🗑️</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-12 text-center font-bold text-slate-400 italic">✨ Peserta ini bersih dari pelanggaran!</td>
                                    </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td colspan="4" class="p-12 text-center text-slate-400">Pilih peserta untuk melihat riwayat denda mereka.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>