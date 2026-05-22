<table border="0" style="font-family: Arial, sans-serif; border-collapse: collapse; width: 100%;">
    <tr>
        <th colspan="4" style="background-color: #1e293b; color: #ffffff; font-size: 18px; font-weight: bold; text-align: center; padding: 15px;">
            REKAPITULASI JUARA PER KATEGORI LOMBA
            <br>
            {{ strtoupper($lomba->nama_lomba) }} - TINGKAT {{ strtoupper($tingkat) }} SEDERAJAT
            <br>
            <span style="font-size: 14px;">TANGGAL: {{ \Carbon\Carbon::parse($lomba->tanggal_pelaksanaan)->format('d F Y') }}</span>
        </th>
    </tr>
    <tr><td colspan="4"></td></tr> @foreach($kategoris as $kat)
        <tr>
            <th colspan="4" style="background-color: #3b82f6; color: #ffffff; font-size: 14px; font-weight: bold; text-align: left; border: 1px solid #000; padding: 10px;">
                🏆 JUARA {{ strtoupper($kat->nama_kategori) }}
            </th>
        </tr>
        
        <tr>
            <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000; width: 10%;">RANK</th>
            <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000; width: 15%;">NO. URUT</th>
            <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000; width: 55%;">NAMA SEKOLAH / TIM</th>
            <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000; width: 20%;">NILAI MURNI</th>
        </tr>
        
        @foreach($ranking_per_kategori[$kat->id]->take(25) as $index => $p)
        <tr>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">#{{ $p->no_urut }}</td>
            <td style="border: 1px solid #000; text-align: left; font-weight: bold;">{{ strtoupper($p->nama_sekolah) }}</td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold; color: #1e3a8a;">{{ number_format($p->skor_kategori, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        
        <tr><td colspan="4"></td></tr> @endforeach

</table>