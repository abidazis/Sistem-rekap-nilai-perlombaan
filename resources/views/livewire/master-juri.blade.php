<div>
    <div class="bg-slate-900 p-6 rounded-lg shadow-lg mb-6 border-b-4 border-red-500">
        <div class="flex flex-col md:flex-row justify-between items-end gap-4">
            <div class="w-full md:w-1/2">
                <label class="block text-red-400 text-xs font-bold uppercase mb-2">Pilih Event:</label>
                <select wire:model.live="selected_lomba_id" class="w-full bg-slate-800 text-white border border-slate-700 rounded p-3 focus:ring-2 focus:ring-red-500 font-bold">
                    <option value="">-- Pilih Event --</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->nama_lomba }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-full md:w-auto">
                @if($selected_lomba_id && !$is_create)
                    <button wire:click="create" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-3 px-8 rounded shadow-lg transition transform hover:-translate-y-1">
                        + Registrasi Juri
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 font-bold rounded">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 font-bold rounded">{{ session('error') }}</div>
    @endif

    @if($is_create)
        <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-red-500 mb-6 animate-fade-in-down">
            <h3 class="text-xl font-bold mb-6 text-gray-800 border-b pb-2">{{ $is_edit ? 'Edit Akun Juri' : 'Buat Akun Juri Baru' }}</h3>
            
            <form wire:submit.prevent="{{ $is_edit ? 'update' : 'store' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    
                    <div>
                        <label class="block text-sm font-bold mb-2 text-gray-600">Nama Lengkap Juri</label>
                        <input type="text" wire:model="nama" class="w-full border border-gray-300 p-3 rounded focus:ring-2 focus:ring-red-500" placeholder="Contoh: Budi Santoso">
                        @error('nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2 text-gray-600">Posisi / Jabatan</label>
                        <select wire:model="posisi" class="w-full border border-gray-300 p-3 rounded bg-white">
                            <option value="">-- Pilih Posisi --</option>
                            <option value="Juri PBB 1">Juri PBB 1</option>
                            <option value="Juri PBB 2">Juri PBB 2</option>
                            <option value="Juri PBB 3">Juri PBB 3</option>
                            <option value="Juri Variasi 1">Juri Variasi 1</option>
                            <option value="Juri Variasi 2">Juri Variasi 2</option>
                            <!-- Tambahan opsi Juri Vafor (Variasi Formasi) -->
                            <option value="Juri Vafor 1">Juri Vafor 1</option>
                            <option value="Juri Vafor 2">Juri Vafor 2</option>
                            
                            <option value="Juri Danton">Juri Danton</option>
                            <option value="Juri Kostum">Juri Kostum</option>
                            <option value="Admin Rekap">Admin Rekap (Timer)</option>
                        </select>
                        @error('posisi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2 text-gray-600">Username Login</label>
                        <input type="text" wire:model="username" class="w-full border border-gray-300 p-3 rounded font-mono bg-gray-50" placeholder="juri_pbb">
                        @error('username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2 text-gray-600">Password</label>
                        <input type="text" wire:model="password" class="w-full border border-gray-300 p-3 rounded font-mono bg-gray-50" placeholder="Min. 4 karakter">
                        @if($is_edit) <p class="text-xs text-gray-400 mt-1">*Kosongkan jika tidak ingin mengganti password</p> @endif
                    </div>

                    <!-- CHECKBOX HAK AKSES KATEGORI (SUNTIKAN BARU) -->
                    <div class="col-span-1 md:col-span-2 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <label class="block text-sm font-bold mb-3 text-blue-800 border-b border-blue-200 pb-2">📋 Juri Ini Bertugas Menilai Kategori Apa Saja?</label>
                        <div class="flex flex-wrap gap-4">
                            @if(isset($kategoris) && count($kategoris) > 0)
                                @foreach($kategoris as $kat)
                                    <label class="flex items-center gap-2 cursor-pointer bg-white px-4 py-2 rounded shadow-sm border border-gray-200 hover:border-blue-400 transition">
                                        <input type="checkbox" wire:model="kategori_ids" value="{{ $kat->id }}" class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                        <span class="font-bold text-slate-700 text-sm">{{ $kat->nama_kategori }}</span>
                                    </label>
                                @endforeach
                            @else
                                <span class="text-xs text-red-500 italic font-bold">⚠️ Silakan buat kategori penilaian terlebih dahulu di Master Kategori.</span>
                            @endif
                        </div>
                        @error('kategori_ids') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                </div>

                <div class="flex justify-end gap-3 border-t pt-4">
                    <button type="button" wire:click="cancel" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded transition">Batal</button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-8 rounded shadow-lg transition">Simpan Akun</button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <table class="w-full text-left">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="p-4 border-b">Nama Juri</th>
                    <th class="p-4 border-b">Posisi</th>
                    <!-- Kolom Baru: Tugas Kategori -->
                    <th class="p-4 border-b">Tugas Kategori</th>
                    <th class="p-4 border-b">Username</th>
                    <th class="p-4 border-b text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @if($selected_lomba_id)
                    @forelse($juris as $j)
                    <tr class="hover:bg-red-50 transition">
                        <td class="p-4 font-bold text-gray-800">{{ $j->nama }}</td>
                        <td class="p-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold">{{ $j->posisi }}</span>
                        </td>
                        
                        <!-- RENDER TUGAS KATEGORI DENGAN BADGE KEREN -->
                        <td class="p-4">
                            <div class="flex flex-wrap gap-1.5">
                                @if($j->kategori_ids && is_array($j->kategori_ids) && count($j->kategori_ids) > 0)
                                    @foreach($j->kategori_ids as $kat_id)
                                        @php
                                            // Mencari nama kategori berdasarkan ID yang dicentang
                                            $kategori = collect($kategoris)->firstWhere('id', $kat_id);
                                            $namaKategori = $kategori ? $kategori->nama_kategori : 'Tidak Diketahui';
                                        @endphp
                                        <span class="bg-emerald-100 text-emerald-800 px-2 py-1 rounded text-[10px] font-black uppercase tracking-wide border border-emerald-300 shadow-sm">
                                            {{ $namaKategori }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-xs text-red-500 italic font-bold">⚠️ Belum Ada Tugas</span>
                                @endif
                            </div>
                        </td>

                        <td class="p-4 font-mono text-gray-600">{{ $j->username }}</td>
                        <td class="p-4 text-center space-x-2">
                            <button wire:click="edit({{ $j->id }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase">Edit</button>
                            <button wire:click="delete({{ $j->id }})" onclick="confirm('Hapus akun juri ini?') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-400">
                            Belum ada akun Juri terdaftar.
                        </td>
                    </tr>
                    @endforelse
                @else
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-400 bg-gray-50">
                            👈 Pilih Event di atas untuk mengelola Juri.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>