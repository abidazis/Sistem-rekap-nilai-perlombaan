<div>
    <div class="bg-slate-800 p-6 rounded-lg shadow-lg mb-6 border border-slate-700 text-white">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div>
                <label class="block text-slate-400 text-xs font-bold uppercase mb-2">Langkah 1: Pilih Event</label>
                <select wire:model.live="selected_lomba_id" class="w-full bg-slate-700 border border-slate-600 rounded p-3 focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Event --</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->nama_lomba }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-400 text-xs font-bold uppercase mb-2">Langkah 2: Pilih Kategori</label>
                <select wire:model.live="selected_kategori_id" class="w-full bg-slate-700 border border-slate-600 rounded p-3 focus:ring-2 focus:ring-blue-500" {{ !$selected_lomba_id ? 'disabled' : '' }}>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($selected_kategori_id && !$is_create)
            <div class="mt-6 flex flex-col md:flex-row justify-between items-center border-t border-slate-700 pt-4 gap-4">
                
                <!-- FORM IMPORT CSV BARU -->
                <form wire:submit.prevent="importData" class="flex flex-wrap items-center gap-2 bg-slate-700 p-2 rounded-lg w-full md:w-auto">
                    <input type="file" wire:model.live="file_import" accept=".csv" class="text-sm text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-green-100 file:text-green-800 hover:file:bg-green-200 cursor-pointer">
                    
                    <!-- TAMBAHKAN KODE INI -->
                    <div wire:loading wire:target="file_import" class="text-xs text-blue-400 font-bold animate-pulse mt-1 w-full">
                        ⏳ Sedang membaca file... Jangan klik Import dulu!
                    </div>
                    <button type="submit" class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded shadow-lg text-sm flex items-center gap-2">
                        <span wire:loading.remove wire:target="importData">📥 Import CSV</span>
                        <span wire:loading wire:target="importData">⏳ Loading...</span>
                    </button>
                    
                    @error('file_import') <span class="text-red-400 text-xs font-bold w-full">{{ $message }}</span> @enderror
                </form>

                <!-- TOMBOL TAMBAH MANUAL -->
                <button wire:click="create" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-6 rounded shadow-lg flex items-center justify-center gap-2 w-full md:w-auto">
                    <span>+ Tambah Manual</span>
                </button>
            </div>
        @endif
        
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 font-bold">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 font-bold">{{ session('error') }}</div>
    @endif

    @if($is_create)
        <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-blue-600 mb-6 animate-fade-in-down">
            <h3 class="text-lg font-bold mb-4 text-gray-800">{{ $is_edit ? 'Edit Item' : 'Input Item Baru' }}</h3>
            
            <form wire:submit.prevent="{{ $is_edit ? 'update' : 'store' }}">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold mb-1 text-gray-600">No. Urut</label>
                        <input type="number" wire:model="urutan" class="w-full border-2 border-gray-300 p-2 rounded-lg text-center font-bold focus:border-blue-500 focus:ring-0">
                    </div>

                    <div class="md:col-span-10">
                        <label class="block text-sm font-bold mb-1 text-gray-600">Nama Gerakan / Item</label>
                        <input type="text" wire:model="nama_gerakan" class="w-full border-2 border-gray-300 p-2 rounded-lg font-bold text-gray-800 uppercase focus:border-blue-500 focus:ring-0" placeholder="Contoh: HORMAT KANAN">
                    </div>

                    <div class="md:col-span-12 mt-2">
                        <label class="block text-sm font-bold mb-3 text-gray-600 text-center border-b-2 border-dashed pb-2">Opsi Nilai (Sesuai Kolom Lembar Juri)</label>
                        
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                            <div class="bg-red-50 border-2 border-red-200 p-3 rounded-xl text-center shadow-sm">
                                <label class="block text-xs font-black text-red-600 mb-2">KS (Kurang Sekali)</label>
                                <input type="text" wire:model="nilai_ks" class="w-full border-red-300 rounded text-center font-mono font-bold text-red-700 focus:ring-red-500" placeholder="Cth: 16">
                            </div>
                            
                            <div class="bg-orange-50 border-2 border-orange-200 p-3 rounded-xl text-center shadow-sm">
                                <label class="block text-xs font-black text-orange-600 mb-2">K (Kurang)</label>
                                <input type="text" wire:model="nilai_k" class="w-full border-orange-300 rounded text-center font-mono font-bold text-orange-700 focus:ring-orange-500" placeholder="Cth: 18">
                            </div>
                            
                            <div class="bg-yellow-50 border-2 border-yellow-200 p-3 rounded-xl text-center shadow-sm">
                                <label class="block text-xs font-black text-yellow-600 mb-2">C (Cukup)</label>
                                <input type="text" wire:model="nilai_c" class="w-full border-yellow-300 rounded text-center font-mono font-bold text-yellow-700 focus:ring-yellow-500" placeholder="Cth: 20, 22">
                            </div>
                            
                            <div class="bg-green-50 border-2 border-green-200 p-3 rounded-xl text-center shadow-sm">
                                <label class="block text-xs font-black text-green-600 mb-2">B (Baik)</label>
                                <input type="text" wire:model="nilai_b" class="w-full border-green-300 rounded text-center font-mono font-bold text-green-700 focus:ring-green-500" placeholder="Cth: 24, 26">
                            </div>
                            
                            <div class="bg-blue-50 border-2 border-blue-200 p-3 rounded-xl text-center shadow-sm">
                                <label class="block text-xs font-black text-blue-600 mb-2">SB (Sangat Baik)</label>
                                <input type="text" wire:model="nilai_sb" class="w-full border-blue-300 rounded text-center font-mono font-bold text-blue-700 focus:ring-blue-500" placeholder="Cth: 28, 30">
                            </div>
                        </div>
                        
                        <p class="text-xs text-gray-500 mt-3 text-center">💡 <b>Tips:</b> Jika di PDF ada dua/tiga nilai dalam satu kolom (seperti C, B, SB), pisahkan dengan koma. Contoh: <code>20, 22</code></p>
                    </div>

                </div>

                <div class="flex justify-end gap-3 border-t-2 border-gray-100 pt-5">
                    <button type="button" wire:click="cancel" class="text-gray-500 hover:text-gray-800 font-bold px-4 transition">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-2 px-8 rounded-lg shadow-lg transform transition hover:scale-105">💾 Simpan Item</button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <table class="w-full text-left">
            <thead class="bg-slate-100 text-slate-600 uppercase text-xs font-bold">
                <tr>
                    <th class="p-3 border-b text-center w-16">No</th>
                    <th class="p-3 border-b">Nama Item / Gerakan</th>
                    <th class="p-3 border-b">Opsi Nilai (Dropdown Juri)</th>
                    <th class="p-3 border-b text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @if($selected_kategori_id)
                    @forelse($items as $item)
                    <tr class="hover:bg-blue-50 transition">
                        <td class="p-3 text-center font-bold text-gray-500">{{ $item->urutan }}</td>
                        <td class="p-3 font-bold text-gray-800">{{ $item->nama_gerakan }}</td>
                        <td class="p-3">
                            <div class="flex flex-wrap gap-1">
                                @if(is_array($item->opsi_nilai))
                                    @foreach($item->opsi_nilai as $val)
                                        <span class="bg-gray-100 border border-gray-300 text-gray-600 px-2 py-0.5 rounded text-xs font-mono font-bold">
                                            {{ $val }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td class="p-3 text-center space-x-2">
                            <button wire:click="edit({{ $item->id }})" class="text-blue-600 hover:text-blue-800 text-xs font-bold uppercase">Edit</button>
                            <button wire:click="delete({{ $item->id }})" class="text-red-600 hover:text-red-800 text-xs font-bold uppercase" onclick="confirm('Hapus item ini?') || event.stopImmediatePropagation()">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400">
                            Belum ada item penilaian di kategori ini.
                        </td>
                    </tr>
                    @endforelse
                @else
                    <tr>
                        <td colspan="4" class="p-10 text-center text-gray-400 bg-gray-50">
                            👆 Pilih <b>Kategori</b> di atas untuk melihat/mengelola format nilai.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>