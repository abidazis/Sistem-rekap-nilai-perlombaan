<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Juri (LJK) - {{ $lomba->nama_lomba }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #000; }
        
        /* HEADER STYLES */
        .kop-table { width: 100%; border: none; margin-bottom: 20px; font-weight: bold;}
        .kop-table td { border: none; vertical-align: middle; }
        .judul-kiri { line-height: 1.4; }
        .judul-kiri h2 { margin: 0; font-size: 18px; }
        .judul-kiri h3 { margin: 0; font-size: 14px; }
        
        .box-info { display: flex; flex-direction: column; gap: 5px; }
        .box-row { display: flex; align-items: center; gap: 10px; }
        .box-input { border: 1px solid #000; padding: 5px; width: 180px; height: 15px; }
        
        /* TABLE STYLES */
        table.ljk-data { width: 100%; border-collapse: collapse; text-align: center; }
        table.ljk-data th, table.ljk-data td { border: 1px solid #000; padding: 6px 4px; }
        table.ljk-data th { background-color: #f1f5f9; font-weight: bold; }
        
        .kategori-row td { background-color: #e2e8f0; font-weight: bold; }
        .text-left { text-align: left; padding-left: 8px !important; }
        
        .ttd-box { margin-top: 30px; text-align: right; font-weight: bold; padding-right: 50px; }

        @media print {
            @page { size: A4 landscape; margin: 15mm; }
            button { display: none; }
            .kategori-row td { background-color: #e2e8f0 !important; -webkit-print-color-adjust: exact; }
            th { background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    <table class="kop-table">
        <tr>
            <td style="width: 40%;" class="judul-kiri">
                <h2>{{ strtoupper($lomba->nama_lomba) }}</h2>
                <h3>FORMAT NILAI (LEMBAR JURI)</h3>
                <h3 style="font-weight: normal;">TINGKAT NASIONAL / SEDERAJAT</h3>
            </td>
            <td style="width: 30%;">
                <div class="box-info">
                    <div class="box-row"><span>Nama Sekolah</span> <div class="box-input"></div></div>
                    <div class="box-row"><span>No. Urut</span> <div class="box-input" style="width: 50px;"></div></div>
                    <div class="box-row"><span>Waktu Tampil</span> <div class="box-input" style="width: 100px;"></div></div>
                </div>
            </td>
            <td style="width: 30%; text-align: right;">
                <h3 style="margin: 0; color: #1e3a8a; font-size: 16px;">PANDARA</h3>
                <p style="margin: 0; font-size: 10px;">QUICK COUNT SYSTEM</p>
                <div style="margin-top: 5px; font-size: 12px;">
                    JURI : <span style="border: 1px solid #000; padding: 2px 10px; margin-left: 5px;">1</span>
                    <span style="border: 1px solid #000; padding: 2px 10px;">2</span>
                    <span style="border: 1px solid #000; padding: 2px 10px;">3</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="ljk-data">
        @foreach($kategoris as $index => $kat)
            @php 
                $huruf = chr(65 + $index); // Mengubah 0,1,2 jadi A,B,C
            @endphp
            
            <!-- HEADER KATEGORI -->
            <tr class="kategori-row">
                <td style="width: 3%;">{{ $huruf }}</td>
                <td class="text-left" style="width: 37%;">{{ strtoupper($kat->nama_kategori) }}</td>
                <td colspan="2" style="width: 12%;">SK</td>
                <td colspan="2" style="width: 12%;">K</td>
                <td colspan="3" style="width: 18%;">C</td>
                <td colspan="2" style="width: 12%;">B</td>
                <td colspan="1" style="width: 6%;">SB</td>
            </tr>

            <!-- ITEM GERAKAN -->
            @foreach($kat->items as $item)
                <tr>
                    <td>{{ $item->urutan }}</td>
                    <td class="text-left">{{ $item->nama_gerakan }}</td>
                    
                    @php
                        // Ambil opsi nilai, limit ke 12 kolom agar rapi dan tidak merusak tabel
                        $opsi = $item->opsi_nilai ?? [];
                        $max_cols = 12; 
                    @endphp
                    
                    @for($i = 0; $i < $max_cols; $i++)
                        <td>{{ $opsi[$i] ?? '' }}</td>
                    @endfor
                </tr>
            @endforeach
        @endforeach
    </table>

    <div class="ttd-box">
        <p>CATATAN JURI :</p>
        <br><br><br><br>
        <p>TTD JURI</p>
    </div>

</body>
</html>