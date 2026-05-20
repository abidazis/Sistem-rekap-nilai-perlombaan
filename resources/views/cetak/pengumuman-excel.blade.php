<table border="0" style="font-family: Arial, sans-serif; border-collapse: collapse; width: 100%;">
    <tr>
        <th colspan="4" style="background-color: #1e293b; color: #ffffff; font-size: 18px; font-weight: bold; text-align: center; padding: 15px;">
            PENGUMUMAN JUARA {{ strtoupper($lomba->nama_lomba) }} - TINGKAT {{ strtoupper($tingkat) }}
            <br>
            <span style="font-size: 14px;">TANGGAL: {{ \Carbon\Carbon::parse($lomba->tanggal_pelaksanaan)->format('d F Y') }}</span>
        </th>
    </tr>
    <tr><td colspan="4"></td></tr> <tr>
        <th colspan="4" style="background-color: #22c55e; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; border: 1px solid #000; padding: 8px;">
            🏆 DAFTAR KLASEMEN JUARA (UTAMA, HARAPAN, MADYA, BINA, DLL)
        </th>
    </tr>
    <tr>
        <th style="background-color: #facc15; font-weight: bold; text-align: center; border: 1px solid #000;">PREDIKAT JUARA</th>
        <th style="background-color: #facc15; font-weight: bold; text-align: center; border: 1px solid #000;">NO. URUT</th>
        <th style="background-color: #facc15; font-weight: bold; text-align: center; border: 1px solid #000;">NAMA SEKOLAH / TIM</th>
        <th style="background-color: #facc15; font-weight: bold; text-align: center; border: 1px solid #000;">GRAND TOTAL</th>
    </tr>
    @foreach($pesertaGrandTotal as $p)
        <tr>
            <td style="border: 1px solid #000; font-weight: bold; text-align: left;">{{ $p->predikat_juara }}</td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">#{{ $p->no_urut }}</td>
            <td style="border: 1px solid #000; font-weight: bold;">{{ strtoupper($p->nama_sekolah) }}</td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold; color: #b91c1c;">{{ number_format($p->grand_total, 0, ',', '.') }}</td>
        </tr>
    @endforeach
    <tr><td colspan="4"></td></tr> <tr>
        <th colspan="4" style="background-color: #3b82f6; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; border: 1px solid #000; padding: 8px;">
            👑 KANDIDAT JUARA UMUM (AKUMULASI KATEGORI)
        </th>
    </tr>
    <tr>
        <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000;">RANK</th>
        <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000;">NO. URUT</th>
        <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000;">NAMA SEKOLAH / TIM</th>
        <th style="background-color: #bfdbfe; font-weight: bold; text-align: center; border: 1px solid #000;">TOTAL POIN UMUM</th>
    </tr>
    @foreach($pesertaUmum as $idx => $p)
        <tr>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">#{{ $p->no_urut }}</td>
            <td style="border: 1px solid #000; font-weight: bold;">{{ strtoupper($p->nama_sekolah) }}</td>
            <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ number_format($p->skor_umum, 0, ',', '.') }}</td>
        </tr>
    @endforeach
    <tr><td colspan="4"></td></tr> <tr>
        <th colspan="4" style="background-color: #9333ea; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; border: 1px solid #000; padding: 8px;">
            🌟 JUARA TERBAIK PER KATEGORI (TOP 3)
        </th>
    </tr>
    @foreach($rankingKategori as $nama_kat => $pesertas)
        <tr>
            <th colspan="4" style="background-color: #f3e8ff; text-align: left; font-weight: bold; font-size: 14px; border: 1px solid #000; padding-top: 10px;">
                ► TERBAIK {{ strtoupper($nama_kat) }}
            </th>
        </tr>
        <tr>
            <th style="background-color: #e9d5ff; font-weight: bold; text-align: center; border: 1px solid #000;">RANK</th>
            <th style="background-color: #e9d5ff; font-weight: bold; text-align: center; border: 1px solid #000;">NO. URUT</th>
            <th style="background-color: #e9d5ff; font-weight: bold; text-align: center; border: 1px solid #000;">NAMA SEKOLAH / TIM</th>
            <th style="background-color: #e9d5ff; font-weight: bold; text-align: center; border: 1px solid #000;">NILAI MURNI</th>
        </tr>
        @foreach($pesertas as $idx => $p)
            <tr>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">JUARA {{ $idx + 1 }}</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">#{{ $p->no_urut }}</td>
                <td style="border: 1px solid #000; font-weight: bold;">{{ strtoupper($p->nama_sekolah) }}</td>
                <td style="border: 1px solid #000; text-align: center; font-weight: bold;">{{ number_format($p->skor_spesifik, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr><td colspan="4"></td></tr> @endforeach

</table>