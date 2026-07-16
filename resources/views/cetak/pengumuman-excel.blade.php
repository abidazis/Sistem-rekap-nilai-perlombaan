<table border="0" style="font-family: Arial, sans-serif; border-collapse: collapse; width: 100%;">
    <tr><td colspan="5" style="text-align: center; font-size: 14pt; font-weight: bold;">PENGUMUMAN JUARA {{ strtoupper($tingkat) }} - {{ strtoupper($lomba->nama_lomba) }}</td></tr>
    <tr><td colspan="5"></td></tr>

    <!-- 1. JUARA SPESIAL / TERSENDIRI -->
    @if(!empty($juaraSpesials))
        @foreach($juaraSpesials as $js)
        <tr><td colspan="5" style="font-weight: bold; font-size: 12pt;">- {{ strtoupper($js['kategori']->nama_kategori) }} -</td></tr>
        <tr>
            <th style="border: 1px solid #000; font-weight: bold; text-align: left;">JUARA</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">NO. URUT</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: left;">NAMA SEKOLAH</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">NILAI</th>
            <th style="border: 1px solid #000;"></th>
        </tr>
        @foreach($js['pemenang'] as $w)
        <tr>
            <td style="border: 1px solid #000;">{{ strtoupper($js['kategori']->nama_kategori) }} {{ $w->rank }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $w->peserta->no_urut ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ strtoupper($w->peserta->nama_sekolah ?? '-') }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $w->nilai ?? $w->keterangan ?? '-' }}</td>
            <td style="border: 1px solid #000;"></td>
        </tr>
        @endforeach
        <tr><td colspan="5"></td></tr>
        @endforeach
    @endif

    <!-- 2. UMUM -->
    @if($pesertaUmum && $pesertaUmum->isNotEmpty())
    <tr><td colspan="5" style="font-weight: bold; font-size: 12pt;">- UMUM -</td></tr>
    <tr>
        <th style="border: 1px solid #000; font-weight: bold; text-align: left;">JUARA</th>
        <th style="border: 1px solid #000; font-weight: bold; text-align: center;">NO. URUT</th>
        <th style="border: 1px solid #000; font-weight: bold; text-align: left;">NAMA SEKOLAH</th>
        <th style="border: 1px solid #000; font-weight: bold; text-align: center;">NILAI</th>
        <th style="border: 1px solid #000;"></th>
    </tr>
    @foreach($pesertaUmum as $idx => $p)
    <tr>
        <td style="border: 1px solid #000;">UMUM {{ $idx + 1 }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $p->no_urut }}</td>
        <td style="border: 1px solid #000;">{{ strtoupper($p->nama_sekolah) }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ number_format($p->skor_umum, 0, ',', '.') }}</td>
        <td style="border: 1px solid #000;"></td>
    </tr>
    @endforeach
    <tr><td colspan="5"></td></tr>
    @endif

    <!-- 3. PERINGKAT (Menampilkan Predikat Juara) -->
    <tr><td colspan="5" style="font-weight: bold; font-size: 12pt;">- PERINGKAT -</td></tr>
    <tr>
        <th style="border: 1px solid #000; font-weight: bold; text-align: left;">PREDIKAT JUARA</th>
        <th style="border: 1px solid #000; font-weight: bold; text-align: center;">NO. URUT</th>
        <th style="border: 1px solid #000; font-weight: bold; text-align: left;">NAMA SEKOLAH</th>
        <th style="border: 1px solid #000; font-weight: bold; text-align: center;">NILAI</th>
        <th style="border: 1px solid #000;"></th>
    </tr>
    @foreach($pesertaGrandTotal->take(12) as $idx => $p)
    <tr>
        <!-- Pemanggilan label Predikat -->
        <td style="border: 1px solid #000; font-weight: bold;">{{ strtoupper($p->predikat_juara) }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ $p->no_urut }}</td>
        <td style="border: 1px solid #000;">{{ strtoupper($p->nama_sekolah) }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ number_format($p->grand_total, 0, ',', '.') }}</td>
        <td style="border: 1px solid #000;"></td>
    </tr>
    @endforeach
    <tr><td colspan="5"></td></tr>

    <!-- 4. KATEGORI MURNI TOP 3 (PBB & KOMANDAN LOGIC) -->
    @foreach($rankingKategori as $bc)
        @php
            $kat = $bc['kategori'];
            $isPBB = stripos($kat->nama_kategori, 'PBB') !== false;
        @endphp
        <tr><td colspan="5" style="font-weight: bold; font-size: 12pt;">- {{ strtoupper($kat->nama_kategori) }} -</td></tr>
        <tr>
            <th style="border: 1px solid #000; font-weight: bold; text-align: left;">JUARA</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">NO. URUT</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: left;">NAMA SEKOLAH</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">NILAI</th>
            @if($isPBB)
                <th style="border: 1px solid #000; font-weight: bold; text-align: center;">KOMANDAN</th>
            @else
                <th style="border: 1px solid #000; font-weight: bold; text-align: center;">PBB</th>
            @endif
        </tr>
        
        @foreach($bc['top3'] as $idx => $p)
        <tr>
            <td style="border: 1px solid #000;">{{ strtoupper($kat->nama_kategori) }} {{ $idx + 1 }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $p->no_urut }}</td>
            <td style="border: 1px solid #000;">{{ strtoupper($p->nama_sekolah) }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ number_format($p->skor_spesifik, 0, ',', '.') }}</td>
            @if($isPBB)
                <td style="border: 1px solid #000; text-align: center;">{{ number_format($p->skor_komandan, 0, ',', '.') }}</td>
            @else
                <td style="border: 1px solid #000; text-align: center;">{{ number_format($p->skor_pbb, 0, ',', '.') }}</td>
            @endif
        </tr>
        @endforeach
        <tr><td colspan="5"></td></tr>
    @endforeach

    <!-- 5. KLASEMEN AKHIR -->
    <tr><td colspan="8" style="page-break-before: always;"></td></tr>
    <tr><td colspan="8" style="text-align: center; font-size: 14pt; font-weight: bold;">REKAP NILAI KESELURUHAN & KLASEMEN AKHIR</td></tr>
    <tr><td colspan="8"></td></tr>

    <tr>
        <th style="border: 1px solid #000; font-weight: bold; text-align: left; background-color:#ffcc00;">PREDIKAT JUARA</th>
        <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color:#ffcc00;">NO. URUT</th>
        <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color:#ffcc00;">NAMA SEKOLAH / TIM</th>
        <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color:#ffcc00;">TOTAL KOTOR</th>
        <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color:#ff9999;">MINUS</th>
        <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color:#ff9999;">KETERANGAN MINUS</th>
        <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color:#99ccff;">WAKTU</th>
        <th style="border: 1px solid #000; font-weight: bold; text-align: center; background-color:#ffcc00;">GRAND TOTAL</th>
    </tr>
    @foreach($pesertaGrandTotal as $idx => $p)
    <tr>
        <!-- Pemanggilan label Predikat -->
        <td style="border: 1px solid #000; text-align: left; font-weight: bold; color: #1e3a8a;">{{ strtoupper($p->predikat_juara) }}</td>
        
        <td style="border: 1px solid #000; text-align: center;">{{ $p->no_urut }}</td>
        <td style="border: 1px solid #000; font-weight: bold;">{{ strtoupper($p->nama_sekolah) }}</td>
        <td style="border: 1px solid #000; text-align: center;">{{ number_format($p->total_kotor, 0, ',', '.') }}</td>
        <td style="border: 1px solid #000; text-align: center; color: red; font-weight: bold;">{{ $p->total_minus > 0 ? '-'.$p->total_minus : '0' }}</td>
        <td style="border: 1px solid #000; font-size: 11px;">{{ $p->keterangan_denda }}</td>
        <td style="border: 1px solid #000; text-align: center; color: blue;">{{ $p->waktu_tampil = $p->durasi_tampil_detik ? gmdate("i:s", $p->durasi_tampil_detik) : '-'; }}</td>
        <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ number_format($p->grand_total, 0, ',', '.') }}</td>
    </tr>
    @endforeach

</table>