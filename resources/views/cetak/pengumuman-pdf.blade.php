<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>HASIL KEJUARAAN {{ strtoupper($tingkat) }} - {{ $lomba->nama_lomba }}</title>
    <style>
        @page { 
            margin: 0.8cm 1cm 1cm 1cm; 
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 8pt; 
            color: #000; 
            line-height: 1.1; 
        }

        /* HEADER KOP DENGAN LOGO */
        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            margin-bottom: 15px;
            padding-bottom: 5px;
        }
        .logo-box {
            width: 80px;
            text-align: center;
        }
        .header-text {
            text-align: center;
        }
        .header-text h1 { 
            margin: 0; 
            font-size: 14pt; 
            text-transform: uppercase; 
            font-weight: bold; 
        }
        .header-text h2 { 
            margin: 2px 0; 
            font-size: 11pt; 
            font-weight: bold; 
        }
        .header-text p { 
            margin: 0; 
            font-size: 8pt; 
            font-style: italic; 
        }

        /* LAYOUT MASTER 2 KOLOM */
        .master-container { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .col-left { 
            width: 49%; 
            vertical-align: top; 
            padding-right: 8px; 
        }
        .col-right { 
            width: 49%; 
            vertical-align: top; 
            padding-left: 8px; 
        }

        /* STYLING TABEL */
        .table-title { 
            background-color: #000; 
            color: #fff; 
            text-align: center; 
            font-weight: bold; 
            padding: 3px; 
            font-size: 8.5pt; 
            text-transform: uppercase; 
            border: 1px solid #000;
        }
        
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px; 
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #000; 
            padding: 3px 2px; 
            text-align: center; 
        }
        table.data-table th { 
            background-color: #f2f2f2; 
            font-weight: bold; 
            font-size: 7.5pt; 
        }
        .text-left { text-align: left !important; padding-left: 4px !important; }
        .font-bold { font-weight: bold; }

        /* FOOTER TANDA TANGAN 5 JURI */
        .footer-ttd {
            margin-top: 15px;
            width: 100%;
            border-collapse: collapse;
        }
        .ttd-cell {
            width: 20%; /* PERBAIKAN: Diubah jadi 20% agar muat 5 Juri rata */
            text-align: center;
            vertical-align: top;
            font-size: 7.5pt;
        }
        .signature-line {
            margin-top: 45px;
            font-weight: bold;
            text-decoration: underline;
        }
        .system-footer {
            text-align: center;
            font-size: 6pt;
            color: #666;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="logo-box">
                <img src="{{ public_path('img/logo-pandara.png') }}" alt="Logo" style="width: 65px;">
            </td>
            <td class="header-text">
                <h1>BERITA ACARA HASIL KEJUARAAN</h1>
                <h1>{{ strtoupper($lomba->nama_lomba) }}</h1>
                <h2>TINGKAT {{ strtoupper($tingkat) }} SEDERAJAT</h2>
                <p>Gedung Serbaguna SMA SUMPAH PEMUDA | Tanggal: {{ \Carbon\Carbon::parse($lomba->tanggal_pelaksanaan)->format('d F Y') }}</p>
            </td>
            <td class="logo-box">
                <img src="{{ public_path('img/logo-pandara.png') }}" alt="Logo" style="width: 65px;">
            </td>
        </tr>
    </table>

    <table class="master-container">
        <tr>
            <td class="col-left">
                <div class="table-title">KLASEMEN JUARA BERJENJANG</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="18%">PREDIKAT</th>
                            <th width="8%">NO</th>
                            <th class="text-left">NAMA SEKOLAH</th>
                            <th width="12%">NILAI AKHIR</th>
                            @if(count($tb_kategoris) > 0)
                                <th width="12%" style="background-color: #fef08a;">{{ strtoupper($tb_kategoris->first()->nama_kategori) }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ranked as $idx => $p)
                            @php
                                // Labeling
                                $urutan = $idx + 1;
                                if($urutan <= 3) $label = "UTAMA $urutan";
                                elseif($urutan <= 6) $label = "HARAPAN " . ($urutan-3);
                                elseif($urutan <= 9) $label = "MADYA " . ($urutan-6);
                                elseif($urutan <= 12) $label = "BINA " . ($urutan-9);
                                elseif($urutan <= 15) $label = "MULA " . ($urutan-12);
                                elseif($urutan <= 18) $label = "PURWA " . ($urutan-15);
                                elseif($urutan <= 21) $label = "CARAKA " . ($urutan-18);
                                elseif($urutan <= 24) $label = "PERINTIS " . ($urutan-21);
                                elseif($urutan <= 27) $label = "POTENSIAL " . ($urutan-24);
                                else $label = "PESERTA " . ($urutan-27);

                                // Deteksi Seri
                                $is_tied = false;
                                if ($idx > 0 && $p->grand_total == $ranked[$idx-1]->grand_total) $is_tied = true;
                                if ($idx < count($ranked) - 1 && $p->grand_total == $ranked[$idx+1]->grand_total) $is_tied = true;
                            @endphp
                            <tr>
                                <td class="font-bold">{{ $label }}</td>
                                <td class="font-bold">{{ $p->no_urut }}</td>
                                <td class="text-left font-bold">{{ strtoupper($p->nama_sekolah) }}</td>
                                <td class="font-bold text-blue-800" style="background-color: #f8fafc;">
                                    {{ number_format($p->grand_total, 0, ',', '.') }}
                                </td>
                                @if(count($tb_kategoris) > 0)
                                    @php $nilai_tb = number_format($p->skor_kategori[$tb_kategoris->first()->id] ?? 0, 0, ',', '.'); @endphp
                                    @if($is_tied)
                                        <td class="font-bold" style="background-color: #ffff00; color: #000;">{{ $nilai_tb }}</td>
                                    @else
                                        <td>{{ $nilai_tb }}</td> @endif
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>

            <td class="col-right">
                <div class="table-title">🏆 JUARA UMUM</div>
                <table class="data-table">
                    @foreach($juaraUmum as $idx => $p)
                    <tr>
                        <td width="25%" class="font-bold">JUARA UMUM</td>
                        <td width="10%" class="font-bold">{{ $p->no_urut }}</td>
                        <td class="text-left font-bold">{{ strtoupper($p->nama_sekolah) }}</td>
                        <td width="20%" class="font-bold">{{ number_format($p->skor_akhir_umum, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </table>

                @foreach($bestCategories as $bc)
                    @php
                        $kat = $bc['kategori'];
                        $p_list = $bc['pesertas'];
                        if(count($p_list) == 0) continue;
                        
                        // Cari Tie Breaker yang BUKAN kategori ini sendiri
                        $tb_kat = $tb_kategoris->firstWhere('id', '!=', $kat->id);
                        if (!$tb_kat && count($tb_kategoris) > 0) $tb_kat = $tb_kategoris->first(); // Safety fallback
                    @endphp
                    
                    <div class="table-title">BEST {{ strtoupper($kat->nama_kategori) }}</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="18%">RANK</th>
                                <th width="10%">NO</th>
                                <th class="text-left">NAMA SEKOLAH</th>
                                <th width="15%">NILAI</th>
                                @if($tb_kat)
                                    <th width="15%" style="background-color: #fef08a;">{{ strtoupper($tb_kat->nama_kategori) }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($p_list as $idx => $p)
                                @php
                                    // Deteksi Seri di Best Kategori
                                    $is_tied = false;
                                    if ($idx > 0 && $p->skor_spesifik == $p_list[$idx-1]->skor_spesifik) $is_tied = true;
                                    if ($idx < count($p_list) - 1 && $p->skor_spesifik == $p_list[$idx+1]->skor_spesifik) $is_tied = true;
                                @endphp
                                <tr>
                                    <td class="font-bold">RANK {{ $idx + 1 }}</td>
                                    <td class="font-bold">{{ $p->no_urut }}</td>
                                    <td class="text-left font-bold">{{ strtoupper($p->nama_sekolah) }}</td>
                                    <td class="font-bold text-blue-800" style="background-color: #f8fafc;">
                                        {{ number_format($p->skor_spesifik, 0, ',', '.') }}
                                    </td>
                                    @if($tb_kat)
                                        @php $nilai_tb = number_format($p->skor_kategori[$tb_kat->id] ?? 0, 0, ',', '.'); @endphp
                                        @if($is_tied)
                                            <td class="font-bold" style="background-color: #ffff00; color: #000;">{{ $nilai_tb }}</td>
                                        @else
                                            <td>{{ $nilai_tb }}</td> @endif
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </td>
        </tr>
    </table>

    <p style="text-align: center; margin-top: 10px; font-weight: bold; text-decoration: underline;">PENGESAHAN DEWAN JURI</p>
    <table class="footer-ttd">
        <tr>
            <td class="ttd-cell">
                <p>Juri I<br>(PBB & Komandan)</p>
                <p class="signature-line">( ______________ )</p>
            </td>
            <td class="ttd-cell">
                <p>Juri II<br>(PBB & Komandan)</p>
                <p class="signature-line">( ______________ )</p>
            </td>
            <td class="ttd-cell">
                <p>Juri III<br>(PBB & Komandan)</p>
                <p class="signature-line">( ______________ )</p>
            </td>
            <td class="ttd-cell">
                <p>Juri<br>(Variasi & Formasi)</p>
                <p class="signature-line">( ______________ )</p>
            </td>
            <td class="ttd-cell">
                <p>Juri<br>(Kostum & Make-up)</p>
                <p class="signature-line">( ______________ )</p>
            </td>
        </tr>
    </table>

    <div class="system-footer">
        Dicetak otomatis oleh PANDARA System pada {{ date('d/m/Y H:i') }} - Bekasi, Jawa Barat
    </div>

</body>
</html>