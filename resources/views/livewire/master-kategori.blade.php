<div>
    <div class="bg-slate-800 p-6 rounded-lg shadow-lg mb-6 border border-slate-700">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="w-full md:w-1/2">
                <label class="block text-slate-300 text-sm font-bold mb-2">Pilih Event / Lomba:</label>
                <select wire:model.live="selected_lomba_id" class="w-full bg-slate-700 text-white border border-slate-600 rounded p-3 focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Event --</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->nama_lomba }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-full md:w-auto">
                @if($selected_lomba_id && !$is_create)
                    <button wire:click="create" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-6 rounded shadow-lg transition transform hover:scale-105">
                        + Kategori Baru
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">{{ session('error') }}</div>
    @endif

    @if($is_create)
        <div class="bg-white p-6 rounded-lg shadow-lg border-t-4 border-blue-600 mb-6 animate-fade-in-down">
            <h3 class="text-lg font-bold mb-4 text-gray-700">{{ $is_edit ? 'Edit Kategori' : 'Tambah Kategori Penilaian' }}</h3>
            <form wire:submit.prevent="{{ $is_edit ? 'update' : 'store' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-600">Nama Kategori</label>
                        <input type="text" wire:model="nama_kategori" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" placeholder="Contoh: PBB MURNI, VARIASH, DANTON">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1 text-gray-600">Bobot Nilai (%)</label>
                        <input type="number" wire:model="bobot_persen" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" placeholder="Contoh: 70">
                        <span class="text-xs text-gray-400">Total bobot sebaiknya 100%</span>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="cancel" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">Batal</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">Simpan</button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-100 text-slate-600 uppercase text-xs font-bold">
                <tr>
                    <th class="p-4 border-b">Nama Kategori</th>
                    <th class="p-4 border-b text-center">Bobot (%)</th>
                    <th class="p-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @if($selected_lomba_id)
                    @forelse($kategoris as $k)
                    <tr class="hover:bg-blue-50 transition">
                        <td class="p-4 font-bold text-gray-800">{{ $k->nama_kategori }}</td>
                        <td class="p-4 text-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">
                                {{ $k->bobot_persen }}%
                            </span>
                        </td>
                        <td class="p-4 text-center space-x-2">
                            <button wire:click="edit({{ $k->id }})" class="text-blue-600 font-bold hover:underline">Edit</button>
                            <button wire:click="delete({{ $k->id }})" onclick="confirm('Hapus kategori ini beserta semua item penilaian di dalamnya?') || event.stopImmediatePropagation()" class="text-red-600 font-bold hover:underline">Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-8 text-center text-gray-400">
                            Belum ada kategori untuk event ini.<br>
                            <span class="text-sm">Silakan klik tombol "+ Kategori Baru" di atas.</span>
                        </td>
                    </tr>
                    @endforelse
                @else
                    <tr>
                        <td colspan="3" class="p-8 text-center text-gray-400">
                            👈 Silakan pilih Event di atas terlebih dahulu.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>