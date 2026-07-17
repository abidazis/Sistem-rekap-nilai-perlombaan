<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Master Data Event</h2>
        @if(!$is_create)
            <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                + Tambah Event
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    @if($is_create)
        <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 mb-6 animate-fade-in-down">
            <h3 class="text-lg font-bold mb-4 text-gray-700 border-b pb-2">
                {{ $is_edit ? 'Edit Event & Pengaturan' : 'Buat Event Baru' }}
            </h3>
            
            <form wire:submit.prevent="{{ $is_edit ? 'update' : 'store' }}">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
                    
                    <div class="space-y-4">
                        <div class="bg-blue-50 p-3 rounded-t-lg border-b-2 border-blue-200 font-bold text-blue-800 uppercase text-sm">Info Dasar Lomba</div>
                        
                        <div>
                            <label class="block text-sm font-bold mb-1 text-gray-600">Nama Lomba / Event</label>
                            <input type="text" wire:model="nama_lomba" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" placeholder="Contoh: LKBB PANDAWA 2026">
                            @error('nama_lomba') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-1 text-gray-600">Tanggal Pelaksanaan</label>
                            <input type="date" wire:model="tanggal_pelaksanaan" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                            @error('tanggal_pelaksanaan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-1 text-gray-600">Lokasi</label>
                            <input type="text" wire:model="lokasi" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Plaza Pemda Bekasi">
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-1 text-gray-600">Durasi Maksimal Tampil (Detik)</label>
                            <input type="number" wire:model="durasi_maksimal_detik" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 bg-yellow-50 font-bold text-lg">
                            <span class="text-xs text-gray-400">Penting: Angka ini akan jadi patokan sistem saat menghitung minus waktu!</span>
                            @error('durasi_maksimal_detik') <span class="text-red-500 text-xs block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-indigo-50 p-3 rounded-t-lg border-b-2 border-indigo-200 font-bold text-indigo-800 uppercase text-sm">Aturan Klasemen & Seri</div>
                        
                        @if($is_edit)

                            <div class="mt-4">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Penentuan Jika Nilai Seri (Tie-Breaker)</label>
                                <div class="flex flex-col gap-3 bg-slate-50 p-3 rounded border border-slate-200">
                                    @if(count($kategoris) > 0)
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-slate-600 w-20">Prioritas 1:</span>
                                            <select wire:model="tie_breakers.0" class="w-full border border-slate-300 rounded p-1 text-sm font-bold">
                                                <option value="">-- Pilih --</option>
                                                @foreach($kategoris as $k)
                                                    <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-slate-600 w-20">Prioritas 2:</span>
                                            <select wire:model="tie_breakers.1" class="w-full border border-slate-300 rounded p-1 text-sm font-bold">
                                                <option value="">-- Pilih --</option>
                                                @foreach($kategoris as $k)
                                                    <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-xs text-slate-600 w-20">Prioritas 3:</span>
                                            <select wire:model="tie_breakers.2" class="w-full border border-slate-300 rounded p-1 text-sm font-bold">
                                                <option value="">-- Pilih --</option>
                                                @foreach($kategoris as $k)
                                                    <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
                                            <div class="space-y-4">
                                                <div class="bg-blue-50 p-3 rounded-t-lg border-b-2 border-blue-200 font-bold text-blue-800 uppercase text-sm">Info Dasar Lomba</div>
                                                
                                                <div>
                                                    <label class="block text-sm font-bold mb-1 text-gray-600">Logo Event (Kanan Kop Surat)</label>
                                                    <input type="file" wire:model="logo" accept="image/*" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                                                    <div wire:loading wire:target="logo" class="text-xs text-blue-500 mt-1">⏳ Mengunggah gambar...</div>
                                                    @if ($logo)
                                                        <img src="{{ $logo->temporaryUrl() }}" class="mt-2 h-16 rounded object-cover">
                                                    @elseif ($logo_lama)
                                                        <img src="{{ asset('storage/'.$logo_lama) }}" class="mt-2 h-16 rounded object-cover">
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="space-y-4">
                                                <div class="bg-indigo-50 p-3 rounded-t-lg border-b-2 border-indigo-200 font-bold text-indigo-800 uppercase text-sm">Aturan Klasemen & Seri</div>
                                                
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Setting Predikat/Urutan Juara</label>
                                                    <textarea wire:model="urutan_juara_teks" rows="6" class="w-full border border-slate-300 rounded p-2 text-sm font-bold text-slate-700 bg-yellow-50 focus:ring-2 focus:ring-indigo-500" placeholder="Juara Utama 1&#10;Juara Utama 2&#10;..."></textarea>
                                                    <span class="text-xs text-gray-400 italic">*Pisahkan setiap urutan juara dengan tombol Enter (Garis Baru). Urutan ini akan langsung tercetak di Berita Acara PDF.</span>
                                                </div>

                                                <div class="mt-4">
                                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Batas Kuota Juara (Trophy)</label>
                                                    <input type="number" wire:model="kuota_juara" class="w-full border border-slate-300 rounded p-2 text-sm font-bold text-slate-700 bg-white focus:ring-2 focus:ring-indigo-500" placeholder="0">
                                                    <span class="text-xs text-blue-500 font-semibold italic">*Isi angka 0 jika event ini ALL TROPHY (Semua dapat gelar juara). Misal diisi 15, maka peringkat 16 dst hanya akan tertulis "Peringkat Ke-16".</span>
                                                </div>
                                                </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-red-500 italic">Isi data Master Kategori dulu agar bisa memilih.</span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="p-6 text-center text-slate-400 bg-slate-50 border border-dashed rounded h-full flex flex-col justify-center">
                                <span class="text-3xl mb-2">🔒</span>
                                <p class="font-bold">Pengaturan Klasemen Dikunci</p>
                                <p class="text-xs">Silakan <b>Simpan Data</b> event ini terlebih dahulu. Setelah tersimpan, klik tombol <b>Edit</b> untuk mengatur Format Juara & Tie-Breaker.</p>
                            </div>
                        @endif
                    </div>
                    
                </div>

                <div class="flex justify-end gap-2 border-t pt-4">
                    <button type="button" wire:click="cancel" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded shadow-lg transition">
                        {{ $is_edit ? '💾 Update Data & Pengaturan' : '💾 Simpan Lomba Baru' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="p-4 border-b">Nama Event</th>
                    <th class="p-4 border-b">Tanggal</th>
                    <th class="p-4 border-b">Lokasi</th>
                    <th class="p-4 border-b text-center">Durasi Max</th>
                    <th class="p-4 border-b text-center">Tie-Breaker</th>
                    <th class="p-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($events as $event)
                <tr class="hover:bg-blue-50 transition">
                    <td class="p-4 font-bold text-gray-800">{{ $event->nama_lomba }}</td>
                    <td class="p-4 text-gray-600">{{ $event->tanggal_pelaksanaan }}</td>
                    <td class="p-4 text-gray-600">{{ $event->lokasi }}</td>
                    <td class="p-4 text-center">
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded border border-yellow-300">
                            {{ gmdate("i:s", $event->durasi_maksimal_detik) }}
                        </span>
                    </td>
                    <td class="p-4 text-center">
                        @if(is_array($event->tie_breakers) && count(array_filter($event->tie_breakers)) > 0)
                            <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">✅ Telah Diset</span>
                        @else
                            <span class="text-xs text-red-500 italic">Belum Diset</span>
                        @endif
                    </td>
                    <td class="p-4 text-center space-x-2 whitespace-nowrap">
                        <button wire:click="edit({{ $event->id }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded border border-blue-200 transition shadow-sm">
                            ⚙️ Edit
                        </button>
                        <button wire:click="delete({{ $event->id }})" wire:confirm="⚠️ Yakin ingin menghapus event ini? Semua data yang terkait mungkin akan ikut terhapus dan tidak dapat dikembalikan!" class="text-red-600 hover:text-red-800 font-bold text-xs bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded border border-red-200 transition shadow-sm">
                            🗑️ Hapus
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-400">Belum ada data event. Silakan tambah baru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>