<div> 
    <div class="bg-slate-900 p-6 rounded-lg shadow-lg mb-8 border-b-4 border-yellow-500 sticky top-0 z-40">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            
            <div>
                <label class="block text-slate-400 text-xs font-bold uppercase mb-1">Event:</label>
                <select wire:model.live="selected_lomba_id" class="w-full bg-slate-800 text-white border border-slate-700 rounded p-2 text-sm">
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->nama_lomba }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-red-400 text-xs font-bold uppercase mb-1">Input Sebagai Juri:</label>
                <select wire:model.live="selected_juri_id" class="w-full bg-red-900 text-white border border-red-700 rounded p-2 font-bold focus:ring-2 focus:ring-red-500">
                    <option value="">-- PILIH JURI --</option>
                    @foreach($juris as $j)
                        <option value="{{ $j->id }}">{{ $j->nama }} ({{ $j->posisi }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-yellow-400 text-xs font-bold uppercase mb-1">Peserta Tampil:</label>
                <select wire:model.live="selected_peserta_id" class="w-full bg-yellow-400 text-slate-900 border-2 border-yellow-500 rounded p-2 text-lg font-bold shadow-inner focus:ring-4 focus:ring-blue-500">
                    <option value="">-- PILIH PESERTA --</option>
                    @foreach($pesertas as $p)
                        <option value="{{ $p->id }}">
                            NO. {{ $p->no_urut }} - {{ $p->nama_sekolah }} 
                        </option>
                    @endforeach
                </select>
            </div>
            
        </div>
    </div>

    <!-- NOTIFIKASI POP-UP SMART (FLOATING TOAST) -->
    @if (session()->has('message'))
        <div class="fixed top-24 left-1/2 transform -translate-x-1/2 z-50 bg-green-500 text-white px-8 py-4 rounded-full shadow-2xl border-4 border-white font-black text-lg flex items-center gap-3 animate-bounce">
            <span>✅</span> {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-24 left-1/2 transform -translate-x-1/2 z-50 bg-red-500 text-white px-8 py-4 rounded-full shadow-2xl border-4 border-white font-black text-lg flex items-center gap-3 animate-pulse">
            <span>⚠️</span> {{ session('error') }}
        </div>
    @endif

    @if($selected_peserta_id && $selected_juri_id)
        <form wire:submit.prevent="simpan">
            
            <div class="grid grid-cols-1 gap-8">
                @foreach($struktur_penilaian as $kategori)
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                        
                        <div class="bg-gradient-to-r from-blue-800 to-blue-600 px-6 py-4 flex justify-between items-center">
                            <h3 class="text-white font-black text-lg tracking-widest uppercase">{{ $kategori->nama_kategori }}</h3>
                        </div>

                        <div class="overflow-x-auto w-full">
                            <table class="w-full text-left border-collapse">
                                <tbody>
                                    @foreach($kategori->items as $item)
                                        <tr class="border-b border-gray-200 hover:bg-blue-50 transition-colors duration-200">
                                            
                                            <td class="py-4 px-4 w-12 text-center text-gray-400 font-bold bg-gray-50 border-r border-gray-200">
                                                #{{ $item->urutan }}
                                            </td>
                                            
                                            <td class="py-4 px-6 font-bold text-gray-800 uppercase w-1/3 md:w-1/2">
                                                {{ $item->nama_gerakan }}
                                            </td>

                                            <td class="py-4 px-4">
                                                <div class="flex flex-wrap items-center gap-2 md:gap-3">
                                                    @php
                                                        $opsi = $item->opsi_nilai ?? [];
                                                        if(!in_array('0', $opsi) && !in_array(0, $opsi)) {
                                                            array_unshift($opsi, '0');
                                                        }
                                                    @endphp

                                                    @if(count($opsi) > 0)
                                                        @foreach($opsi as $val)
                                                            @php
                                                                $isZero = ($val == '0' || $val == 0);
                                                                $bgChecked = $isZero ? 'peer-checked:bg-red-600 peer-checked:border-red-600' : 'peer-checked:bg-blue-600 peer-checked:border-blue-600';
                                                                $textHover = $isZero ? 'hover:border-red-400 hover:bg-red-100 text-red-600 border-red-300 bg-red-50' : 'hover:border-blue-400 hover:bg-gray-100 text-gray-600 border-gray-300';
                                                            @endphp

                                                            <label class="cursor-pointer m-0 relative">
                                                                <!-- DIHILANGKAN REQUIRED BIAR BISA SIMPAN WALAU BELUM SELESAI -->
                                                                <input 
                                                                    type="radio" 
                                                                    wire:model="inputs.{{ $item->id }}" 
                                                                    value="{{ $val }}" 
                                                                    class="peer sr-only" 
                                                                >
                                                                
                                                                <div class="w-12 h-12 md:w-14 md:h-14 flex items-center justify-center border-2 rounded-lg font-bold text-lg {{ $bgChecked }} peer-checked:text-white peer-checked:shadow-inner transition-all duration-200 ease-in-out {{ $textHover }}">
                                                                    {{ $val }}
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    @else
                                                        <span class="text-red-500 text-sm italic font-semibold py-2 px-4 bg-red-100 rounded">
                                                            ⚠️ Opsi nilai belum diatur
                                                        </span>
                                                    @endif
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

            <!-- TOMBOL SIMPAN YANG BENAR (CUMA ADA 1 SEKARANG) -->
            <div class="fixed bottom-0 left-0 w-full bg-white/95 backdrop-blur border-t border-gray-300 p-4 shadow-[0_-10px_20px_rgba(0,0,0,0.1)] z-50">
                <div class="container mx-auto flex justify-between items-center max-w-7xl">
                    <div class="text-sm text-gray-600 hidden md:flex flex-col">
                        <span class="font-bold text-blue-900 text-lg uppercase">Pastikan semua nilai terisi!</span>
                        <span>Klik kotak nilai untuk memilih. Jika gerakan terlewat, <b class="text-red-600">pilih angka 0</b>.</span>
                    </div>
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full md:w-1/3 bg-gradient-to-r from-green-600 to-green-500 hover:from-green-700 hover:to-green-600 text-white font-black text-lg py-4 px-10 rounded-xl shadow-xl transform transition hover:scale-105 border-b-4 border-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 flex justify-center items-center gap-2"
                    >
                        <span wire:loading.remove wire:target="simpan">💾 SIMPAN NILAI</span>
                        <span wire:loading wire:target="simpan">⏳ Menyimpan...</span>
                    </button>
                </div>
            </div>
            
            <div class="h-32"></div> 

        </form>
    @else
        <div class="text-center py-20 bg-white rounded-xl shadow-sm border-2 border-dashed border-gray-300">
            <div class="text-6xl mb-4">👮‍♂️📝</div>
            <h2 class="text-2xl font-black text-gray-800 mb-2">Sistem Siap Digunakan!</h2>
            <p class="text-gray-500 text-lg font-medium">
                Silakan pilih <span class="text-red-600 font-bold">Juri</span> dan <span class="text-yellow-600 font-bold">Peserta</span> pada panel di atas untuk mulai menilai.
            </p>
        </div>
    @endif

</div>