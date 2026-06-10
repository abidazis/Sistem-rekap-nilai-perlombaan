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
                <select wire:model.live="selected_kategori_id" class="w-full bg-slate-700 border border-slate-600 rounded p-3 focus:ring-2 focus:ring-blue-500 mb-4" {{ !$selected_lomba_id ? 'disabled' : '' }}>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($selected_kategori_id && !$is_create)
            <div class="text-xs text-slate-400 bg-slate-900/50 p-3 rounded-md border border-slate-700">
                💡 <b>Format Dokumentasi Kolom CSV:</b> <span class="font-mono text-yellow-400 font-bold">KATEGORI; NO_URUT; NAMA_GERAKAN; OPSI_NILAI</span>
                <br><span class="text-[11px] text-slate-400">*Sistem akan mencocokkan data otomatis tanpa mengubah setingan bobot / persentase penilaian yang sudah Anda buat.</span>
            </div>
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

                @if($selected_lomba_id)
                    <div class="mt-4 flex justify-end">
                        <button onclick="window.open('/cetak-ljk/{{ $selected_lomba_id }}', '_blank')" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold py-2 px-4 rounded shadow-md flex items-center gap-2 transition">
                            📊 Unduh Lembar Juri (Excel)
                        </button>
                    </div>
                @endif
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
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div class="md:col-span-1">
                        <label class="block text-slate-500 text-xs font-bold uppercase mb-2">No. Urut</label>
                        <input type="number" wire:model="urutan" class="w-full border-2 border-slate-300 rounded p-2 focus:border-blue-500" required>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Nama Gerakan / Item</label>
                        <input type="text" wire:model="nama_gerakan" class="w-full border-2 border-slate-300 rounded p-2 focus:border-blue-500" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-slate-500 text-xs font-bold uppercase mb-2 text-center">Daftar Opsi Nilai (Pisahkan dengan Koma)</label>
                    <div class="bg-blue-50 border-2 border-blue-200 p-4 rounded-xl shadow-sm">
                        <input type="text" wire:model="nilai_ks" class="w-full border-2 border-blue-300 rounded-lg text-lg font-mono font-bold text-blue-800 focus:ring-blue-500 p-3 text-center" placeholder="Contoh: 5, 7, 8, 10, 11, 13, 14, 16" required>
                        <p class="mt-3 text-xs text-blue-600 text-center">
                            💡 <b>Tips:</b> Masukkan seluruh deret angka nilai dari <i>Kurang</i> sampai <i>Sangat Baik</i> ke dalam satu kotak ini. Sistem akan otomatis memecahnya menjadi tombol-tombol di aplikasi Juri.
                        </p>
                    </div>
                    
                    <!-- Hidden input agar backend kita tidak error -->
                    <div class="hidden">
                        <input type="text" wire:model="nilai_k">
                        <input type="text" wire:model="nilai_c">
                        <input type="text" wire:model="nilai_b">
                        <input type="text" wire:model="nilai_sb">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" wire:click="cancel" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2 px-6 rounded shadow-sm">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-6 rounded shadow-lg flex items-center gap-2">
                        <span>{{ $is_edit ? '💾 Update Item' : '💾 Simpan Item' }}</span>
                    </button>
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