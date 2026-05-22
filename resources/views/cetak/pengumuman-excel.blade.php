<table border="0" style="font-family: Arial, sans-serif; border-collapse: collapse; width: 100%;">
    <tr>
        <th colspan="5" style="background-color: #1e293b; color: #ffffff; font-size: 18px; font-weight: bold; text-align: center; padding: 15px;">
            PENGUMUMAN JUARA {{ strtoupper($lomba->nama_lomba) }} - TINGKAT {{ strtoupper($tingkat) }}
            <br>
            <span style="font-size: 14px;">TANGGAL: {{ \Carbon\Carbon::parse($lomba->tanggal_pelaksanaan)->format('d F Y') }}</span>
        </th>
    </tr>
    <tr><td colspan="5"></td></tr> 
    
    <tr>
        <th colspan="5" style="background-color: #22c55e; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; border: 1px solid #000; padding: 8px;">
            🏆 DAFTAR KLASEMEN JUARA BERJENJANG
        </th>
    </tr>
    <tr>
        <th style="background-color: #facc15; font-weight: bold; text-align: center; border: 1px solid #000;">PREDIKAT JUARA</th>
        <th style="background-color: #facc15; font-weight: bold; text-align: center; border: 1px solid #000;">NO. URUT</th>
        <th style="background-color: #facc15; font-weight: bold; text-align: center; border: 1px solid #000;">NAMA SEKOLAH / TIM</th>
        <th style="background-color: #facc15; font-weight: bold; text-align: center; border: 1px solid #000;">GRAND TOTAL</th>
        @if(count($tb_kategoris) > 0)
            <th style="background-color: #fef08a; font-weight: bold; text-align: center; border: 1px solid #000;">{{ strtoupper($tb_kategoris->first()->nama_kategori) }} (TIE-BREAKER)</th>
        @else
            <th style="background-color: #facc15; font-weight: bold; text-align: center; border: 1px solid #000;">KETERANGAN</th>
        @endif
    </tr>
    @foreach($ranked as $idx => $p)
        @php
            $urutan = $idx + 1;
            if($urutan <= 3) $label = "UTAMA $urutan";
            elseif($urutan <= 6) $label = "HARAPAN " . ($urutan-3);
            elseif($urutan <= 9) $label = "MADYA " . ($urutan-6);
            elseif($urutan <= 12) $label = "BINA " . ($urutan-9);
            elseif($urutan <= 15) $label = "MULA " . ($urutan-12);
            elseif($urutan <= 18) $label = "PURWA " . ($urutan-15);
            elseif($urutan <= 21) $label = "CARAKA " . ($urutan-18);
            elseif($urutan <= 24) $label = "PERINTIS " . ($urutan-21);
            elseif($urutan <= 27) $label = "POTENSIAL " . ($urutan-24);
            else $label = "PESERTA " . ($urutan-27);
        @endphp
        <tr>
            <td style="border: 1px solid #000; font-weight: bold; text-align: left;">{{ $label }}</td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">#{{ $p->no_urut }}</td>
            <td style="border: 1px solid #000; font-weight: bold;">{{ strtoupper($p->nama_sekolah) }}</td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold; color: #b91c1c;">{{ number_format($p->grand_total, 0, ',', '.') }}</td>
            
            @if(count($tb_kategoris) > 0)
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ number_format($p->skor_kategori[$tb_kategoris->first()->id] ?? 0, 0, ',', '.') }}</td>
            @else
                <td style="border: 1px solid #000;"></td>
            @endif
        </tr>
    @endforeach
    
    <tr><td colspan="5"></td></tr> 
    
    <tr>
        <th colspan="5" style="background-color: #3b82f6; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; border: 1px solid #000; padding: 8px;">
            👑 KANDIDAT JUARA UMUM (AKUMULASI KATEGORI)
        </th>
    </tr>
    <tr>
        <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000;">RANK</th>
        <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000;">NO. URUT</th>
        <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000;">NAMA SEKOLAH / TIM</th>
        <th colspan="2" style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000;">TOTAL POIN UMUM</th>
    </tr>
    @foreach($juaraUmum as $idx => $p)
        <tr>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">JUARA UMUM {{ $idx + 1 }}</td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">#{{ $p->no_urut }}</td>
            <td style="border: 1px solid #000; font-weight: bold;">{{ strtoupper($p->nama_sekolah) }}</td>
            <td colspan="2" style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ number_format($p->skor_akhir_umum, 0, ',', '.') }}</td>
        </tr>
    @endforeach
    
    <tr><td colspan="5"></td></tr> 
    
    <tr>
        <th colspan="5" style="background-color: #9333ea; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; border: 1px solid #000; padding: 8px;">
            🌟 JUARA TERBAIK PER KATEGORI (TOP 3)
        </th>
    </tr>
    @foreach($bestCategories as $bc)
        @php
            $kat = $bc['kategori'];
            $p_list = $bc['pesertas'];
            if(count($p_list) == 0) continue;
            
            $tb_kat = $tb_kategoris->firstWhere('id', '!=', $kat->id);
            if (!$tb_kat && count($tb_kategoris) > 0) $tb_kat = $tb_kategoris->first();
        @endphp
        <tr>
            <th colspan="5" style="background-color: #f3e8ff; text-align: left; font-weight: bold; font-size: 14px; border: 1px solid #000; padding-top: 10px;">
                ► TERBAIK {{ strtoupper($kat->nama_kategori) }}
            </th>
        </tr>
        <tr>
            <th style="background-color: #e9d5ff; font-weight: bold; text-align: center; border: 1px solid #000;">RANK</th>
            <th style="background-color: #e9d5ff; font-weight: bold; text-align: center; border: 1px solid #000;">NO. URUT</th>
            <th style="background-color: #e9d5ff; font-weight: bold; text-align: center; border: 1px solid #000;">NAMA SEKOLAH / TIM</th>
            <th style="background-color: #e9d5ff; font-weight: bold; text-align: center; border: 1px solid #000;">NILAI MURNI</th>
            @if($tb_kat)
                <th style="background-color: #fef08a; font-weight: bold; text-align: center; border: 1px solid #000;">{{ strtoupper($tb_kat->nama_kategori) }} (TIE)</th>
            @else
                <th style="background-color: #e9d5ff; border: 1px solid #000;"></th>
            @endif
        </tr>
        @foreach($p_list as $idx => $p)
            <tr>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">RANK {{ $idx + 1 }}</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">#{{ $p->no_urut }}</td>
                <td style="border: 1px solid #000; font-weight: bold;">{{ strtoupper($p->nama_sekolah) }}</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ number_format($p->skor_spesifik, 0, ',', '.') }}</td>
                
                @if($tb_kat)
                    <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ number_format($p->skor_kategori[$tb_kat->id] ?? 0, 0, ',', '.') }}</td>
                @else
                    <td style="border: 1px solid #000;"></td>
                @endif
            </tr>
        @endforeach
        <tr><td colspan="5"></td></tr> 
    @endforeach

    @if(count($juaraSpesials) > 0)
        <tr><td colspan="5"></td></tr> 
        <tr>
            <th colspan="5" style="background-color: #db2777; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; border: 1px solid #000; padding: 8px;">
                🌟 JUARA KATEGORI SPESIAL / TERSENDIRI
            </th>
        </tr>
        @foreach($juaraSpesials as $js)
            <tr>
                <th colspan="5" style="background-color: #fce7f3; text-align: left; font-weight: bold; font-size: 14px; border: 1px solid #000; padding-top: 10px;">
                    ► JUARA {{ strtoupper($js['kategori']->nama_kategori) }}
                </th>
            </tr>
            <tr>
                <th style="background-color: #fbcfe8; font-weight: bold; text-align: center; border: 1px solid #000;">RANK</th>
                <th style="background-color: #fbcfe8; font-weight: bold; text-align: center; border: 1px solid #000;">NO. URUT</th>
                <th style="background-color: #fbcfe8; font-weight: bold; text-align: center; border: 1px solid #000;">NAMA SEKOLAH / TIM</th>
                <th colspan="2" style="background-color: #fbcfe8; font-weight: bold; text-align: center; border: 1px solid #000;">KETERANGAN</th>
            </tr>
            @foreach($js['pemenang'] as $w)
                <tr>
                    <td style="border: 1px solid #000; text-align: center; font-weight: bold;">RANK {{ $w->rank }}</td>
                    <td style="border: 1px solid #000; text-align: center; font-weight: bold;">#{{ $w->peserta->no_urut ?? '-' }}</td>
                    <td style="border: 1px solid #000; font-weight: bold;">{{ strtoupper($w->peserta->nama_sekolah ?? '-') }}</td>
                    <td colspan="2" style="border: 1px solid #000; text-align: center; font-weight: bold; color: #9d174d;">JUARA {{ $w->rank }}</td>
                </tr>
            @endforeach
        @endforeach
    @endif
</table>