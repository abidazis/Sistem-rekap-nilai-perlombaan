<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Laporan Peserta - {{ $peserta->nama_sekolah }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; font-weight: bold; }
        .header div { line-height: 1.5; }
        .section-title { font-size: 13px; font-weight: bold; background: #eee; padding: 5px; margin-top: 15px; border: 1px solid #000; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 4px 6px; text-align: center; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        @media print {
            @page { size: A4 portrait; margin: 15mm; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    @php 
        // Ambil data Juri untuk lomba ini agar dinamis
        $juris = \App\Models\Juri::where('lomba_id', $peserta->lomba_id)->get(); 
        
        // Siapkan wadah untuk hitung Total Akhir
        $totalKotorSemua = 0; 
        $totalMinus = $peserta->denda->sum('poin_minus');
    @endphp

    <h2 style="text-align: center; margin-bottom: 20px;">DETAIL DATA LAPORAN PESERTA</h2>

    <div class="header">
        <div>
            NO. URUT : {{ $peserta->no_urut }}<br>
            EVENT : {{ $peserta->lomba->nama_lomba }}<br>
            SEKOLAH : {{ $peserta->nama_sekolah }}
        </div>
        <div style="text-align: right;">
            DURASI : {{ $peserta->durasi_tampil_detik ? gmdate("i:s", $peserta->durasi_tampil_detik) : '00:00' }}
        </div>
    </div>

    @foreach($kategoris as $kat)
        <div class="section-title">{{ strtoupper($kat->nama_kategori) }}</div>
        <table>
            <thead>
                <tr>
                    <th class="text-left" style="width: 50%">NAMA GERAKAN</th>
                    @foreach($juris as $index => $juri)
                        <th>JURI {{ $index + 1 }}</th>
                    @endforeach
                    <th>TOTAL NILAI</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotalKategori = 0; @endphp
                @foreach($kat->items as $item)
                    <tr>
                        <td class="text-left">{{ $item->nama_gerakan }}</td>
                        
                        @php $totalItem = 0; @endphp
                        @foreach($juris as $juri)
                            @php
                                $nilai = $peserta->nilai->where('item_penilaian_id', $item->id)->where('juri_id', $juri->id)->first();
                                $skor = $nilai ? $nilai->nilai : 0;
                                $totalItem += $skor;
                            @endphp
                            <td>{{ $skor > 0 ? $skor : '0' }}</td>
                        @endforeach
                        
                        <td class="bold">{{ $totalItem }}</td>
                        @php $grandTotalKategori += $totalItem; @endphp
                    </tr>
                @endforeach
                <tr style="background: #f9f9f9;">
                    <td colspan="{{ count($juris) + 1 }}" class="text-left bold">TOTAL POIN {{ strtoupper($kat->nama_kategori) }}</td>
                    <td class="bold" style="font-size: 14px;">{{ $grandTotalKategori }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Tambahkan nilai kategori ini ke Total Kotor Keseluruhan -->
        @php $totalKotorSemua += $grandTotalKategori; @endphp
    @endforeach

    <div class="section-title" style="background: #ffcccc;">REKAPITULASI AKHIR</div>
    <table>
        <tr>
            <td class="text-left bold" style="width: 70%">TOTAL KOTOR SELURUH KATEGORI</td>
            <td class="bold">{{ number_format($totalKotorSemua, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="text-left bold" style="color: red;">PENGURANGAN POINT (DENDA)</td>
            <td class="bold" style="color: red;">-{{ number_format($totalMinus, 0, ',', '.') }}</td>
        </tr>
        <tr style="background: #eef;">
            <td class="text-left bold" style="font-size: 16px;">GRAND TOTAL POINT</td>
            <td class="bold" style="font-size: 16px;">{{ number_format($totalKotorSemua - $totalMinus, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Tanda Tangan Pengesahan -->
    <table style="border: none; width: 100%; text-align: center; margin-top: 40px;">
        <tr style="border: none;">
            <td style="border: none; width: 50%;">TIM REKAP<br><br><br><br><b>( ............................... )</b></td>
            <td style="border: none; width: 50%;">PELATIH<br><br><br><br><b>( ............................... )</b></td>
        </tr>
    </table>

</body>
</html>