<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Juara Kategori - {{ $lomba->nama_lomba }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #000; }
        .kop { text-align: center; font-weight: bold; margin-bottom: 25px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .kop h2 { margin: 2px 0; font-size: 16px; }
        .kop h3 { margin: 2px 0; font-size: 13px; font-weight: normal; }
        
        /* Layout Grid agar muat 2 tabel bersebelahan */
        .grid-container { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 15px; }
        .kategori-box { width: 48%; margin-bottom: 20px; break-inside: avoid; }
        
        .kategori-title { font-weight: bold; font-size: 14px; margin-bottom: 5px; text-decoration: underline; text-align: center; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; text-align: center; }
        th { background-color: #e2e8f0; font-weight: bold; }
        .text-left { text-align: left; padding-left: 5px; }
        .bold { font-weight: bold; }

        @media print {
            @page { size: A4 portrait; margin: 15mm; }
            button { display: none; }
            th { background-color: #e2e8f0 !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="kop">
        <h2>JUARA {{ strtoupper($lomba->nama_lomba) }} KATEGORI</h2>
        <h3>TINGKAT NASIONAL / SEDERAJAT</h3>
        <h3>{{ \Carbon\Carbon::parse($lomba->tanggal_pelaksanaan)->format('d-M-Y') }}</h3>
    </div>

    <div class="grid-container">
        @foreach($kategoris as $kat)
            <div class="kategori-box">
                <div class="kategori-title">{{ strtoupper($kat->nama_kategori) }}</div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 10%">No</th>
                            <th style="width: 15%">No. Urut</th>
                            <th class="text-left" style="width: 55%">Sekolah</th>
                            <th style="width: 20%">Total Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Tampilkan Top 15 saja agar muat rapi, atau hapus take(15) untuk semua -->
                        @foreach($ranking_per_kategori[$kat->id]->take(15) as $index => $p)
                        <tr>
                            <td class="bold">{{ $index + 1 }}</td>
                            <td>{{ $p->no_urut }}</td>
                            <td class="text-left bold">{{ $p->nama_sekolah }}</td>
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