<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Lomba;
use App\Models\Peserta;
use App\Models\KategoriPenilaian;


class CetakController extends Controller
{
    // Fungsi 1: Cetak Seluruh Klasemen Lomba
    public function cetakKlasemen($lomba_id)
    {
        $lomba = Lomba::findOrFail($lomba_id);
        $kategoris = KategoriPenilaian::where('lomba_id', $lomba_id)->with('items')->get();
        $all_peserta = Peserta::where('lomba_id', $lomba_id)->with(['nilai', 'denda'])->get();
        
        $ranking = $all_peserta->map(function($p) use ($kategoris) {
            $total_nilai_murni = 0;
            
            // 1. GUNAKAN ARRAY SEMENTARA DI SINI
            $temp_skor_kategori = []; 
            
            foreach($kategoris as $kat) {
                $item_ids = $kat->items->pluck('id');
                // Nilai murni kotor
                $raw_score = $p->nilai->whereIn('item_penilaian_id', $item_ids)->sum('nilai');
                
                // 2. MASUKKAN KE WADAH SEMENTARA
                $temp_skor_kategori[$kat->id] = $raw_score; 
                $total_nilai_murni += $raw_score;
            }
            
            // 3. BARU TIMPA KE PROPERTI MODELNYA
            $p->skor_kategori = $temp_skor_kategori; 
            
            $p->total_minus = $p->denda->sum('poin_minus');
            $p->keterangan_denda = $p->denda->pluck('keterangan')->implode(', '); 
            
            $p->total_skor = $total_nilai_murni - $p->total_minus;
            return $p;
        })->sortByDesc('total_skor')->values();

        return view('cetak.klasemen', compact('lomba', 'kategoris', 'ranking'));
    }

    // Fungsi 2: Cetak Rincian 1 Peserta
    public function cetakPeserta($peserta_id)
    {
        $peserta = Peserta::with(['lomba', 'denda', 'nilai.item.kategori', 'nilai.juri'])->findOrFail($peserta_id);
        $kategoris = KategoriPenilaian::where('lomba_id', $peserta->lomba_id)->with('items')->get();
        
        return view('cetak.peserta', compact('peserta', 'kategoris'));
    }

    // Fungsi 3: Cetak Juara Utama (Kombinasi Kategori Dinamis)
    public function cetakUtama(Request $request, $lomba_id)
    {
        $lomba = Lomba::findOrFail($lomba_id);
        
        // Ambil array ID kategori yang dicentang oleh user
        $selected_kategori_ids = $request->input('kategori', []); 

        if(empty($selected_kategori_ids)) {
            return "<script>alert('Pilih minimal 1 kategori bro!'); window.close();</script>";
        }

        // Hanya ambil kategori yang dipilih
        $kategoris = KategoriPenilaian::whereIn('id', $selected_kategori_ids)->with('items')->get();
        $all_peserta = Peserta::where('lomba_id', $lomba_id)->with(['nilai', 'denda'])->get();
        
        $ranking = $all_peserta->map(function($p) use ($kategoris) {
            $total_nilai_utama = 0;
            
            // Hitung nilai HANYA dari kategori yang dipilih
            foreach($kategoris as $kat) {
                $item_ids = $kat->items->pluck('id');
                $raw_score = $p->nilai->whereIn('item_penilaian_id', $item_ids)->sum('nilai');
                $total_nilai_utama += $raw_score;
            }
            
            $p->total_minus = $p->denda->sum('poin_minus');
            $p->keterangan_denda = $p->denda->pluck('keterangan')->implode(', '); 
            
            // Perhitungan akhir sesuai format Pandawa
            $p->total_kotor = $total_nilai_utama;
            $p->grand_total = $total_nilai_utama - $p->total_minus;
            
            return $p;
        })->sortByDesc('grand_total')->values();

        // Buat string nama kategori yang dipilih untuk dicetak di kertas
        $nama_kategori_dipilih = $kategoris->pluck('nama_kategori')->implode(' + ');

        return view('cetak.utama', compact('lomba', 'ranking', 'nama_kategori_dipilih'));
    }

