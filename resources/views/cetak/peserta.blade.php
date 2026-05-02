<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peserta - {{ $peserta->nama_sekolah }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #000; }
        
        /* HEADER STYLES */
        .kop-container { border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .title-section { text-align: center; margin-bottom: 15px; }
        .title-section h2 { margin: 0; font-size: 16px; text-decoration: underline; font-weight: bold; }
        
        .info-table { width: 100%; border: none; font-weight: bold; font-size: 12px; }
        .info-table td { border: none; padding: 2px; text-align: left; }
        .info-table .right-align { text-align: right; }
        
        /* TABLE STYLES */
        .section-title { font-size: 12px; font-weight: bold; background: #e2e8f0; padding: 6px; margin-top: 15px; border: 1px solid #000; border-bottom: none; text-align: left;}
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #f8fafc; font-weight: bold; }
        .text-left { text-align: left; padding-left: 8px; }
        .bold { font-weight: bold; }

        /* DOUBLE BORDER UNTUK TABEL GRAND TOTAL */
        .table-grand-total { border: 4px double #000; font-size: 14px; }
        .table-grand-total td { border: 1px solid #000; padding: 8px; }

        @media print {
            @page { size: A4 portrait; margin: 15mm; }
            button { display: none; }
            .kop-container { border-bottom: 2px solid #000 !important; }
            .section-title { background: #e2e8f0 !important; -webkit-print-color-adjust: exact; }
            th { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    @php 
        $juris = \App\Models\Juri::where('lomba_id', $peserta->lomba_id)->get(); 
        $totalKotorSemua = 0; 
        $totalMinus = $peserta->denda->sum('poin_minus');
    @endphp

    <!-- ==================== HEADER INFO PESERTA ==================== -->
    <div class="kop-container">
        <table style="width: 100%; border: none; margin-bottom: 10px;">
            <tr>
                <td style="width: 25%; border: none; text-align: left; vertical-align: middle;">
                    <img src="{{ asset('img/logo_pandara.jpeg') }}" alt="Logo Pandara" style="max-height: 40px; display: inline-block;">
                    <div style="font-weight: 900; font-size: 16px; color: #1e3a8a; line-height: 1;">PANDARA</div>
                    <div style="font-size: 9px; letter-spacing: 1px;">SYSTEM REKAP NILAI</div>
                </td>
                <td style="width: 50%; border: none; text-align: center; vertical-align: middle;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: bold; text-decoration: underline;">DETAIL DATA LAPORAN PESERTA</h2>
                </td>
                <td style="width: 25%; border: none;"></td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td style="width: 10%">NO. URUT</td>
                <td style="width: 2%">:</td>
                <td style="width: 48%">{{ $peserta->no_urut }}</td>
                <td style="width: 40%" class="right-align" style="color: #1e3a8a;">PANDARA QUICK COUNT SYSTEM</td>
            </tr>
            <tr>
                <td>EVENT</td>
                <td>:</td>
                <td>{{ strtoupper($peserta->lomba->nama_lomba) }}</td>
                <td class="right-align">DURASI : {{ $peserta->durasi_tampil_detik ? gmdate("i:s", $peserta->durasi_tampil_detik) : '00:00' }}</td>
            </tr>
            <tr>
                <td>SEKOLAH</td>
                <td>:</td>
                <td>{{ strtoupper($peserta->nama_sekolah) }}</td>
                <td></td>
            </tr>
        </table>
    </div>

    <!-- ==================== TABEL RINCIAN NILAI ==================== -->
    <div style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">I. DAFTAR PENILAIAN</div>

    @foreach($kategoris as $kat)
        <div class="section-title">{{ strtoupper($kat->nama_kategori) }}</div>
        <table>
            <thead>
                <tr>
                    <th class="text-left" style="width: 45%">NAMA GERAKAN</th>
                    @foreach($juris as $index => $juri)
                        <th>JURI {{ $index + 1 }}</th>
                    @endforeach
                    <th style="width: 15%">NILAI</th>
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
                
                <!-- FORMAT SUB-TOTAL PER KATEGORI (Sesuai Referensi PDF) -->
                <tr style="background: #fff;">
                    <td colspan="{{ count($juris) + 1 }}" class="text-left bold">PENAMBAHAN POINT</td>
                    <td class="bold">0</td>
                </tr>
                <tr style="background: #fff;">
                    <td colspan="{{ count($juris) + 1 }}" class="text-left bold">PENGURANGAN POINT</td>
                    <td class="bold">(0)</td>
                </tr>
                <tr style="background: #f8fafc;">
                    <td colspan="{{ count($juris) + 1 }}" class="text-left bold">TOTAL POINT</td>
                    <td class="bold">{{ $grandTotalKategori }}</td>
                </tr>
            </tbody>
        </table>
        
        @php $totalKotorSemua += $grandTotalKategori; @endphp
    @endforeach

    <!-- ==================== REKAPITULASI AKHIR (OTENTIK RAKSASA) ==================== -->
    <div style="font-weight: bold; font-size: 14px; margin-top: 25px; margin-bottom: 5px;">II. AKUMULASI NILAI AKHIR</div>
    
    <table style="width: 100%; border-collapse: collapse; border: 4px double #000; text-align: center; font-weight: bold; font-family: 'Times New Roman', Times, serif;">
        <tr>
            <td style="border: 1px solid #000; text-align: left; padding: 10px; font-size: 14px; width: 25%;">TOTAL POIN</td>
            <td colspan="2" style="border: 1px solid #000; padding: 10px; font-size: 18px; width: 50%;">{{ number_format($totalKotorSemua, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; padding: 10px; font-size: 14px; width: 25%;">GRAND TOTAL</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; text-align: left; padding: 10px; font-size: 14px;">PENAMBAHAN</td>
            <td style="border: 1px solid #000; padding: 10px; font-size: 16px; width: 10%;">0</td>
            <td style="border: 1px solid #000; padding: 10px; width: 40%;"></td>
            <!-- KOLOM GRAND TOTAL RAKSASA -->
            <td rowspan="2" style="border: 1px solid #000; font-size: 52px; padding: 10px; vertical-align: middle;">
                {{ number_format($totalKotorSemua - $totalMinus, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; text-align: left; padding: 10px; font-size: 14px;">PENGURANGAN</td>
            <td style="border: 1px solid #000; padding: 10px; font-size: 16px;">{{ number_format($totalMinus, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; padding: 10px;"></td>
        </tr>
    </table>

    <!-- ==================== TANDA TANGAN ==================== -->
    <table style="border: none; width: 100%; text-align: center; margin-top: 40px; font-weight: bold;">
        <tr style="border: none;">
            <td style="border: none; width: 50%;">TIM REKAP<br><br><br><br><br><b>( ............................... )</b></td>
            <td style="border: none; width: 50%;">PELATIH<br><br><br><br><br><b>( ............................... )</b></td>
        </tr>
    </table>

</body>
</html>