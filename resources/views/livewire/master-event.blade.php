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
                {{ $is_edit ? 'Edit Event' : 'Buat Event Baru' }}
            </h3>
            
            <form wire:submit.prevent="{{ $is_edit ? 'update' : 'store' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-600">Nama Lomba / Event</label>
                        <input type="text" wire:model="nama_lomba" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" placeholder="Contoh: LKBB PANDAWA 2026">
                        @error('nama_lomba') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-slate-500 text-xs font-bold uppercase mb-2">Tanggal Pelaksanaan</label>
                        <input type="date" wire:model="tanggal_pelaksanaan" class="w-full border-2 border-slate-200 rounded-lg p-3 font-bold text-slate-700 focus:border-blue-500 transition-colors">
                        @error('tanggal_pelaksanaan') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-600">Lokasi</label>
                        <input type="text" wire:model="lokasi" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Plaza Pemda Bekasi">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-600">Durasi Tampil (Detik)</label>
                        <input type="number" wire:model="durasi_maksimal_detik" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="text-xs text-gray-400">Default: 600 detik (10 menit)</span>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="cancel" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                        {{ $is_edit ? 'Update Data' : 'Simpan Data' }}
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
                    <th class="p-4 border-b text-center">Durasi</th>
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
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded">
                            {{ $event->durasi_maksimal_detik }} Detik
                        </span>
                    </td>
                    <td class="p-4 text-center space-x-2">
                        <button wire:click="edit({{ $event->id }})" class="text-blue-600 hover:text-blue-800 font-bold text-sm">Edit</button>
                        <button wire:click="delete({{ $event->id }})" 
                                onclick="confirm('Yakin mau hapus event ini?') || event.stopImmediatePropagation()"
                                class="text-red-600 hover:text-red-800 font-bold text-sm">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-400">Belum ada data event. Silakan tambah baru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>