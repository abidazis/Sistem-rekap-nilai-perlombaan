<div>
    <div class="bg-slate-900 p-6 rounded-lg shadow-lg mb-6 border-b-4 border-yellow-500 text-white">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
            
            <div class="lg:col-span-4">
                <label class="block text-yellow-400 text-xs font-bold uppercase mb-2">Pilih Event LKBB:</label>
                <select wire:model.live="selected_lomba_id" class="w-full bg-slate-800 text-white border border-slate-700 rounded-lg p-3 focus:ring-2 focus:ring-yellow-500 font-bold text-sm">
                    <option value="">-- Pilih Event --</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->nama_lomba }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="lg:col-span-6 bg-slate-800 p-4 rounded-xl border border-slate-700">
                <label class="block text-slate-400 text-xs font-bold uppercase mb-2 tracking-wider">📥 Fitur Import Pasukan via CSV</label>
                
                <form wire:submit.prevent="importDataPeserta" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                    <div>
                        <label class="block text-slate-300 text-[11px] mb-1">1. Tingkat</label>
                        <select wire:model.live="tingkat" class="w-full bg-slate-700 text-white text-xs border border-slate-600 rounded-md p-2 font-bold focus:ring-2 focus:ring-blue-500">
                            <option value="SD">SD / MI Sederajat</option>
                            <option value="SMP">SMP / MTs Sederajat</option>
                            <option value="SMA">SMA / SMK / MA Sederajat</option>
                            <option value="UMUM">UMUM / PURNA</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-300 text-[11px] mb-1">2. File Berkas (.csv)</label>
                        <input type="file" wire:model.live="file_import_peserta" accept=".csv" class="w-full text-[11px] text-slate-400 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-3 rounded-md text-xs flex items-center justify-center gap-1 shadow transition">
                            <span wire:loading.remove wire:target="importDataPeserta">📥 Import CSV</span>
                            <span wire:loading wire:target="importDataPeserta">⏳ Memproses...</span>
                        </button>
                    </div>
                </form>

                <div wire:loading wire:target="file_import_peserta" class="text-[11px] text-blue-400 font-bold animate-pulse mt-2">
                    ⏳ Sistem sedang membaca file berkas... Mohon jangan klik import dahulu!
                </div>
            </div>

            <div class="lg:col-span-2 text-right">
                @if($selected_lomba_id && !$is_create)
                    <button wire:click="create" class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg flex items-center justify-center gap-2 transition transform hover:-translate-y-0.5">
                        <span>+ Tambah Manual</span>
                    </button>
                @endif
            </div>

        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 font-bold rounded shadow-sm">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 font-bold rounded shadow-sm">{{ session('error') }}</div>
    @endif

    @if($is_create)
        <div class="bg-white p-6 rounded-lg shadow-lg border-t-4 border-green-600 mb-6 animate-fade-in-down">
            <h3 class="text-xl font-bold mb-6 text-gray-800 border-b pb-2">
                {{ $is_edit ? 'Edit Data Registrasi Peserta' : 'Registrasi Peserta Baru (Manual)' }}
            </h3>
            
            <form wire:submit.prevent="{{ $is_edit ? 'update' : 'store' }}">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold mb-2 text-gray-600">No. Urut</label>
                        <div class="flex items-center">
                            <span class="bg-gray-200 text-gray-600 font-bold p-3 rounded-l border border-r-0 border-gray-300">#</span>
                            <input type="number" wire:model="no_urut" class="w-full border border-gray-300 p-3 rounded-r text-center font-bold text-lg focus:ring-2 focus:ring-blue-500" placeholder="1">
                        </div>
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-bold mb-2 text-gray-600">Nama Sekolah / Pasukan</label>
                        <input type="text" wire:model="nama_sekolah" class="w-full border border-gray-300 p-3 rounded-md font-bold uppercase focus:ring-2 focus:ring-blue-500" placeholder="Contoh: SMAN 1 BEKASI">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-sm font-bold mb-2 text-gray-600">Tingkat Sekolah</label>
                        <select wire:model.live="tingkat" class="w-full border border-gray-300 p-3 rounded-md font-bold text-gray-700 bg-white focus:ring-2 focus:ring-blue-500">
                            <option value="SD">SD / MI Sederajat</option>
                            <option value="SMP">SMP / MTs Sederajat</option>
                            <option value="SMA">SMA / SMK / MA Sederajat</option>
                            <option value="UMUM">UMUM / PURNA PASKIBRAKA</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-3">
                        <label class="block text-sm font-bold mb-2 text-gray-600">Nama Danton (Opsional)</label>
                        <input type="text" wire:model="nama_danton" class="w-full border border-gray-300 p-3 rounded-md uppercase focus:ring-2 focus:ring-blue-500" placeholder="Nama Komandan">
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t pt-4">
                    <button type="button" wire:click="cancel" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-md transition">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-md shadow-lg transition">Simpan Data</button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
        <table class="w-full text-left">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="p-4 border-b text-center w-20">No. Urut</th>
                    <th class="p-4 border-b">Nama Sekolah</th>
                    <th class="p-4 border-b text-center">Tingkat</th> 
                    <th class="p-4 border-b">Nama Danton</th>
                    <th class="p-4 border-b text-center">Status Tampil</th>
                    <th class="p-4 border-b text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @if($selected_lomba_id)
                    @forelse($pesertas as $p)
                    <tr class="hover:bg-blue-50 transition duration-150">
                        <td class="p-4 text-center">
                            <div class="w-10 h-10 mx-auto bg-slate-800 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-sm">
                                {{ $p->no_urut }}
                            </div>
                        </td>
                        <td class="p-4 font-bold text-gray-800 text-lg">{{ $p->nama_sekolah }}</td>
                        
                        <td class="p-4 text-center">
                            <span class="bg-indigo-100 text-indigo-800 border border-indigo-200 px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest shadow-sm">
                                {{ $p->tingkat }}
                            </span>
                        </td>

                        <td class="p-4 text-gray-600">{{ $p->nama_danton ?? '-' }}</td>
                        <td class="p-4 text-center">
                            @if($p->status_tampil == 'selesai')
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">SUDAH TAMPIL</span>
                            @elseif($p->status_tampil == 'tampil')
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold animate-pulse">SEDANG TAMPIL</span>
                            @else
                                <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-xs font-bold">BELUM</span>
                            @endif
                        </td>
                        <td class="p-4 text-center space-x-2">
                            <button wire:click="edit({{ $p->id }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase">Edit</button>
                            <button wire:click="delete({{ $p->id }})" onclick="confirm('Hapus peserta ini?') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center text-gray-400">
                            Belum ada peserta terdaftar.<br>
                            <span class="text-sm">Silakan tambah peserta baru atau jalankan fitur import CSV.</span>
                        </td>
                    </tr>
                    @endforelse
                @else
                    <tr>
                        <td colspan="6" class="p-10 text-center text-gray-400 bg-gray-50">
                            👈 Silakan pilih Event di atas terlebih dahulu untuk memantau klasemen tim.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>