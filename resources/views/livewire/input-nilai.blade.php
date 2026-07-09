<div> 
    <div wire:loading class="fixed top-0 left-0 w-full h-1 bg-yellow-400 animate-pulse z-50 shadow-lg"></div>

    <div class="bg-slate-900 p-6 rounded-lg shadow-lg mb-8 border-b-4 border-yellow-500 sticky top-0 z-40">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            
            <div>
                <label class="block text-slate-400 text-xs font-bold uppercase mb-1">Event:</label>
                {{-- @if($is_juri_locked)
                    <select class="w-full bg-slate-800 text-white border border-slate-700 rounded p-2 text-sm font-bold opacity-60 cursor-not-allowed" disabled>
                        @foreach($events as $event)
                            @if($selected_lomba_id == $event->id)
                                <option value="{{ $event->id }}" selected>{{ $event->nama_lomba }}</option>
                            @endif
                        @endforeach
                    </select>
                @else --}}
                    <select wire:model.live="selected_lomba_id" class="w-full bg-slate-800 text-white border border-slate-700 rounded p-2 text-sm font-bold focus:ring-2 focus:ring-blue-500">
                        <option value="">-- PILIH EVENT --</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->nama_lomba }}</option>
                        @endforeach
                    </select>
                {{-- @endif --}}
            </div>

            <div>
                <label class="block text-blue-400 text-xs font-bold uppercase mb-1">Tingkat Sekolah:</label>
                <select wire:model.live="selected_tingkat" class="w-full bg-blue-900 text-white border border-blue-700 rounded p-2 font-bold focus:ring-2 focus:ring-blue-500">
                    <option value="SD">SD / MI Sederajat</option>
                    <option value="SMP">SMP / MTs Sederajat</option>
                    <option value="SMA">SMA / SMK / MA Sederajat</option>
                    <option value="UMUM">UMUM / PURNA PASKIBRAKA</option>
                </select>
            </div>

            <div>
                <label class="block text-green-400 text-xs font-bold uppercase mb-1">Kategori Nilai:</label>
                <select wire:model.live="selected_kategori_id" class="w-full bg-green-900 text-white border border-green-700 rounded p-2 font-bold focus:ring-2 focus:ring-green-500">
                    <option value="">-- PILIH KATEGORI --</option>
                    @if(isset($all_kategoris))
                        @foreach($all_kategoris as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div>
                <label class="block text-red-400 text-xs font-bold uppercase mb-1">Input Sbg Juri:</label>
                @if($is_juri_locked)
                    <span class="text-[10px] text-red-300 italic mt-1 block">*Akun dikunci untuk nama juri ini.</span>
                    <select class="w-full bg-red-900 text-white border border-red-700 rounded p-2 font-bold opacity-60 cursor-not-allowed" disabled>
                        <option value="{{ $selected_juri_id }}" selected>{{ $nama_juri_locked }}</option>
                    </select>
                @else
                    <select wire:model.live="selected_juri_id" class="w-full bg-red-900 text-white border border-red-700 rounded p-2 font-bold focus:ring-2 focus:ring-red-500">
                        <option value="">-- PILIH JURI --</option>
                        @foreach($juris as $j)
                            <option value="{{ $j->id }}">{{ $j->nama }} ({{ $j->posisi }})</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div>
                <label class="block text-yellow-400 text-xs font-bold uppercase mb-1">Peserta Tampil:</label>
                <select wire:model.live="selected_peserta_id" class="w-full bg-yellow-400 text-slate-900 border-2 border-yellow-500 rounded p-2 text-lg font-bold shadow-inner focus:ring-4 focus:ring-blue-500">
                    <option value="">-- PILIH PESERTA --</option>
                    @foreach($pesertas as $p)
                        <option value="{{ $p->id }}">
                            #{{ $p->no_urut }} - {{ $p->nama_sekolah }} 
                        </option>
                    @endforeach
                </select>
            </div>
            
        </div>
    </div>

    @if (session()->has('message'))
        <div class="fixed top-24 left-1/2 transform -translate-x-1/2 z-50 bg-green-500 text-white px-8 py-4 rounded-full shadow-2xl border-4 border-white font-black text-lg flex items-center gap-3 animate-bounce">
            <span>✅</span> {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="fixed top-24 left-1/2 transform -translate-x-1/2 z-50 bg-red-600 text-white px-8 py-4 rounded-xl shadow-2xl border-4 border-white font-black text-sm flex items-center gap-3 animate-pulse max-w-2xl w-full">
            <span>⚠️</span> {{ session('error') }}
        </div>
    @endif

    @if(!empty($selected_peserta_id) && !empty($selected_juri_id))
        
        @php
            $juriTerpilih = collect($juris)->firstWhere('id', $selected_juri_id);
            $posisiJuri = $juriTerpilih ? strtolower($juriTerpilih->posisi) : '';
            $isTimer = str_contains($posisiJuri, 'admin') || str_contains($posisiJuri, 'timer');
        @endphp

        <form wire:submit.prevent="simpan">

            @if($isTimer)
                <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl shadow-xl border border-yellow-500 overflow-hidden mb-8 p-6 md:p-8 animate-fade-in-down">
                    <div class="flex items-center justify-between mb-6 border-b border-slate-700 pb-4">
                        <h3 class="text-yellow-400 font-black text-2xl tracking-widest uppercase">⏱️ INPUT WAKTU TAMPIL</h3>
                    </div>
                    <div class="flex flex-col md:flex-row gap-6 items-center justify-center">
                        <div class="text-center">
                            <label class="block text-slate-400 text-sm font-bold uppercase mb-2">Menit</label>
                            <input type="number" wire:model="menit_tampil" min="0" class="w-32 bg-slate-900 text-white text-6xl font-black text-center rounded-xl border-2 border-slate-600 focus:border-yellow-500 py-6">
                        </div>
                        <div class="text-6xl font-black text-slate-500 pb-2 md:block hidden">:</div>
                        <div class="text-center">
                            <label class="block text-slate-400 text-sm font-bold uppercase mb-2">Detik</label>
                            <input type="number" wire:model="detik_tampil" min="0" max="59" class="w-32 bg-slate-900 text-white text-6xl font-black text-center rounded-xl border-2 border-slate-600 focus:border-yellow-500 py-6">
                        </div>
                    </div>
                </div>
            @endif

            @if(!empty($selected_kategori_id))
                <div class="grid grid-cols-1 gap-8 animate-fade-in-down">
                    @foreach($struktur_penilaian as $kategori)
                        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-800 to-blue-600 px-6 py-4 flex justify-between items-center">
                                <h3 class="text-white font-black text-lg tracking-widest uppercase">{{ $kategori->nama_kategori }}</h3>
                            </div>

                            <div class="overflow-x-auto w-full">
                                <table class="w-full text-left border-collapse">
                                    <tbody>
                                        @if(count($kategori->daftar_item) == 0)
                                            <tr>
                                                <td colspan="3" class="p-8 text-center bg-red-50 text-red-600 font-bold border-b border-red-200">
                                                    <span class="text-3xl block mb-2">⚠️</span>
                                                    Oops! Belum ada rincian Item Penilaian di Kategori {{ $kategori->nama_kategori }}.<br>
                                                    <span class="text-sm font-normal text-slate-500">Silakan ke menu Master Format Nilai lalu Import CSV untuk kategori ini.</span>
                                                </td>
                                            </tr>
                                        @endif

                                        @foreach($kategori->daftar_item as $item)
                                            <tr wire:key="item-{{ $item->id }}" class="border-b border-gray-200 hover:bg-blue-50 transition-colors duration-200">
                                                <td class="py-4 px-4 w-12 text-center text-gray-400 font-bold bg-gray-50 border-r border-gray-200">
                                                    #{{ $item->urutan }}
                                                </td>
                                                <td class="py-4 px-6 font-bold text-gray-800 uppercase w-1/3 md:w-1/2">
                                                    {{ $item->nama_gerakan }}
                                                </td>
                                                <td class="py-3 px-4 w-2/3">
                                                    <div class="flex flex-nowrap items-center gap-1.5 md:gap-2 overflow-x-auto pb-2 scrollbar-hide">
                                                        
                                                        @php
                                                            $raw_opsi = $item->opsi_nilai;
                                                            $opsi = is_string($raw_opsi) ? json_decode($raw_opsi, true) : $raw_opsi;
                                                            if (!is_array($opsi)) $opsi = [];
                                                            if (!in_array('0', $opsi) && !in_array(0, $opsi)) {
                                                                array_unshift($opsi, '0');
                                                            }
                                                        @endphp

                                                        @foreach($opsi as $val)
                                                            @php
                                                                $isZero = ($val == '0' || $val == 0);
                                                                $bgChecked = $isZero ? 'peer-checked:bg-red-600 peer-checked:border-red-600' : 'peer-checked:bg-blue-600 peer-checked:border-blue-600';
                                                                $textHover = $isZero ? 'hover:border-red-400 hover:bg-red-100 text-red-600 border-red-300 bg-red-50' : 'hover:border-blue-400 hover:bg-gray-100 text-gray-600 border-gray-300';
                                                            @endphp

                                                            <label class="cursor-pointer m-0 relative flex-shrink-0">
                                                                <input type="radio" wire:model="inputs.{{ $item->id }}" value="{{ $val }}" class="peer sr-only">
                                                                <div class="w-10 h-10 md:w-11 md:h-11 flex items-center justify-center border-2 rounded-lg font-bold text-base md:text-lg {{ $bgChecked }} peer-checked:text-white peer-checked:shadow-inner transition-all duration-200 ease-in-out {{ $textHover }}">
                                                                    {{ $val }}
                                                                </div>
                                                            </label>
                                                        @endforeach

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif(!$isTimer)
                <div class="text-center py-16 bg-blue-50 rounded-xl border-2 border-dashed border-blue-300 mt-4">
                    <div class="text-5xl mb-3">📁</div>
                    <h2 class="text-xl font-black text-blue-700 mb-1">PILIH KATEGORI PENILAIAN</h2>
                    <p class="text-blue-500 font-medium">Silakan pilih <b>Kategori Nilai</b> di panel atas agar format tombol nilai muncul.</p>
                </div>
            @endif

            @if($isTimer || !empty($selected_kategori_id))
                <div class="fixed bottom-0 left-0 w-full bg-white/95 backdrop-blur border-t border-gray-300 p-4 shadow-[0_-10px_20px_rgba(0,0,0,0.1)] z-50">
                    <div class="container mx-auto flex justify-between items-center max-w-7xl">
                        <div class="text-sm text-gray-600 hidden md:flex flex-col">
                            <span class="font-bold text-blue-900 text-lg uppercase">Pastikan semua nilai terisi!</span>
                            <span>Klik kotak nilai untuk memilih. Jika terlewat, <b class="text-red-600">pilih angka 0</b>.</span>
                        </div>
                        <button type="submit" wire:loading.attr="disabled" class="w-full md:w-1/3 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-black text-lg py-4 px-10 rounded-xl shadow-xl transform transition hover:scale-105 border-b-4 border-green-800">
                            <span wire:loading.remove wire:target="simpan">💾 SIMPAN NILAI</span>
                            <span wire:loading wire:target="simpan">⏳ Menyimpan...</span>
                        </button>
                    </div>
                </div>
                <div class="h-32"></div> 
            @endif

        </form>
    @else
        <div class="text-center py-20 bg-white rounded-xl shadow-sm border-2 border-dashed border-gray-300">
            <div class="text-6xl mb-4">👮‍♂️📝</div>
            <h2 class="text-2xl font-black text-gray-800 mb-2">Sistem Siap Digunakan!</h2>
            <p class="text-gray-500 text-lg font-medium">
                Pilih <b>Tingkat, Kategori, Juri, dan Peserta</b> pada panel atas untuk mulai menilai.
            </p>
        </div>
    @endif
</div>