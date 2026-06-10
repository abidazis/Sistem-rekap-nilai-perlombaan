<div>
    <div class="mb-6">
        <h2 class="text-2xl font-black text-slate-800">🌟 Input Juara Spesial (Tersendiri)</h2>
        <p class="text-sm text-slate-500">Input manual untuk Suporter Terbaik, Pasukan Favorit, dll.</p>
    </div>

    <div class="bg-white p-5 rounded-lg shadow-md border-t-4 border-purple-600 mb-6 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-slate-500 mb-1">Pilih Event/Lomba</label>
            <select wire:model.live="selected_lomba_id" class="w-full border-2 border-slate-300 rounded p-2 font-bold text-slate-700 focus:ring-2 focus:ring-purple-400">
                <option value="">-- Pilih Lomba --</option>
                @foreach($events as $e) <option value="{{ $e->id }}">{{ $e->nama_lomba }}</option> @endforeach
            </select>
        </div>
        
        <div class="w-32">
            <label class="block text-xs font-bold text-slate-500 mb-1">Tingkat</label>
            <select wire:model.live="selected_tingkat" class="w-full border-2 border-slate-300 rounded p-2 font-bold text-slate-700">
                <option value="SD">SD</option>
                <option value="SMP">SMP</option>
                <option value="SMA">SMA</option>
                <option value="UMUM">UMUM</option>
            </select>
        </div>
        
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-slate-500 mb-1">Kategori Spesial</label>
            <select wire:model.live="selected_kategori_id" class="w-full border-2 border-purple-300 bg-purple-50 rounded p-2 font-black text-purple-800 focus:ring-2 focus:ring-purple-400">
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategoris as $k) <option value="{{ $k->id }}">🌟 {{ $k->nama_kategori }}</option> @endforeach
            </select>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow font-bold animate-fade-in-down">
            {{ session('message') }}
        </div>
    @endif

    @if($selected_kategori_id)
        <div class="bg-white p-6 rounded-lg shadow-lg border border-slate-200 animate-fade-in-down">
            <h3 class="text-lg font-black text-slate-700 mb-4 border-b pb-2">Tentukan Pemenang & Total Poin</h3>
            <form wire:submit.prevent="simpanJuara">
                
                <div class="space-y-4 mb-6">
                    <div class="flex flex-col sm:flex-row items-center gap-4 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <div class="w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center text-white font-black text-xl shadow shrink-0">1</div>
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-yellow-800 mb-1">Pilih Sekolah</label>
                            <select wire:model="juara_1" class="w-full border border-yellow-300 rounded p-2 font-bold text-slate-700">
                                <option value="">-- Pilih Sekolah --</option>
                                @foreach($pesertas as $p) <option value="{{ $p->id }}">#{{ $p->no_urut }} - {{ $p->nama_sekolah }}</option> @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-32">
                            <label class="block text-xs font-bold text-yellow-800 mb-1">Total Nilai</label>
                            <input type="number" wire:model="nilai_1" class="w-full border border-yellow-300 rounded p-2 font-bold text-slate-700 text-center" placeholder="Poin">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-4 bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <div class="w-12 h-12 bg-slate-300 rounded-full flex items-center justify-center text-slate-600 font-black text-xl shadow shrink-0">2</div>
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-slate-600 mb-1">Pilih Sekolah</label>
                            <select wire:model="juara_2" class="w-full border border-slate-300 rounded p-2 font-bold text-slate-700">
                                <option value="">-- Pilih Sekolah --</option>
                                @foreach($pesertas as $p) <option value="{{ $p->id }}">#{{ $p->no_urut }} - {{ $p->nama_sekolah }}</option> @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-32">
                            <label class="block text-xs font-bold text-slate-600 mb-1">Total Nilai</label>
                            <input type="number" wire:model="nilai_2" class="w-full border border-slate-300 rounded p-2 font-bold text-slate-700 text-center" placeholder="Poin">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-4 bg-orange-50 p-4 rounded-lg border border-orange-200">
                        <div class="w-12 h-12 bg-orange-400 rounded-full flex items-center justify-center text-white font-black text-xl shadow shrink-0">3</div>
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-orange-800 mb-1">Pilih Sekolah</label>
                            <select wire:model="juara_3" class="w-full border border-orange-300 rounded p-2 font-bold text-slate-700">
                                <option value="">-- Pilih Sekolah --</option>
                                @foreach($pesertas as $p) <option value="{{ $p->id }}">#{{ $p->no_urut }} - {{ $p->nama_sekolah }}</option> @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-32">
                            <label class="block text-xs font-bold text-orange-800 mb-1">Total Nilai</label>
                            <input type="number" wire:model="nilai_3" class="w-full border border-orange-300 rounded p-2 font-bold text-slate-700 text-center" placeholder="Poin">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition">
                        💾 Simpan Pemenang & Nilai
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="p-10 text-center bg-slate-50 rounded-lg border-2 border-dashed border-slate-200">
            <span class="text-4xl">👆</span>
            <p class="mt-4 font-bold text-slate-500">Pilih Kategori Spesial di atas untuk menginput pemenang.</p>
        </div>
    @endif
</div>