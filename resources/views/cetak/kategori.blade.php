<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Juara Kategori - {{ $lomba->nama_lomba }} ({{ strtoupper($tingkat) }})</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #000; }
        .kop { text-align: center; font-weight: bold; margin-bottom: 25px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .kop h2 { margin: 2px 0; font-size: 16px; }
        .kop h3 { margin: 2px 0; font-size: 13px; font-weight: normal; }
        
        /* Layout Grid agar muat 2 tabel bersebelahan */
        .grid-container { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 15px; }
        .kategori-box { width: 48%; margin-bottom: 20px; break-inside: avoid; }
        
        .kategori-title { 
            font-weight: bold; 
            font-size: 13px; 
            margin-bottom: 0; 
            text-align: center; 
            background-color: #f8fafc; 
            padding: 8px; 
            border: 1px solid #000; 
            border-bottom: none;
            text-transform: uppercase;
        }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background-color: #e2e8f0; font-weight: bold; font-size: 10px; }
        .text-left { text-align: left; padding-left: 8px; }
        .bold { font-weight: bold; }

        @media print {
            @page { size: A4 portrait; margin: 15mm; }
            button { display: none; }
            th { background-color: #e2e8f0 !important; -webkit-print-color-adjust: exact; }
            .kategori-title { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="kop">
        <h2>REKAPITULASI JUARA PER KATEGORI LOMBA</h2>
        <h2>{{ strtoupper($lomba->nama_lomba) }}</h2>
        <h3>TINGKAT {{ strtoupper($tingkat) }} SEDERAJAT</h3>
        <h3>{{ \Carbon\Carbon::parse($lomba->tanggal_pelaksanaan)->format('d F Y') }}</h3>
    </div>

    <div class="grid-container">
        @foreach($kategoris as $kat)
            <div class="kategori-box">
                <div class="kategori-title">🏆 JUARA {{ $kat->nama_kategori }}</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 12%">RANK</th>
                            <th style="width: 15%">NO. URUT</th>
                            <th class="text-left" style="width: 53%">NAMA SEKOLAH</th>
                            <th style="width: 20%">NILAI MURNI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ranking_per_kategori[$kat->id]->take(25) as $index => $p)
                        <tr>
                            <td class="bold">{{ $index + 1 }}</td>
                            <td>#{{ $p->no_urut }}</td>
                            <td class="text-left bold">{{ strtoupper($p->nama_sekolah) }}</td>
                            <td class="bold">{{ number_format($p->skor_kategori, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

</body>
</html>