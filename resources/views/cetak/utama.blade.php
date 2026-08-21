<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Juara Utama - {{ $lomba->nama_lomba }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 12px; }
        .kop { text-align: center; font-weight: bold; margin-bottom: 20px; }
        .kop h2 { margin: 2px 0; font-size: 18px; }
        .kop h3 { margin: 2px 0; font-size: 14px; font-weight: normal; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #000; padding: 6px 8px; text-align: center; }
        th { background-color: #d1d5db; font-weight: bold; font-size: 11px; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }

        @media print {
            @page { size: A4 portrait; margin: 15mm; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="kop">
        <h2>JUARA {{ strtoupper($lomba->nama_lomba) }}</h2>
        <h3>KATEGORI JUARA UTAMA ({{ strtoupper($nama_kategori_dipilih) }})</h3>
        <h3>TINGKAT NASIONAL / SEDERAJAT</h3>
        <h3>{{ \Carbon\Carbon::parse($lomba->tanggal_pelaksanaan)->format('d-M-Y') }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 8%">No. Urut</th>
                <th class="text-left" style="width: 35%">Sekolah</th>
                <th style="width: 10%">Total</th>
                <th style="width: 8%">Minus</th>
                <th style="width: 16%">Keterangan</th>
                <th style="width: 8%">Durasi</th>
                <th style="width: 10%">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ranking as $index => $p)
                <tr>
                    <td class="bold">{{ $index + 1 }}</td>
                    <td>{{ $p->no_urut }}</td>
                    <td class="text-left bold">{{ $p->nama_sekolah }}</td>
                    <td>{{ number_format($p->total_kotor, 0, ',', '.') }}</td>
                    <td style="color: red;">{{ $p->total_minus > 0 ? $p->total_minus : '0' }}</td>
                    <td style="font-size: 10px;">{{ $p->keterangan_denda }}</td>
                    <td>{{ $p->durasi_format }}</td>
                    <td class="bold">{{ number_format($p->grand_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="border: none; width: 100%; text-align: center; margin-top: 50px;">
        <tr style="border: none;">
            <td style="border: none; width: 33%;">Mengetahui,<br>Ketua Pelaksana<br><br><br><br><b>( ............................... )</b></td>
            <td style="border: none; width: 33%;"></td>
            <td style="border: none; width: 33%;">Bekasi, {{ date('d F Y') }}<br>Koordinator Tim Rekap<br><br><br><br><b>( ............................... )</b></td>
        </tr>
    </table>

</body>
</html>