    // Fungsi 4: Cetak Khusus Juara Per Kategori
    public function cetakKategori($lomba_id, $tingkat)
    {
        $lomba = \App\Models\Lomba::findOrFail($lomba_id);
        $kategoris = \App\Models\KategoriPenilaian::where('lomba_id', $lomba_id)->get();
        
        // Ambil peserta khusus tingkat yang dipilih saja
        $pesertas = \App\Models\Peserta::where('lomba_id', $lomba_id)
                                       ->where('tingkat', $tingkat)
                                       ->with('nilai')
                                       ->get();

        $ranking_per_kategori = [];

        foreach ($kategoris as $kat) {
            $item_ids = $kat->items->pluck('id');
            
            $ranked = $pesertas->map(function($p) use ($item_ids) {
                // WAJIB CLONE: Agar skor kategori satu tidak menimpa skor kategori lain di memori
                $pesertaClone = clone $p; 
                $pesertaClone->skor_kategori = $p->nilai->whereIn('item_penilaian_id', $item_ids)->sum('nilai');
                return $pesertaClone;
            })->sortByDesc('skor_kategori')->values();

            $ranking_per_kategori[$kat->id] = $ranked;
        }
        $filename = "REKAP_KATEGORI_" . strtoupper($tingkat) . "_" . strtoupper(str_replace(' ', '_', $lomba->nama_lomba)) . ".xls";
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        
        return view('cetak.kategori', compact('lomba', 'kategoris', 'ranking_per_kategori', 'tingkat'));
    }

    // Fungsi 5: Cetak Lembar Penilaian Juri (LJK) Kosong
    public function cetakLJK($lomba_id)
    {
        $lomba = Lomba::findOrFail($lomba_id);
        $kategoris = KategoriPenilaian::where('lomba_id', $lomba_id)->with('items')->get();

        return view('cetak.ljk', compact('lomba', 'kategoris'));
    }

    // SUNTIKAN FUNGSI PENENTU PREDIKAT KLASIK PASKIBRA
    private function getPredikatJuara($rank, $format = 'all_harapan') {
        $tingkat = ['UTAMA', 'MADYA', 'BINA', 'MULA', 'PURWA', 'CARAKA', 'POTENSIAL', 'PERINTIS', 'PEJUANG'];
        $rank--; // Jadikan index 0 (0 = Juara 1)

        if ($format == 'all_harapan') {
            // Format All Trophy: UTAMA 123, Harapan UTAMA 123, MADYA 123, Harapan MADYA 123, dst.
            $idxTingkat = floor($rank / 6);
            if ($idxTingkat >= count($tingkat)) return "FINALIS " . ($rank + 1);

            $posisi = $rank % 6; // 0,1,2 (Juara) - 3,4,5 (Harapan)
            $nama = $tingkat[$idxTingkat];

            if ($posisi < 3) return $nama . " " . ($posisi + 1);
            return "HARAPAN " . $nama . " " . ($posisi - 2);
        } else {
            // Format Standard: Utama 123, Harapan Utama 123, lalu Madya 123, Bina 123 (tanpa harapan di bawah)
            if ($rank < 3) return "UTAMA " . ($rank + 1);
            if ($rank < 6) return "HARAPAN UTAMA " . ($rank - 2);

            $idxTingkat = floor(($rank - 6) / 3) + 1; // index 1 = MADYA
            if ($idxTingkat >= count($tingkat)) return "FINALIS " . ($rank + 1);

            $posisi = ($rank % 3) + 1;
            return $tingkat[$idxTingkat] . " " . $posisi;
        }
    }

