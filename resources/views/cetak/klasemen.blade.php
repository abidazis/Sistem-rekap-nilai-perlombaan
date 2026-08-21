<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil LKBB - {{ $lomba->nama_lomba }}</title>
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
        
        .page-break { page-break-before: always; }
        .kategori-title { font-weight: bold; font-size: 14px; margin-top: 20px; margin-bottom: 5px; background: #1e293b; color: white; padding: 5px 10px; display: inline-block;}

        @media print {
            @page { size: A4 portrait; margin: 15mm; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- ============================================== -->
    <!-- BAGIAN 1: HASIL KLASEMEN UTAMA (GRAND TOTAL)   -->
    <!-- ============================================== -->
    <div class="kop">
        <h2>JUARA {{ strtoupper($lomba->nama_lomba) }}</h2>
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
                @php 
                    $total_kotor = $p->total_skor + $p->total_minus; 
                @endphp
                <tr>
                    <td class="bold">{{ $index + 1 }}</td>
                    <td>{{ $p->no_urut }}</td>
                    <td class="text-left bold">{{ $p->nama_sekolah }}</td>
                    <td>{{ number_format($total_kotor, 0, ',', '.') }}</td>
                    <td style="color: red;">{{ $p->total_minus > 0 ? $p->total_minus : '0' }}</td>
                    <td style="font-size: 10px;">{{ $p->keterangan_denda }}</td>
                    <td>{{ $p->durasi_format }}</td>
                    <td class="bold">{{ number_format($p->total_skor, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <!-- ============================================== -->
    <!-- BAGIAN 2: HASIL KLASEMEN PER KATEGORI          -->
    <!-- ============================================== -->
    <div class="page-break"></div>

    <div class="kop" style="margin-bottom: 10px;">
        <h2>JUARA {{ strtoupper($lomba->nama_lomba) }} KATEGORI</h2>
    </div>

    <!-- Looping Otomatis Semua Kategori (PBB, Vafor, Danton, dll) -->
    @foreach($kategoris as $kat)
        @php
            // Urutkan ulang peserta KHUSUS berdasarkan nilai kategori ini saja
            $ranking_kategori = $ranking->sortByDesc(function($p) use ($kat) {
                return $p->skor_kategori[$kat->id] ?? 0;
            })->values();
        @endphp

        <div class="kategori-title">{{ strtoupper($kat->nama_kategori) }}</div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 10%">No. Urut</th>
                    <th class="text-left" style="width: 60%">Sekolah</th>
                    <th style="width: 25%">Total Nilai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ranking_kategori as $index => $p)
                <tr>
                    <td class="bold">{{ $index + 1 }}</td>
                    <td>{{ $p->no_urut }}</td>
                    <td class="text-left bold">{{ $p->nama_sekolah }}</td>
                    <td class="bold">{{ number_format($p->skor_kategori[$kat->id] ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

</body>
</html>