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

    @php
        // 1. PROSES LOGO KIRI (LOGO SISTEM/PANDARA) DENGAN INLINE BASE64
        $logoKiriPath = public_path('img/logo-pandara.png');
        $logoKiriB64 = null;
        
        if (file_exists($logoKiriPath)) {
            $type = pathinfo($logoKiriPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoKiriPath);
            $logoKiriB64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        // 2. PROSES LOGO KANAN (LOGO EVENT DARI DATABASE)
        $logoKananB64 = $logoKiriB64; // Fallback ke logo sistem jika panitia tidak upload logo event
        
        if (!empty($lomba->logo)) {
            $eventLogoPath = storage_path('app/public/' . $lomba->logo);
            if (file_exists($eventLogoPath)) {
                $type = pathinfo($eventLogoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($eventLogoPath);
                $logoKananB64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
    @endphp

    <table class="header-table" style="width: 100%; text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px;">
        <tr>
            <td class="logo-box" style="width: 15%;">
                @if($logoKiriB64)
                    <img src="{{ $logoKiriB64 }}" alt="Logo Kiri" style="width: 65px;">
                @endif
            </td>
            
            <td class="header-text" style="width: 70%;">
                <h1 style="margin: 0; font-size: 16px;">BERITA ACARA HASIL KEJUARAAN</h1>
                <h1 style="margin: 5px 0; font-size: 18px;">{{ strtoupper($lomba->nama_lomba) }}</h1>
                <h2 style="margin: 0; font-size: 14px;">TINGKAT {{ strtoupper($tingkat) }} SEDERAJAT</h2>
                <p style="margin: 5px 0 0 0; font-size: 11px;">Lokasi: {{ strtoupper($lomba->lokasi) }} | Tanggal: {{ \Carbon\Carbon::parse($lomba->tanggal_pelaksanaan)->format('d F Y') }}</p>
            </td>
            
            <td class="logo-box" style="width: 15%;">
                @if($logoKananB64)
                    <img src="{{ $logoKananB64 }}" alt="Logo Kanan" style="width: 65px; max-height: 65px; object-fit: contain;">
                @endif
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
                        @php
                            // Siapkan format urutan juara dari database
                            $urutanFormat = is_array($lomba->urutan_juara) && count($lomba->urutan_juara) > 0 
                                            ? $lomba->urutan_juara 
                                            : []; 
                        @endphp

                        @foreach($ranked as $idx => $p)
                            @php
                                // Labeling Dinamis: Ambil dari urutanFormat sesuai index.
                                // Jika jumlah peserta lebih banyak dari urutan juara yang disiapkan, beri default "PERINGKAT X"
                                $label = isset($urutanFormat[$idx]) ? strtoupper($urutanFormat[$idx]) : "PERINGKAT " . ($idx + 1);

                                // Deteksi Seri (Tie)
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
                                        <td>{{ $nilai_tb }}</td> 
                                    @endif
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
                @foreach($juaraSpesials as $js)
                    <div class="table-title">🌟 JUARA {{ strtoupper($js['kategori']->nama_kategori) }}</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="18%">RANK</th>
                                <th width="10%">NO</th>
                                <th class="text-left">NAMA SEKOLAH</th>
                                <th width="15%">TOTAL POIN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($js['pemenang'] as $w)
                                <tr>
                                    <td class="font-bold">RANK {{ $w->rank }}</td>
                                    <td class="font-bold">{{ $w->peserta->no_urut ?? '-' }}</td>
                                    <td class="text-left">{{ strtoupper($w->peserta->nama_sekolah ?? '-') }}</td>
                                    
                                    <td class="font-bold text-purple-600" style="background-color: #f3e8ff;">
                                        {{ $w->keterangan ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </td>
        </tr>
    </table>

    <p style="text-align: center; margin-top: 25px; font-weight: bold; text-decoration: underline; font-family: Arial, sans-serif;">
        PENGESAHAN DEWAN JURI
    </p>
    
    <table class="footer-ttd" style="width: 100%; border-collapse: collapse; margin-top: 15px; font-family: Arial, sans-serif; font-size: 11px;">
        <tr>
            @forelse($juris as $juri)
                <td class="ttd-cell" style="text-align: center; vertical-align: top; padding: 10px;">
                    <p style="margin: 0; font-weight: bold; color: #1e293b;">
                        {{ $juri->posisi }}
                    </p>
                    <p style="margin: 2px 0 0 0; font-size: 10px; color: #64748b; italic">
                        {{ $lomba->nama_lomba }}
                    </p>
                    
                    <br><br><br><br>
                    
                    <p class="signature-line" style="margin: 0; font-weight: bold; color: #0f172a;">
                        ( {{ strtoupper($juri->nama) }} )
                    </p>
                </td>
            @empty
                <td style="text-align: center; color: #94a3b8; font-style: italic; padding: 20px;">
                    Belum ada data juri terregistrasi pada event ini.
                </td>
            @endforelse
        </tr>
    </table>

    <div class="system-footer">
        Dicetak otomatis oleh PANDARA System pada {{ date('d/m/Y') }} - Bekasi, Jawa Barat
    </div>

</body>
</html>