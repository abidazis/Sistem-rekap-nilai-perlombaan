<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th colspan="{{ 6 + count($kategoris) }}" style="text-align: center; font-size: 16px; font-weight: bold;">
                    ARSIP REKAPITULASI LENGKAP - {{ strtoupper($lomba->nama_lomba) }}
                </th>
            </tr>
            <tr>
                <th style="background-color: #facc15; font-weight: bold; text-align: center;">RANK</th>
                <th style="background-color: #facc15; font-weight: bold; text-align: center;">NO URUT</th>
                <th style="background-color: #facc15; font-weight: bold;">NAMA SEKOLAH</th>
                
                <!-- Looping Kolom Kategori Otomatis -->
                @foreach($kategoris as $kat)
                    <th style="background-color: #bae6fd; font-weight: bold; text-align: center;">{{ strtoupper($kat->nama_kategori) }}</th>
                @endforeach
                
                <th style="background-color: #fecaca; font-weight: bold; text-align: center;">TOTAL KOTOR</th>
                <th style="background-color: #fca5a5; font-weight: bold; text-align: center;">MINUS/DENDA</th>
                <th style="background-color: #86efac; font-weight: bold; text-align: center; font-size: 14px;">GRAND TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesertas as $index => $p)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                    <td style="text-align: center;">{{ $p->no_urut }}</td>
                    <td>{{ strtoupper($p->nama_sekolah) }}</td>
                    
                    <!-- Looping Nilai per Kategori -->
                    @foreach($kategoris as $kat)
                        <td style="text-align: center;">{{ $p->skor_kategori[$kat->id] }}</td>
                    @endforeach
                    
                    <td style="text-align: center;">{{ $p->total_kotor }}</td>
                    <td style="text-align: center; color: red;">-{{ $p->total_minus }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $p->grand_total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>