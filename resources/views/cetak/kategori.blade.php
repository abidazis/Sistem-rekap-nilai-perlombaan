<table border="0" style="font-family: Arial, sans-serif; border-collapse: collapse; width: 100%;">
    <tr>
        <th colspan="5" style="background-color: #1e293b; color: #ffffff; font-size: 18px; font-weight: bold; text-align: center; padding: 15px;">
            REKAPITULASI JUARA PER KATEGORI LOMBA
            <br>
            {{ strtoupper($lomba->nama_lomba) }} - TINGKAT {{ strtoupper($tingkat) }} SEDERAJAT
            <br>
            <span style="font-size: 14px;">TANGGAL: {{ \Carbon\Carbon::parse($lomba->tanggal_pelaksanaan)->format('d F Y') }}</span>
        </th>
    </tr>
    <tr><td colspan="5" style="padding: 5px;"></td></tr> 
    
    @foreach($kategoris as $kat)
        <tr>
            <th colspan="5" style="background-color: #3b82f6; color: #ffffff; font-size: 14px; font-weight: bold; text-align: left; border: 1px solid #000; padding: 10px;">
                🏆 JUARA {{ strtoupper($kat->nama_kategori) }}
            </th>
        </tr>
        
        <tr>
            <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000; width: 8%; padding: 5px;">RANK</th>
            <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000; width: 12%; padding: 5px;">NO. URUT</th>
            <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000; width: 45%; padding: 5px;">NAMA SEKOLAH / TIM</th>
            <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000; width: 17%; padding: 5px;">NILAI MURNI</th>
            <th style="background-color: #fef3c7; color: #b45309; font-weight: bold; text-align: center; border: 1px solid #000; width: 18%; padding: 5px;">NILAI PBB<br><span style="font-size: 9px;">(TIE-BREAKER)</span></th>
        </tr>
        
        @foreach($ranking_per_kategori[$kat->id]->take(40) as $index => $p)
        <tr>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 5px;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold; padding: 5px;">#{{ $p->no_urut }}</td>
            <td style="border: 1px solid #000; text-align: left; font-weight: bold; padding: 5px; padding-left: 8px;">{{ strtoupper($p->nama_sekolah) }}</td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold; color: #1e3a8a; padding: 5px; font-size: 13px;">
                {{ number_format($p->skor_kategori, 0, ',', '.') }}
            </td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold; color: #b45309; background-color: #fffbeb; padding: 5px; font-size: 13px;">
                {{ number_format($p->skor_pbb ?? 0, 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
        
        <tr><td colspan="5" style="padding: 10px;"></td></tr> 
    @endforeach

</table>