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
}