<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peserta - {{ $peserta->nama_sekolah }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 11px; color: #000; }
        
        /* HEADER STYLES */
        .kop-container { border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
        .info-table { width: 100%; border: none; font-weight: bold; font-size: 12px; }
        .info-table td { border: none; padding: 2px; text-align: left; }
        .info-table .right-align { text-align: right; }
        
        /* TABLE STYLES */
        .section-title { font-size: 12px; font-weight: bold; background: #e2e8f0; padding: 6px; margin-top: 15px; border: 1px solid #000; border-bottom: none; text-align: left;}
        table.table-nilai { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.table-nilai th, table.table-nilai td { border: 1px solid #000; padding: 5px; text-align: center; }
        table.table-nilai th { background-color: #f8fafc; font-weight: bold; }
        .text-left { text-align: left; padding-left: 8px !important; }
        .bold { font-weight: bold; }

        /* DOUBLE BORDER UNTUK TABEL GRAND TOTAL (OTENTIK) */
        .table-grand-total { width: 100%; border-collapse: collapse; border: 4px double #000; text-align: center; font-weight: bold; font-family: 'Times New Roman', Times, serif; margin-top: 5px; margin-bottom: 25px; }
        .table-grand-total td { border: 1px solid #000; padding: 8px; }

        @media print {
            @page { size: A4 portrait; margin: 15mm; }
            button { display: none; }
            .kop-container { border-bottom: 2px solid #000 !important; }
            .section-title { background: #e2e8f0 !important; -webkit-print-color-adjust: exact; }
            table.table-nilai th { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    @php 
        $juris = \App\Models\Juri::where('lomba_id', $peserta->lomba_id)->get(); 
        $totalMinus = $peserta->denda->sum('poin_minus');

        // Pisahkan Kategori Berdasarkan Setingan Master (Otomatis)
        $kategoriUtama = $kategoris->where('is_utama', 1);
        $kategoriKhusus = $kategoris->where('is_utama', 0);

        $totalUtamaKotor = 0; 
        $totalUmumKotor = 0; 
    @endphp

    <!-- ==================== HEADER INFO PESERTA (KOP RETRO PANDARA) ==================== -->
    <div class="kop-container" style="border: none; padding-bottom: 5px; margin-bottom: 25px;">
        <table style="width: 100%; border: none;">
            <tr>
                <!-- BAGIAN LOGO KIRI -->
                <td style="width: 35%; text-align: center; vertical-align: middle;">
                    <!-- Pastikan antum sudah punya file logo ini di folder public/img -->
                    <img src="{{ asset('img/logo-pandara.png') }}" alt="Logo PUCKS Pandara" style="max-width: 220px; display: inline-block;">
                </td>
                
                <!-- BAGIAN IDENTITAS KANAN (KOTAK 3D RETRO) -->
                <td style="width: 65%; vertical-align: middle; padding-left: 15px;">
                    <table style="width: 100%; border: none; font-family: 'Times New Roman', Times, serif; font-size: 16px; font-weight: bold;">
                        <tr>
                            <td style="width: 20%; padding: 6px 0; border: none; text-align: left;">NO. URUT</td>
                            <td style="width: 5%; border: none; text-align: center;">:</td>
                            <td style="width: 75%; border: none;">
                                <!-- Efek border inset memberi kesan kotak teks 3D klasik -->
                                <div style="border: 3px inset #888; padding: 6px 12px; background-color: #fff; width: 100%; box-sizing: border-box;">
                                    {{ $peserta->no_urut }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; border: none; text-align: left;">EVENT</td>
                            <td style="border: none; text-align: center;">:</td>
                            <td style="border: none;">
                                <div style="border: 3px inset #888; padding: 6px 12px; background-color: #fff; width: 100%; box-sizing: border-box;">
                                    {{ strtoupper($peserta->lomba->nama_lomba) }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; border: none; text-align: left;">SEKOLAH</td>
                            <td style="border: none; text-align: center;">:</td>
                            <td style="border: none;">
                                <div style="border: 3px inset #888; padding: 6px 12px; background-color: #fff; width: 100%; box-sizing: border-box;">
                                    {{ strtoupper($peserta->nama_sekolah) }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 6px 0; border: none; text-align: left;">DURASI</td>
                            <td style="border: none; text-align: center;">:</td>
                            <td style="border: none;">
                                <div style="border: 3px inset #888; padding: 6px 12px; background-color: #fff; width: 100%; box-sizing: border-box;">
                                    {{ $peserta->durasi_tampil_detik ? gmdate("i:s", $peserta->durasi_tampil_detik) : '00:00' }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- ==================== BAGIAN I: DAFTAR PENILAIAN UTAMA ==================== -->
    @if($kategoriUtama->count() > 0)
        <div style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">I. DAFTAR PENILAIAN UTAMA</div>

        @foreach($kategoriUtama as $kat)
            <div class="section-title">{{ strtoupper($kat->nama_kategori) }}</div>
            <table class="table-nilai">
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
            
            @php 
                $totalUtamaKotor += $grandTotalKategori; 
                if($kat->is_umum) { $totalUmumKotor += $grandTotalKategori; }
            @endphp
        @endforeach

        <!-- TABEL RAKSASA GRAND TOTAL UTAMA -->
        <table class="table-grand-total">
            <tr>
                <td style="text-align: left; font-size: 14px; width: 25%;">TOTAL POIN (UTAMA)</td>
                <td colspan="2" style="font-size: 18px; width: 50%;">{{ number_format($totalUtamaKotor, 0, ',', '.') }}</td>
                <td style="font-size: 14px; width: 25%;">GRAND TOTAL (UTAMA)</td>
            </tr>
            <tr>
                <td style="text-align: left; font-size: 14px;">PENAMBAHAN</td>
                <td style="font-size: 16px; width: 10%;">0</td>
                <td style="width: 40%;"></td>
                <td rowspan="2" style="font-size: 48px; vertical-align: middle;">
                    {{ number_format($totalUtamaKotor - $totalMinus, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="text-align: left; font-size: 14px;">PENGURANGAN</td>
                <td style="font-size: 16px;">{{ number_format($totalMinus, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </table>
    @endif

    <!-- ==================== BAGIAN II: DAFTAR PENILAIAN KHUSUS ==================== -->
    @if($kategoriKhusus->count() > 0)
        <div style="font-weight: bold; font-size: 14px; margin-top: 20px; margin-bottom: 5px;">II. DAFTAR PENILAIAN KHUSUS / TAMBAHAN</div>

        @foreach($kategoriKhusus as $kat)
            <div class="section-title">{{ strtoupper($kat->nama_kategori) }}</div>
            <table class="table-nilai">
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
            
            @php 
                if($kat->is_umum) { $totalUmumKotor += $grandTotalKategori; }
            @endphp
        @endforeach
    @endif

    <!-- ==================== TANDA TANGAN ==================== -->
    <table style="border: none; width: 100%; text-align: center; margin-top: 40px; font-weight: bold; page-break-inside: avoid;">
        <tr style="border: none;">
            <td style="border: none; width: 50%;">TIM REKAP<br><br><br><br><br><b>( ............................... )</b></td>
            <td style="border: none; width: 50%;">PELATIH<br><br><br><br><br><b>( ............................... )</b></td>
        </tr>
    </table>

</body>
</html>