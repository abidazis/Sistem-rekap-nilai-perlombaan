<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    public function cetakKategori($lomba_id)
    {
        $lomba = Lomba::findOrFail($lomba_id);
        $kategoris = KategoriPenilaian::where('lomba_id', $lomba_id)->with('items')->get();
        $all_peserta = Peserta::where('lomba_id', $lomba_id)->with('nilai')->get();

        $ranking_per_kategori = [];
        
        foreach($kategoris as $kat) {
            $item_ids = $kat->items->pluck('id');
            
            // Hitung dan urutkan peserta HANYA berdasarkan kategori ini
            $ranking = $all_peserta->map(function($p) use ($kat, $item_ids) {
                // Kloning object agar tidak bentrok antar kategori
                $peserta_clone = clone $p;
                $peserta_clone->skor_kategori = $p->nilai->whereIn('item_penilaian_id', $item_ids)->sum('nilai');
                return $peserta_clone;
            })->sortByDesc('skor_kategori')->values();
            
            $ranking_per_kategori[$kat->id] = $ranking;
        }

        return view('cetak.kategori', compact('lomba', 'kategoris', 'ranking_per_kategori'));
    }

    // Fungsi 5: Cetak Lembar Penilaian Juri (LJK) Kosong
    public function cetakLJK($lomba_id)
    {
        $lomba = Lomba::findOrFail($lomba_id);
        $kategoris = KategoriPenilaian::where('lomba_id', $lomba_id)->with('items')->get();

        return view('cetak.ljk', compact('lomba', 'kategoris'));
    }

    // Fungsi: Export Rekap Lengkap ke Excel
    public function exportExcel($lomba_id)
    {
        $lomba = Lomba::findOrFail($lomba_id);
        $kategoris = KategoriPenilaian::where('lomba_id', $lomba_id)->orderBy('bobot_persen', 'desc')->get();
        
        // Ambil semua peserta beserta nilainya
        $pesertas = Peserta::where('lomba_id', $lomba_id)->with('nilai', 'denda')->get()->map(function($p) use ($kategoris) {
            $skor_per_kategori = [];
            $total_kotor = 0;

            foreach($kategoris as $kat) {
                // Ambil semua ID item penilaian di kategori ini
                $item_ids = $kat->items->pluck('id');
                // Hitung total skor dari semua juri untuk kategori ini
                $skor = $p->nilai->whereIn('item_penilaian_id', $item_ids)->sum('nilai');
                
                $skor_per_kategori[$kat->id] = $skor;
                $total_kotor += $skor;
            }

            $total_minus = $p->denda->sum('poin_minus');
            
            // Simpan ke object peserta
            $p->skor_kategori = $skor_per_kategori;
            $p->total_kotor = $total_kotor;
            $p->total_minus = $total_minus;
            $p->grand_total = $total_kotor - $total_minus;
            
            return $p;
        })->sortByDesc('grand_total')->values();

        // 2 Baris Ajaib untuk memaksa browser mendownloadnya sebagai file Excel (.xls)
        $filename = "ARSIP_REKAP_" . strtoupper(str_replace(' ', '_', $lomba->nama_lomba)) . ".xls";
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        return view('cetak.excel', compact('lomba', 'pesertas', 'kategoris'));
    }
}