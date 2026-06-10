<?php
// Memicu browser untuk langsung mengunduh sebagai file Excel (.xls)
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=LJK_" . str_replace(' ', '_', $lomba->nama_lomba) . ".xls");
header("Cache-Control: private, max-age=0, must-revalidate");
header("Pragma: public");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        .title { font-family: Arial, sans-serif; font-size: 16px; font-weight: bold; color: #1e293b; text-align: center; }
        .subtitle { font-family: Arial, sans-serif; font-size: 11px; font-weight: bold; text-align: center; color: #475569; }
        .system-tag { font-family: Arial, sans-serif; font-size: 9px; color: #94a3b8; text-align: right; }
        
        table.meta-table { font-family: Arial, sans-serif; font-size: 11px; margin-bottom: 15px; border: none; }
        table.meta-table td { padding: 4px; border: none; }
        .meta-label { font-weight: bold; text-align: right; }
        .meta-value { border-bottom: 1px solid #000000 !important; font-weight: bold; }
        
        table.main-table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 10px; }
        table.main-table th { background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; padding: 8px; border: 1px solid #cbd5e1; }
        table.main-table td { padding: 6px; border: 1px solid #cbd5e1; vertical-align: middle; }
        
        .cat-header { background-color: #0f172a; color: #ffffff; font-weight: bold; font-size: 11px; padding: 8px; }
        .subcat-header { background-color: #334155; color: #ffffff; font-weight: bold; text-align: center; }
        .item-bg { background-color: #ffffff; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .score-column { background-color: #fffde7; font-weight: bold; }
    </style>
</head>
<body>

    <div class="system-tag">PANDARA QUICK COUNT SYSTEM</div>
    <div class="title">LKBB {{ strtoupper($lomba->nama_lomba) }}</div>
    <div class="subtitle">LEMBAR JURI KERJA FISIK LAPANGAN</div>
    <br>

    <table class="meta-table">
        <tr>
            <td class="meta-label" style="width: 120px;">Nama Sekolah :</td>
            <td class="meta-value" style="width: 250px;"></td>
            <td style="width: 50px;"></td>
            <td class="meta-label" style="width: 100px;">Nama Juri :</td>
            <td class="meta-value" style="width: 200px;"></td>
        </tr>
        <tr>
            <td class="meta-label">No. Urut Tampil :</td>
            <td class="meta-value"></td>
            <td></td>
            <td class="meta-label">Jabatan Juri :</td>
            <td class="meta-value"></td>
        </tr>
        <tr>
            <td class="meta-label">Waktu Tampil :</td>
            <td class="meta-value">...... Menit ...... Detik</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="main-table">
        @foreach($kategoris as $kategori)
            @php
                $firstItem = $kategori->items->first();
                $opsiCount = $firstItem && is_array($firstItem->opsi_nilai) ? count($firstItem->opsi_nilai) : 0;
                $totalColumns = 2 + $opsiCount + 1; // NO + GERAKAN + OPSI + SKOR AKHIR
                
                // ANALISIS KEYWORD UNTUK SUB-HEADER (ALGORITMA AKURAT)
                $nama_kat_lower = strtolower($kategori->nama_kategori);
                $headerGroups = [];

                if (strpos($nama_kat_lower, 'pbb') !== false || strpos($nama_kat_lower, 'komandan') !== false) {
                    // Kategori 5 Sub-Header
                    $labels = ['SK', 'KURANG', 'CUKUP', 'BAIK', 'SB'];
                    $numGroups = count($labels);
                    if ($opsiCount == 11) {
                        $spans = [2, 3, 3, 2, 1]; // Presisi sesuai PDF Juknis asli
                    } else {
                        // Distribusi merata jika jumlah kolom custom/uji coba (seperti 10 kolom)
                        $base = floor($opsiCount / $numGroups);
                        $rem = $opsiCount % $numGroups;
                        $spans = [];
                        for ($i = 0; $i < $numGroups; $i++) {
                            $spans[$i] = $base + ($i < $rem ? 1 : 0);
                        }
                    }
                    for ($i = 0; $i < $numGroups; $i++) {
                        if ($spans[$i] > 0) $headerGroups[] = ['text' => $labels[$i], 'span' => $spans[$i]];
                    }
                } elseif (strpos($nama_kat_lower, 'variasi') !== false || strpos($nama_kat_lower, 'formasi') !== false || strpos($nama_kat_lower, 'vafor') !== false || strpos($nama_kat_lower, 'danton') !== false) {
                    // Kategori 4 Sub-Header
                    $labels = ['KURANG', 'CUKUP', 'BAIK', 'SANGAT BAIK'];
                    $numGroups = count($labels);
                    $base = floor($opsiCount / $numGroups);
                    $rem = $opsiCount % $numGroups;
                    $spans = [];
                    for ($i = 0; $i < $numGroups; $i++) {
                        $spans[$i] = $base + ($i < $rem ? 1 : 0);
                    }
                    for ($i = 0; $i < $numGroups; $i++) {
                        if ($spans[$i] > 0) $headerGroups[] = ['text' => $labels[$i], 'span' => $spans[$i]];
                    }
                } elseif (strpos($nama_kat_lower, 'kostum') !== false || strpos($nama_kat_lower, 'baju') !== false || strpos($nama_kat_lower, 'sepatu') !== false || strpos($nama_kat_lower, 'makeup') !== false) {
                    // Kategori 3 Sub-Header
                    $labels = ['KURANG', 'CUKUP', 'BAIK'];
                    $numGroups = count($labels);
                    $base = floor($opsiCount / $numGroups);
                    $rem = $opsiCount % $numGroups;
                    $spans = [];
                    for ($i = 0; $i < $numGroups; $i++) {
                        $spans[$i] = $base + ($i < $rem ? 1 : 0);
                    }
                    for ($i = 0; $i < $numGroups; $i++) {
                        if ($spans[$i] > 0) $headerGroups[] = ['text' => $labels[$i], 'span' => $spans[$i]];
                    }
                } else {
                    // Fallback default aman
                    $headerGroups = [['text' => 'RENTANG OPSI NILAI JURI', 'span' => max(1, $opsiCount)]];
                }
            @endphp

            <tr>
                <td colspan="{{ $totalColumns }}" class="cat-header">
                    KATEGORI: {{ strtoupper($kategori->nama_kategori) }} (BOBOT {{ $kategori->bobot_persen }}%)
                </td>
            </tr>

            <tr style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center;">
                <th rowspan="2" style="width: 40px; vertical-align: middle;">NO</th>
                <th rowspan="2" style="width: 280px; vertical-align: middle; text-align: left;">ASPEK PENILAIAN / GERAKAN</th>
                <th colspan="{{ $opsiCount }}" class="center">KATEGORI PENILAIAN JURI</th>
                <th rowspan="2" style="width: 100px; vertical-align: middle;">SKOR AKHIR</th>
            </tr>
            <tr class="subcat-header">
                @foreach($headerGroups as $group)
                    <td colspan="{{ $group['span'] }}" class="center bold" style="padding: 4px; font-size: 9px; border: 1px solid #cbd5e1; background-color: #334155; color: #ffffff;">
                        {{ $group['text'] }}
                    </td>
                @endforeach
            </tr>

            @forelse($kategori->items as $item)
                <tr class="item-bg">
                    <td class="center bold" style="background-color: #f8fafc;">{{ $item->urutan }}</td>
                    <td class="bold" style="color: #1e293b;">{{ $item->nama_gerakan }}</td>
                    
                    @php
                        $options = is_array($item->opsi_nilai) ? $item->opsi_nilai : [];
                    @endphp
                    @for($i = 0; $i < $opsiCount; $i++)
                        <td class="center" style="width: 35px; color: #475569; font-family: monospace;">
                            {{ isset($options[$i]) ? $options[$i] : '' }}
                        </td>
                    @endfor
                    
                    <td class="score-column center"></td>
                </tr>
            @empty
                <tr>
                    <td></td>
                    <td colspan="{{ 1 + $opsiCount + 1 }}" style="color: #94a3b8; font-style: italic; padding: 10px;">
                        Belum ada item format nilai pada kategori ini.
                    </td>
                </tr>
            @endforelse

            <tr><td colspan="{{ $totalColumns }}" style="border: none; height: 15px;"></td></tr>
        @endforeach

        <tr style="background-color: #1e293b; color: #ffffff; font-weight: bold;">
            <td colspan="2" style="text-align: right; padding: 10px; font-size: 11px;">
                TOTAL KESELURUHAN SKOR (REKAP PANITIA) :
            </td>
            <td colspan="{{ $opsiCount }}" style="background-color: #1e293b;"></td>
            <td class="score-column center" style="background-color: #fbbf24; color: #000000; font-size: 12px;"></td>
        </tr>
    </table>

    <br><br>
    <table style="width: 100%; border: none; font-family: Arial, sans-serif; font-size: 11px;">
        <tr>
            <td style="width: 65%; border: none;"></td>
            <td style="text-align: center; width: 35%; border: none;">
                Bekasi, .................................... 2026<br>
                Tanda Tangan Juri Lapangan,<br><br><br><br><br><br>
                ( ...................................................... )
            </td>
        </tr>
    </table>

</body>
</html>