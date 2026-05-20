<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <table border="1" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">
        <thead>
            <tr>
                <th colspan="{{ 7 + count($kategoris) }}" style="background-color: #1e293b; color: #ffffff; font-weight: bold; font-size: 16px; text-align: center; padding: 10px;">
                    KLASEMEN JUARA & REKAPITULASI LENGKAP - {{ strtoupper($lomba->nama_lomba) }} (TINGKAT {{ strtoupper($tingkat) }})
                </th>
            </tr>
            
            <tr>
                <th style="background-color: #22c55e; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle;">PREDIKAT JUARA</th>
                <th style="background-color: #facc15; color: #000000; font-weight: bold; text-align: center; vertical-align: middle;">RANK</th>
                <th style="background-color: #facc15; color: #000000; font-weight: bold; text-align: center; vertical-align: middle;">NO URUT</th>
                <th style="background-color: #facc15; color: #000000; font-weight: bold; text-align: center; vertical-align: middle;">NAMA SEKOLAH</th>
                
                @foreach($kategoris as $kat)
                    @php 
                        $isTie = in_array($kat->id, $tieBreakers); 
                        // Jika Tie Breaker warna kuning, jika bukan warna biru muda
                        $bgColor = $isTie ? '#facc15' : '#bae6fd';
                    @endphp
                    <th style="background-color: {{ $bgColor }}; color: #000000; font-weight: bold; text-align: center; vertical-align: middle;">
                        {{ strtoupper($kat->nama_kategori) }} {!! $isTie ? '<br>(TIE-BREAKER)' : '' !!}
                    </th>
                @endforeach
                
                <th style="background-color: #fecaca; color: #000000; font-weight: bold; text-align: center; vertical-align: middle;">TOTAL KOTOR</th>
                <th style="background-color: #fca5a5; color: #000000; font-weight: bold; text-align: center; vertical-align: middle;">MINUS/DENDA</th>
                <th style="background-color: #86efac; color: #000000; font-weight: bold; text-align: center; vertical-align: middle;">GRAND TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesertas as $index => $p)
                <tr>
                    <td style="font-weight: bold; text-align: left;">{{ $p->predikat_juara }}</td>
                    <td style="font-weight: bold; text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: center;">{{ $p->no_urut }}</td>
                    <td style="font-weight: bold;">{{ strtoupper($p->nama_sekolah) }}</td>
                    
                    @foreach($kategoris as $kat)
                        <td style="text-align: center;">{{ $p->skor_kategori[$kat->id] }}</td>
                    @endforeach
                    
                    <td style="text-align: center;">{{ $p->total_kotor }}</td>
                    <td style="text-align: center; color: red; font-weight: bold;">{{ $p->total_minus == 0 ? '0' : '-'.$p->total_minus }}</td>
                    <td style="text-align: center; font-weight: bold; font-size: 14px;">{{ $p->grand_total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>