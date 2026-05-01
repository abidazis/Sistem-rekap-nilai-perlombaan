<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lomba;
use App\Models\Peserta;
use App\Models\KategoriPenilaian;
use Livewire\Attributes\Layout;

class RekapJuara extends Component
{
    public $selected_lomba_id;

    public function mount()
    {
        $latest = Lomba::latest()->first();
        if($latest) {
            $this->selected_lomba_id = $latest->id;
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $pesertas = collect();
        $kategoris = collect(); 

        if ($this->selected_lomba_id) {
            $kategoris = KategoriPenilaian::where('lomba_id', $this->selected_lomba_id)->with('items')->get();
            $all_peserta = Peserta::where('lomba_id', $this->selected_lomba_id)->with(['nilai', 'denda'])->get();
            
            $pesertas = $all_peserta->map(function($p) use ($kategoris) {
                $total_nilai_murni = 0;
                
                // 1. Buat variabel array sementara (Bukan langsung property $p)
                $temp_skor_kategori = []; 
                
                foreach($kategoris as $kat) {
                    $item_ids = $kat->items->pluck('id');
                    $raw_score = $p->nilai->whereIn('item_penilaian_id', $item_ids)->sum('nilai');
                    $weighted_score = $raw_score * ($kat->bobot_persen / 100);
                    
                    // 2. Simpan ke array sementara dulu
                    $temp_skor_kategori[$kat->id] = $weighted_score; 
                    
                    $total_nilai_murni += $weighted_score;
                }
                
                // 3. Masukkan array utuh ke dalam property model
                $p->skor_kategori = $temp_skor_kategori; 
                
                $p->total_minus = $p->denda->sum('poin_minus');
                $p->total_skor = $total_nilai_murni - $p->total_minus;
                
                return $p;
            })->sortByDesc('total_skor')->values();
        }

        return view('livewire.rekap-juara', [
            'events' => Lomba::latest()->get(),
            'kategoris' => $kategoris, 
            'ranking_peserta' => $pesertas
        ]);
    }
}