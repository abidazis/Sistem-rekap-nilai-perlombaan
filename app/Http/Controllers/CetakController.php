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

    // FUNGSI EXPORT EXCEL YANG SUDAH DI-UPGRADE
    public function exportExcel($lomba_id, $tingkat)
    {
        $lomba = \App\Models\Lomba::findOrFail($lomba_id);
        $kategoris = \App\Models\KategoriPenilaian::where('lomba_id', $lomba_id)->orderBy('bobot_persen', 'desc')->get();
        $tieBreakers = is_array($lomba->tie_breakers) ? $lomba->tie_breakers : [];
        
        // HANYA AMBIL PESERTA SESUAI TINGKAT (SD/SMP/SMA)
        $pesertas = \App\Models\Peserta::where('lomba_id', $lomba_id)
                        ->where('tingkat', $tingkat)
                        ->with('nilai', 'denda')->get()->map(function($p) use ($kategoris) {
            $total_kotor = 0;
            $skor_per_kategori = [];
            
            foreach($kategoris as $kat) {
                $item_ids = $kat->items->pluck('id');
                $skor = $p->nilai->whereIn('item_penilaian_id', $item_ids)->sum('nilai');
                $skor_per_kategori[$kat->id] = $skor;
                $total_kotor += $skor;
            }

            $p->skor_kategori = $skor_per_kategori;
            $p->total_kotor = $total_kotor;
            $p->total_minus = $p->denda->sum('poin_minus');
            $p->grand_total = $total_kotor - $p->total_minus;
            
            return $p;
        });

        // TIE-BREAKER LOGIC SAKTI
        $pesertas = $pesertas->sort(function($a, $b) use ($tieBreakers) {
            if ($a->grand_total != $b->grand_total) return $b->grand_total <=> $a->grand_total; 
            
            foreach ($tieBreakers as $kat_id) {
                $nilaiA = $a->skor_kategori[$kat_id] ?? 0;
                $nilaiB = $b->skor_kategori[$kat_id] ?? 0;
                if ($nilaiA != $nilaiB) return $nilaiB <=> $nilaiA; 
            }
            return 0; 
        })->values(); 

        $format = $lomba->format_juara ?? 'all_harapan';
        $pesertas = $pesertas->map(function($p, $idx) use ($format) {
            $p->predikat_juara = $this->getPredikatJuara($idx + 1, $format);
            return $p;
        });

        $filename = "REKAP_KLASEMEN_" . strtoupper($tingkat) . "_" . strtoupper(str_replace(' ', '_', $lomba->nama_lomba)) . ".xls";
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        return view('cetak.excel', compact('lomba', 'pesertas', 'kategoris', 'tieBreakers', 'tingkat'));
    }

    public function exportPengumuman($lomba_id, $tingkat)
    {
        $lomba = \App\Models\Lomba::findOrFail($lomba_id);
        $kategoris = \App\Models\KategoriPenilaian::where('lomba_id', $lomba_id)->get();
        $tieBreakers = is_array($lomba->tie_breakers) ? $lomba->tie_breakers : [];
        
        $all_peserta = \App\Models\Peserta::where('lomba_id', $lomba_id)
                        ->where('tingkat', $tingkat)
                        ->with('nilai', 'denda')->get();

        // 1. HITUNG KLASEMEN GRAND TOTAL (UTAMA, HARAPAN, MADYA, BINA)
        $pesertaGrandTotal = $all_peserta->map(function($p) use ($kategoris) {
            $total_kotor = 0;
            $skor_kategori = [];
            foreach($kategoris as $kat) {
                $skor = $p->nilai->whereIn('item_penilaian_id', $kat->items->pluck('id'))->sum('nilai');
                $skor_kategori[$kat->id] = $skor;
                if ($kat->is_utama) { // Hanya jumlahkan yang is_utama = true
                    $total_kotor += $skor;
                }
            }
            $p->skor_kategori = $skor_kategori;
            $p->total_kotor = $total_kotor;
            $p->total_minus = $p->denda->sum('poin_minus');
            $p->grand_total = $total_kotor - $p->total_minus;
            return $p;
        })->sort(function($a, $b) use ($tieBreakers) {
            if ($a->grand_total != $b->grand_total) return $b->grand_total <=> $a->grand_total; 
            foreach ($tieBreakers as $kat_id) {
                $nilaiA = $a->skor_kategori[$kat_id] ?? 0;
                $nilaiB = $b->skor_kategori[$kat_id] ?? 0;
                if ($nilaiA != $nilaiB) return $nilaiB <=> $nilaiA; 
            }
            return 0; 
        })->values();

        // Labeli Predikat (Fungsi getPredikatJuara harus ada di controller ini)
        $format = $lomba->format_juara ?? 'all_harapan';
        $pesertaGrandTotal = $pesertaGrandTotal->map(function($p, $idx) use ($format) {
            // Jika tidak ada fungsi getPredikatJuara, kita buat manual sederhana di sini
            $urutan = $idx + 1;
            if($urutan <= 3) $predikat = "UTAMA $urutan";
            elseif($urutan <= 6) $predikat = "HARAPAN " . ($urutan-3);
            elseif($urutan <= 9) $predikat = "MADYA " . ($urutan-6);
            elseif($urutan <= 12) $predikat = "BINA " . ($urutan-9);
            elseif($urutan <= 15) $predikat = "MULA " . ($urutan-12);
            else $predikat = "PURWA " . ($urutan-15);
            
            $p->predikat_juara = $predikat;
            return $p;
        });

        // 2. HITUNG JUARA PER KATEGORI (BEST PBB, VAFOR, KOSTUM, DLL)
        $rankingKategori = [];
        foreach($kategoris as $kat) {
            $rankingKategori[$kat->nama_kategori] = $all_peserta->map(function($p) use ($kat) {
                $p_clone = clone $p;
                $p_clone->skor_spesifik = $p->nilai->whereIn('item_penilaian_id', $kat->items->pluck('id'))->sum('nilai');
                return $p_clone;
            })->sortByDesc('skor_spesifik')->values()->take(3); // Ambil Top 3 Saja
        }

        // 3. HITUNG JUARA UMUM (Berdasarkan centangan is_umum)
        $pesertaUmum = $all_peserta->map(function($p) use ($kategoris) {
            $total_umum = 0;
            foreach($kategoris as $kat) {
                if ($kat->is_umum) {
                    $total_umum += $p->nilai->whereIn('item_penilaian_id', $kat->items->pluck('id'))->sum('nilai');
                }
            }
            $p_clone = clone $p;
            $p_clone->skor_umum = $total_umum;
            return $p_clone;
        })->sortByDesc('skor_umum')->values()->take(3);

        $filename = "LEMBAR_MC_PENGUMUMAN_" . strtoupper($tingkat) . "_" . strtoupper(str_replace(' ', '_', $lomba->nama_lomba)) . ".xls";
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        return view('cetak.pengumuman-excel', compact('lomba', 'tingkat', 'pesertaGrandTotal', 'rankingKategori', 'pesertaUmum'));
    }

    public function cetakPengumumanPDF($lomba_id, $tingkat)
    {
        $lomba = \App\Models\Lomba::findOrFail($lomba_id);
        $kategoris = \App\Models\KategoriPenilaian::where('lomba_id', $lomba_id)->get();
        $pesertas = \App\Models\Peserta::where('lomba_id', $lomba_id)
                    ->where('tingkat', $tingkat)
                    ->with('nilai', 'denda')->get();

        // 1. HITUNG SKOR & RANKING UTAMA (KOLOM KIRI)
        $ranked = $pesertas->map(function($p) use ($kategoris) {
            $total_kotor = 0;
            $skor_kat = [];
            foreach($kategoris as $kat) {
                $s = $p->nilai->whereIn('item_penilaian_id', $kat->items->pluck('id'))->sum('nilai');
                $skor_kat[$kat->id] = $s;
                if($kat->is_utama) $total_kotor += $s;
            }
            $p->grand_total = $total_kotor - $p->denda->sum('poin_minus');
            $p->skor_kategori = $skor_kat;
            return $p;
        })->sortByDesc('grand_total')->values();

        // 2. HITUNG JUARA UMUM (KOLOM KANAN)
        $juaraUmum = $pesertas->map(function($p) use ($kategoris) {
            $total_umum = 0;
            foreach($kategoris as $kat) {
                if($kat->is_umum) {
                    $total_umum += $p->nilai->whereIn('item_penilaian_id', $kat->items->pluck('id'))->sum('nilai');
                }
            }
            $p_umum = clone $p;
            $p_umum->skor_akhir_umum = $total_umum;
            return $p_umum;
        })->sortByDesc('skor_akhir_umum')->values()->take(3);

        // 3. AMBIL JUARA PER KATEGORI (BEST PBB, VAFOR, DLL)
        $bestCategories = [];
        foreach($kategoris as $kat) {
            $item_ids = $kat->items->pluck('id');
            
            // Urutkan peserta berdasarkan kategori ini saja
            $bestCategories[$kat->nama_kategori] = $pesertas->map(function($p) use ($item_ids) {
                $p_kat = clone $p;
                $p_kat->skor_spesifik = $p->nilai->whereIn('item_penilaian_id', $item_ids)->sum('nilai');
                return $p_kat;
            })->sortByDesc('skor_spesifik')->values()->take(3); // Ambil Top 3 sebagai List
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cetak.pengumuman-pdf', compact('lomba', 'tingkat', 'ranked', 'juaraUmum', 'bestCategories'));
        $pdf->setPaper('A4', 'portrait'); 
        return $pdf->stream("PENGUMUMAN_JUARA_".strtoupper($tingkat).".pdf");
    }
}