    public function cetakPengumumanExcel($lomba_id, $tingkat)
    {
        // 1. AMBIL DATA DAN LOGIKA SAKTI YANG SAMA PERSIS DENGAN PDF
        $lomba = \App\Models\Lomba::findOrFail($lomba_id);
        $kategoris = \App\Models\KategoriPenilaian::where('lomba_id', $lomba_id)->get();
        $pesertas = \App\Models\Peserta::where('lomba_id', $lomba_id)->where('tingkat', $tingkat)->with('nilai', 'denda')->get();

        $tieBreakersAktif = is_array($lomba->tie_breakers) ? array_map('intval', array_filter($lomba->tie_breakers)) : [];
        $tb_kategoris = collect();
        foreach($tieBreakersAktif as $tb_id) {
            $k = $kategoris->where('id', $tb_id)->first();
            if($k) $tb_kategoris->push($k);
        }

        // 2. SORTING KLASEMEN UTAMA
        $ranked = $pesertas->map(function($p) use ($kategoris) {
            $total_kotor = 0; $skor_kat = [];
            foreach($kategoris as $kat) {
                $s = $p->nilai->whereIn('item_penilaian_id', $kat->items->pluck('id'))->sum('nilai');
                $skor_kat[$kat->id] = $s;
                if($kat->is_utama) $total_kotor += $s;
            }
            $p->grand_total = $total_kotor - $p->denda->sum('poin_minus');
            $p->skor_kategori = $skor_kat;
            return $p;
        })->sort(function($a, $b) use ($tieBreakersAktif) {
            if ($a->grand_total != $b->grand_total) return $b->grand_total <=> $a->grand_total;
            foreach ($tieBreakersAktif as $kat_id) {
                $nA = $a->skor_kategori[$kat_id] ?? 0;
                $nB = $b->skor_kategori[$kat_id] ?? 0;
                if ($nA != $nB) return $nB <=> $nA;
            }
            return 0;
        })->values();

        // 3. SORTING JUARA UMUM
        $juaraUmum = $ranked->map(function($p) use ($kategoris) {
            $total_umum = 0;
            foreach($kategoris as $kat) {
                if($kat->is_umum) $total_umum += ($p->skor_kategori[$kat->id] ?? 0);
            }
            $p_umum = clone $p; $p_umum->skor_akhir_umum = $total_umum; return $p_umum;
        })->sort(function($a, $b) use ($tieBreakersAktif) {
            if ($a->skor_akhir_umum != $b->skor_akhir_umum) return $b->skor_akhir_umum <=> $a->skor_akhir_umum;
            foreach ($tieBreakersAktif as $tb_id) {
                $nA = $a->skor_kategori[$tb_id] ?? 0; $nB = $b->skor_kategori[$tb_id] ?? 0;
                if ($nA != $nB) return $nB <=> $nA;
            }
            return $b->grand_total <=> $a->grand_total;
        })->values()->take(3); // Ambil Top 3 untuk Excel

        // 4. SORTING BEST KATEGORI
        $bestCategories = [];
        foreach($kategoris as $kat) {
            $sorted = $ranked->map(function($p) use ($kat) {
                $p_kat = clone $p; $p_kat->skor_spesifik = $p->skor_kategori[$kat->id] ?? 0; return $p_kat;
            })->sort(function($a, $b) use ($tieBreakersAktif, $kat) {
                if ($a->skor_spesifik != $b->skor_spesifik) return $b->skor_spesifik <=> $a->skor_spesifik;
                foreach ($tieBreakersAktif as $tb_id) {
                    if ($tb_id == $kat->id) continue; 
                    $nA = $a->skor_kategori[$tb_id] ?? 0; $nB = $b->skor_kategori[$tb_id] ?? 0;
                    if ($nA != $nB) return $nB <=> $nA;
                }
                return $b->grand_total <=> $a->grand_total;
            })->values()->take(3);

            $bestCategories[] = ['kategori' => $kat, 'pesertas' => $sorted];
        }

        // 5. AMBIL DATA JUARA SPESIAL (JALUR VIP / INPUT MANUAL)
        $kategoriSpesials = \App\Models\KategoriPenilaian::where('lomba_id', $lomba_id)
                            ->where('is_tersendiri', true)->get();
        
        $juaraSpesials = [];
        foreach ($kategoriSpesials as $ks) {
            $winners = \App\Models\PemenangSpesial::where('lomba_id', $lomba_id)
                        ->where('tingkat', $tingkat)
                        ->where('kategori_penilaian_id', $ks->id)
                        ->orderBy('rank', 'asc')
                        ->with('peserta')
                        ->get();
            
            if ($winners->count() > 0) {
                $juaraSpesials[] = [
                    'kategori' => $ks,
                    'pemenang' => $winners
                ];
            }
        }

        // 6. RENDER KE FORMAT EXCEL
        $filename = "REKAP_JUARA_" . strtoupper($tingkat) . "_" . date('Ymd') . ".xls";
        return response(view('cetak.pengumuman-excel', compact('lomba', 'tingkat', 'ranked', 'juaraUmum', 'bestCategories', 'tb_kategoris', 'juaraSpesials')))
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportPengumuman($lomba_id, $tingkat)
    {
        $lomba = \App\Models\Lomba::findOrFail($lomba_id);
        $kategoris = \App\Models\KategoriPenilaian::where('lomba_id', $lomba_id)->get();
        $tieBreakers = is_array($lomba->tie_breakers) ? $lomba->tie_breakers : [];
        
        $all_peserta = \App\Models\Peserta::where('lomba_id', $lomba_id)
                        ->where('tingkat', $tingkat)
                        ->with('nilai', 'denda')->get();

        // 1. HITUNG KLASEMEN GRAND TOTAL
        $pesertaGrandTotal = $all_peserta->map(function($p) use ($kategoris) {
            $total_kotor = 0; $skor_kategori = [];
            foreach($kategoris as $kat) {
                $skor = $p->nilai->whereIn('item_penilaian_id', $kat->items->pluck('id'))->sum('nilai');
                $skor_kategori[$kat->id] = $skor;
                if ($kat->is_utama) $total_kotor += $skor;
            }
            $p->skor_kategori = $skor_kategori; $p->total_kotor = $total_kotor;
            $p->total_minus = $p->denda->sum('poin_minus');
            $p->grand_total = $total_kotor - $p->total_minus;
            return $p;
        })->sort(function($a, $b) use ($tieBreakers) {
            if ($a->grand_total != $b->grand_total) return $b->grand_total <=> $a->grand_total; 
            foreach ($tieBreakers as $kat_id) {
                $nilaiA = $a->skor_kategori[$kat_id] ?? 0; $nilaiB = $b->skor_kategori[$kat_id] ?? 0;
                if ($nilaiA != $nilaiB) return $nilaiB <=> $nilaiA; 
            }
            return 0; 
        })->values();

        // Labeli Predikat
        $format = $lomba->format_juara ?? 'all_harapan';
        $pesertaGrandTotal = $pesertaGrandTotal->map(function($p, $idx) use ($format) {
            $urutan = $idx + 1;
            if($urutan <= 3) $predikat = "UTAMA $urutan";
            elseif($urutan <= 6) $predikat = "HARAPAN " . ($urutan-3);
            elseif($urutan <= 9) $predikat = "MADYA " . ($urutan-6);
            elseif($urutan <= 12) $predikat = "BINA " . ($urutan-9);
            elseif($urutan <= 15) $predikat = "MULA " . ($urutan-12);
            else $predikat = "PURWA " . ($urutan-15);
            
            $p->predikat_juara = $predikat; return $p;
        });

        // 2. HITUNG JUARA PER KATEGORI 
        $rankingKategori = [];
        foreach($kategoris as $kat) {
            if ($kat->is_tersendiri) continue; // Skip Kategori Tersendiri di sini
            $rankingKategori[$kat->nama_kategori] = $all_peserta->map(function($p) use ($kat) {
                $p_clone = clone $p;
                $p_clone->skor_spesifik = $p->nilai->whereIn('item_penilaian_id', $kat->items->pluck('id'))->sum('nilai');
                return $p_clone;
            })->sortByDesc('skor_spesifik')->values()->take(3);
        }

        // 3. HITUNG JUARA UMUM
        $pesertaUmum = $all_peserta->map(function($p) use ($kategoris) {
            $total_umum = 0;
            foreach($kategoris as $kat) {
                if ($kat->is_umum) $total_umum += $p->nilai->whereIn('item_penilaian_id', $kat->items->pluck('id'))->sum('nilai');
            }
            $p_clone = clone $p; $p_clone->skor_umum = $total_umum; return $p_clone;
        })->sortByDesc('skor_umum')->values()->take(3);

        // 4. AMBIL JUARA SPESIAL (JALUR MANUAL)
        $kategoriSpesials = \App\Models\KategoriPenilaian::where('lomba_id', $lomba_id)->where('is_tersendiri', true)->get();
        $juaraSpesials = [];
        foreach ($kategoriSpesials as $ks) {
            $winners = \App\Models\PemenangSpesial::where('lomba_id', $lomba_id)->where('tingkat', $tingkat)
                        ->where('kategori_penilaian_id', $ks->id)->orderBy('rank', 'asc')->with('peserta')->get();
            if ($winners->count() > 0) $juaraSpesials[] = ['kategori' => $ks, 'pemenang' => $winners];
        }

        $filename = "LEMBAR_MC_PENGUMUMAN_" . strtoupper($tingkat) . "_" . strtoupper(str_replace(' ', '_', $lomba->nama_lomba)) . ".xls";
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        return view('cetak.pengumuman-excel', compact('lomba', 'tingkat', 'pesertaGrandTotal', 'rankingKategori', 'pesertaUmum', 'juaraSpesials'));
    }

    public function cetakPengumumanPDF($lomba_id, $tingkat)
    {
        $lomba = \App\Models\Lomba::findOrFail($lomba_id);
        $kategoris = \App\Models\KategoriPenilaian::where('lomba_id', $lomba_id)->get();
        $pesertas = \App\Models\Peserta::where('lomba_id', $lomba_id)
                    ->where('tingkat', $tingkat)->with('nilai', 'denda')->get();

        // 1. TIE BREAKER
        $tieBreakersAktif = is_array($lomba->tie_breakers) ? array_map('intval', array_filter($lomba->tie_breakers)) : [];
        $tb_kategoris = collect();
        foreach($tieBreakersAktif as $tb_id) {
            $k = $kategoris->where('id', $tb_id)->first();
            if($k) $tb_kategoris->push($k);
        }

        // 2. SORTING RANKING UTAMA
        $ranked = $pesertas->map(function($p) use ($kategoris) {
            $total_kotor = 0; $skor_kat = [];
            foreach($kategoris as $kat) {
                $s = $p->nilai->whereIn('item_penilaian_id', $kat->items->pluck('id'))->sum('nilai');
                $skor_kat[$kat->id] = $s;
                if($kat->is_utama) $total_kotor += $s;
            }
            $p->grand_total = $total_kotor - $p->denda->sum('poin_minus');
            $p->skor_kategori = $skor_kat; return $p;
        })->sort(function($a, $b) use ($tieBreakersAktif) {
            if ($a->grand_total != $b->grand_total) return $b->grand_total <=> $a->grand_total;
            foreach ($tieBreakersAktif as $kat_id) {
                $nA = $a->skor_kategori[$kat_id] ?? 0; $nB = $b->skor_kategori[$kat_id] ?? 0;
                if ($nA != $nB) return $nB <=> $nA;
            }
            return 0;
        })->values();

        // 3. JUARA UMUM
        $juaraUmum = $ranked->map(function($p) use ($kategoris) {
            $total_umum = 0;
            foreach($kategoris as $kat) {
                if($kat->is_umum) $total_umum += ($p->skor_kategori[$kat->id] ?? 0);
            }
            $p_umum = clone $p; $p_umum->skor_akhir_umum = $total_umum; return $p_umum;
        })->sort(function($a, $b) use ($tieBreakersAktif) {
            if ($a->skor_akhir_umum != $b->skor_akhir_umum) return $b->skor_akhir_umum <=> $a->skor_akhir_umum;
            foreach ($tieBreakersAktif as $tb_id) {
                $nA = $a->skor_kategori[$tb_id] ?? 0; $nB = $b->skor_kategori[$tb_id] ?? 0;
                if ($nA != $nB) return $nB <=> $nA;
            }
            return $b->grand_total <=> $a->grand_total;
        })->values()->take(1);

        // 4. BEST KATEGORI
        $bestCategories = [];
        foreach($kategoris as $kat) {
            if ($kat->is_tersendiri) continue; // Skip Kategori Tersendiri di sini
            $sorted = $ranked->map(function($p) use ($kat) {
                $p_kat = clone $p; $p_kat->skor_spesifik = $p->skor_kategori[$kat->id] ?? 0; return $p_kat;
            })->sort(function($a, $b) use ($tieBreakersAktif, $kat) {
                if ($a->skor_spesifik != $b->skor_spesifik) return $b->skor_spesifik <=> $a->skor_spesifik;
                foreach ($tieBreakersAktif as $tb_id) {
                    if ($tb_id == $kat->id) continue; 
                    $nA = $a->skor_kategori[$tb_id] ?? 0; $nB = $b->skor_kategori[$tb_id] ?? 0;
                    if ($nA != $nB) return $nB <=> $nA;
                }
                return $b->grand_total <=> $a->grand_total;
            })->values()->take(3);
            $bestCategories[] = ['kategori' => $kat, 'pesertas' => $sorted];
        }

        // 5. AMBIL JUARA SPESIAL (JALUR MANUAL)
        $kategoriSpesials = \App\Models\KategoriPenilaian::where('lomba_id', $lomba_id)->where('is_tersendiri', true)->get();
        $juaraSpesials = [];
        foreach ($kategoriSpesials as $ks) {
            $winners = \App\Models\PemenangSpesial::where('lomba_id', $lomba_id)->where('tingkat', $tingkat)
                        ->where('kategori_penilaian_id', $ks->id)->orderBy('rank', 'asc')->with('peserta')->get();
            if ($winners->count() > 0) $juaraSpesials[] = ['kategori' => $ks, 'pemenang' => $winners];
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cetak.pengumuman-pdf', compact('lomba', 'tingkat', 'ranked', 'juaraUmum', 'bestCategories', 'tb_kategoris', 'juaraSpesials'));
        $pdf->setPaper('A4', 'portrait'); 
        return $pdf->stream("PENGUMUMAN_JUARA_".strtoupper($tingkat).".pdf");
    }
}