<div wire:poll.10s>

    {{-- ============================================================
        HEADER
    ============================================================= --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-6">

        <div class="w-full lg:w-1/3">

            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">
                🏆 LEADERBOARD LIVE
            </h1>

            <p class="text-slate-500 text-sm">
                Pantauan hasil perolehan nilai murni secara realtime.
            </p>

        </div>


        <div class="flex flex-col items-start lg:items-end gap-3 w-full lg:w-2/3">

            {{-- ====================================================
                FILTER
            ===================================================== --}}
            <div class="flex flex-wrap items-center gap-2 w-full lg:justify-end">

                {{-- EVENT --}}
                <select
                    wire:model.live="selected_lomba_id"
                    class="border-2 border-slate-300 rounded-lg p-2 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-400 outline-none"
                >

                    @foreach($events as $event)

                        <option value="{{ $event->id }}">
                            {{ $event->nama_lomba }}
                        </option>

                    @endforeach

                </select>


                {{-- TINGKAT --}}
                <select
                    wire:model.live="selected_tingkat"
                    class="border-2 border-blue-400 bg-blue-50 rounded-lg p-2 text-sm font-black text-blue-800 focus:ring-2 focus:ring-blue-400 outline-none"
                >

                    <option value="SD">
                        SD
                    </option>

                    <option value="SMP">
                        SMP
                    </option>

                    <option value="SMA">
                        SMA
                    </option>

                    <option value="UMUM">
                        UMUM
                    </option>

                </select>


                {{-- MODE --}}
                <select
                    wire:model.live="mode_tampilan"
                    class="border-2 border-purple-400 bg-purple-50 rounded-lg p-2 text-sm font-black text-purple-800 focus:ring-2 focus:ring-purple-300 outline-none"
                >

                    <option value="utama">
                        🏆 KLASEMEN UTAMA
                    </option>

                    <option value="umum">
                        👑 KANDIDAT UMUM
                    </option>

                    <optgroup label="PER KATEGORI MURNI">

                        @foreach($semua_kategori as $kat)

                            <option value="{{ $kat->id }}">
                                🔥 BEST {{ $kat->nama_kategori }}
                            </option>

                        @endforeach

                    </optgroup>

                </select>

            </div>


            {{-- ====================================================
                BUTTON EXPORT
            ===================================================== --}}
            @if(auth()->check() && auth()->user()->role === 'admin')

                <div class="flex flex-wrap items-center gap-2 w-full lg:justify-end mt-1">

                    <button
                        onclick="window.open('/cetak-kategori/{{ $selected_lomba_id }}/{{ $selected_tingkat }}', '_blank')"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 px-4 rounded-lg shadow-sm flex items-center gap-1.5 transition"
                    >
                        🏅 Cetak Kategori
                    </button>


                    <button
                        onclick="window.open('/cetak-pengumuman-pdf/{{ $selected_lomba_id }}/{{ $selected_tingkat }}', '_blank')"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 px-4 rounded-lg shadow-sm flex items-center gap-1.5 transition"
                    >
                        📑 Lembar MC
                    </button>


                    <button
                        onclick="window.open('/cetak-pengumuman-excel/{{ $selected_lomba_id }}/{{ $selected_tingkat }}', '_blank')"
                        class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2.5 px-4 rounded-lg shadow-sm flex items-center gap-1.5 transition"
                    >
                        📊 Export Excel
                    </button>

                </div>

            @endif

        </div>

    </div>


    {{-- ============================================================
        TABLE
    ============================================================= --}}
    <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-slate-200">

        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse">

                {{-- =================================================
                    HEADER TABLE
                ================================================== --}}
                <thead class="bg-slate-900 text-white uppercase text-[11px] font-bold tracking-wider">

                    <tr>

                        <th class="p-4 text-center w-16">
                            Rank
                        </th>

                        <th class="p-4 w-16 text-center">
                            No
                        </th>

                        <th class="p-4">
                            Nama Sekolah / Tim
                        </th>

                        <th class="p-4 text-center">
                            WAKTU
                        </th>


                        @foreach($kolom_kategori_tampil as $kat)

                            <th class="p-4 text-center border-l border-slate-700 bg-slate-800">

                                {{ strtoupper($kat->nama_kategori) }}

                            </th>

                        @endforeach


                        {{-- KOLOM TIEBREAKER (TIDAK UNTUK UTAMA) --}}
                        @if($mode_tampilan !== 'utama')
                            @php
                                // Cek apakah kategori yang sedang dilihat adalah PBB
                                $isPbb = false;
                                if (is_numeric($mode_tampilan) && $kategori_pbb) {
                                    $isPbb = ($mode_tampilan == $kategori_pbb->id);
                                }
                            @endphp

                            @if($isPbb)
                                {{-- Jika lihat PBB, tiebreaker adalah KOMANDAN --}}
                                @if($kategori_komandan)
                                    <th class="p-4 text-center text-purple-400 border-l border-slate-700 bg-slate-800 text-xs">
                                        KOMANDAN ⬆️
                                    </th>
                                @endif
                            @else
                                {{-- Jika lihat kategori lain, tiebreaker adalah PBB --}}
                                @if($kategori_pbb)
                                    <th class="p-4 text-center text-green-400 border-l border-slate-700 bg-slate-800 text-xs">
                                        PBB ⬆️
                                    </th>
                                @endif
                            @endif
                        @endif


                        @if(
                            $mode_tampilan === 'utama' ||
                            $mode_tampilan === 'umum'
                        )

                            <th class="p-4 text-center text-red-400 border-l border-slate-700">

                                MINUS

                            </th>

                        @endif


                        <th class="p-4 text-center text-yellow-400 text-base border-l border-slate-700">

                            TOTAL

                        </th>


                        <th class="p-4 text-center border-l border-slate-700 w-20">

                            AKSI

                        </th>

                    </tr>

                </thead>


                {{-- =================================================
                    BODY
                ================================================== --}}
                <tbody class="divide-y divide-gray-100">

                    @forelse($ranking_peserta as $index => $p)

                        @php

                            $rank = $index + 1;

                            $rowClass =
                                $rank === 1
                                    ? 'bg-yellow-50/50'
                                    : (
                                        $rank === 2
                                            ? 'bg-gray-50/50'
                                            : (
                                                $rank === 3
                                                    ? 'bg-orange-50/50'
                                                    : ''
                                            )
                                    );

                            $badgeClass =
                                $rank === 1
                                    ? 'bg-yellow-400 text-yellow-900'
                                    : (
                                        $rank === 2
                                            ? 'bg-slate-300 text-slate-800'
                                            : (
                                                $rank === 3
                                                    ? 'bg-orange-300 text-orange-900'
                                                    : 'bg-slate-100 text-slate-500'
                                            )
                                    );

                        @endphp


                        <tr class="hover:bg-blue-50/50 transition {{ $rowClass }}">

                            {{-- RANK --}}
                            <td class="p-4 text-center">

                                <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center font-black text-sm {{ $badgeClass }}">

                                    {{ $rank }}

                                </div>

                            </td>


                            {{-- NO URUT --}}
                            <td class="p-4 text-center font-bold text-slate-400 text-sm">

                                #{{ $p->no_urut }}

                            </td>


                            {{-- SEKOLAH --}}
                            <td class="p-4">

                                <div class="font-bold text-slate-800 uppercase text-sm">

                                    {{ $p->nama_sekolah }}

                                </div>


                                @if($p->status_tampil === 'selesai')

                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-green-100 text-green-700">

                                        SELESAI

                                    </span>

                                @endif

                            </td>


                            {{-- WAKTU --}}
                            <td class="p-4 text-center">

                                @if($p->durasi_tampil_detik > 0)

                                    <div
                                        class="font-black text-slate-700 bg-slate-100 rounded-md px-2 py-1 inline-block border border-slate-200 shadow-sm text-sm"
                                        title="{{ $p->durasi_tampil_detik }} Detik"
                                    >

                                        ⏱️
                                        {{ $p->durasi_format }}

                                    </div>

                                @else

                                    <span class="text-slate-300 font-bold">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- =================================================
                                NILAI KATEGORI
                            ================================================== --}}
                            @foreach($kolom_kategori_tampil as $kat)

                                @php

                                    $nilai =
                                        (float) (
                                            $p->skor_kategori[
                                                $kat->id
                                            ] ?? 0
                                        );

                                @endphp


                                <td class="p-4 text-center font-bold text-slate-600 border-l border-gray-50 text-sm">

                                    @if($nilai == floor($nilai))

                                        {{ number_format($nilai, 0, ',', '.') }}

                                    @else

                                        {{ number_format($nilai, 2, ',', '.') }}

                                    @endif

                                </td>

                            @endforeach

                            {{-- KOLOM TIEBREAKER (TIDAK UNTUK UTAMA) --}}
                            @if($mode_tampilan !== 'utama')
                                @php
                                    $isPbb = false;
                                    if (is_numeric($mode_tampilan) && $kategori_pbb) {
                                        $isPbb = ($mode_tampilan == $kategori_pbb->id);
                                    }
                                @endphp

                                @if($isPbb)
                                    {{-- Jika lihat PBB, tiebreaker adalah KOMANDAN --}}
                                    @if($kategori_komandan)
                                        <td class="p-4 text-center font-bold text-purple-600 border-l border-gray-50 text-sm">
                                            {{ number_format((float) $p->skor_komandan, 0, ',', '.') }}
                                        </td>
                                    @endif
                                @else
                                    {{-- Jika lihat kategori lain, tiebreaker adalah PBB --}}
                                    @if($kategori_pbb)
                                        <td class="p-4 text-center font-bold text-green-600 border-l border-gray-50 text-sm">
                                            {{ number_format((float) $p->skor_pbb, 0, ',', '.') }}
                                        </td>
                                    @endif
                                @endif
                            @endif


                            {{-- =================================================
                                MINUS
                            ================================================== --}}
                            @if(
                                $mode_tampilan === 'utama' ||
                                $mode_tampilan === 'umum'
                            )

                                <td class="p-4 text-center font-bold text-red-500 border-l border-gray-50 text-sm">

                                    @if((float) $p->total_minus > 0)

                                        -
                                        {{ number_format(
                                            $p->total_minus,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    @else

                                        -

                                    @endif

                                </td>

                            @endif


                            {{-- =================================================
                                TOTAL
                            ================================================== --}}
                            <td class="p-4 text-center border-l border-gray-100 bg-slate-50/30">

                                <span class="text-lg font-black text-slate-900">

                                    @php
                                        $total = (float) $p->total_skor;
                                    @endphp


                                    @if($total == floor($total))

                                        {{ number_format(
                                            $total,
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                    @else

                                        {{ number_format(
                                            $total,
                                            2,
                                            ',',
                                            '.'
                                        ) }}

                                    @endif

                                </span>

                            </td>


                            {{-- =================================================
                                AKSI
                            ================================================== --}}
                            <td class="p-4 text-center border-l border-gray-100">

                                <button
                                    onclick="window.open('/cetak-peserta/{{ $p->id }}', '_blank')"
                                    class="text-slate-400 hover:text-blue-600 transition"
                                    title="Cetak Kartu Nilai"
                                >
                                    🖨️
                                </button>

                            </td>

                        </tr>


                    @empty

                        @php

                            $colspan =
                                count($kolom_kategori_tampil)
                                +
                                (
                                    (
                                        $mode_tampilan === 'utama' ||
                                        $mode_tampilan === 'umum'
                                    )
                                    ? 7
                                    : 6
                                );

                        @endphp


                        <tr>

                            <td
                                colspan="{{ $colspan }}"
                                class="p-10 text-center text-slate-400 italic"
                            >

                                Belum ada data peserta untuk tingkat ini.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- FOOTER --}}
    <div class="mt-4 text-center text-[10px] text-slate-400 mb-10">

        Auto-refresh aktif (10s) • Pandara System v2.0

    </div>

</div>