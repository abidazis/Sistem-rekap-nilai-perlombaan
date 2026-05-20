<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lomba;
use App\Models\Peserta;
use App\Models\KategoriPenilaian;
use Livewire\Attributes\Layout;

class RekapJuara extends Component
{
    public $format_juara = 'all_harapan';
    public $tie_breakers = [];
    public $selected_lomba_id;
    public $selected_tingkat = 'SMP'; // 👈 DEFAULT TINGKAT

    public function mount()
    {
        $latest = Lomba::latest()->first();
        if($latest) {
            $this->selected_lomba_id = $latest->id;
            $this->loadPengaturan(); // Load settingan pas pertama buka
        }
    }

    public function updatedSelectedLombaId()
    {
        $this->loadPengaturan();
    }

    public function loadPengaturan()
    {
        $lomba = Lomba::find($this->selected_lomba_id);
        if($lomba) {
            $this->format_juara = $lomba->format_juara ?? 'all_harapan';
            $this->tie_breakers = is_array($lomba->tie_breakers) ? $lomba->tie_breakers : [];
        }
    }

    public function simpanPengaturan()
    {
        if ($this->selected_lomba_id) {
            Lomba::where('id', $this->selected_lomba_id)->update([
                'format_juara' => $this->format_juara,
                'tie_breakers' => $this->tie_breakers 
            ]);
            
            session()->flash('message_setting', 'Pengaturan Format & Tie-Breaker berhasil disimpan!');
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $pesertas = collect();
        $kategoris = collect(); 

        if ($this->selected_lomba_id) {
            // 1. Ambil Kategori dengan items
            $kategoris = KategoriPenilaian::where('lomba_id', $this->selected_lomba_id)
                            ->orderBy('bobot_persen', 'desc')
                            ->with('items')
                            ->get();
            
            // 2. Ambil Peserta sesuai tingkat
            $all_peserta = Peserta::where('lomba_id', $this->selected_lomba_id)
                            ->where('tingkat', $this->selected_tingkat)
                            ->with(['nilai', 'denda']) // Eager loading sangat penting
                            ->get();
            
            $tieBreakersAktif = $this->tie_breakers;

            $pesertas = $all_peserta->map(function($p) use ($kategoris) {
                $total_kotor = 0;
                $temp_skor_kategori = []; 
                
                // Pastikan nilai adalah koleksi, kalau null buat koleksi kosong
                $nilai_peserta = $p->nilai ?? collect();

                foreach($kategoris as $kat) {
                    // Pakai map untuk memastikan semua ID adalah integer
                    $item_ids = $kat->items->pluck('id')->map(fn($id) => (int)$id)->toArray();
                    
                    // Gunakan filter manual untuk memastikan perbandingan tipe data yang aman
                    $raw_score = $nilai_peserta->filter(function($n) use ($item_ids) {
                        return in_array((int)$n->item_penilaian_id, $item_ids);
                    })->sum('nilai');
                    
                    $temp_skor_kategori[$kat->id] = $raw_score; 
                    
                    // Logika pengecualian Kostum & Make Up
                    $nama_kat = strtolower($kat->nama_kategori);
                    if (!str_contains($nama_kat, 'kostum') && !str_contains($nama_kat, 'make up')) {
                        $total_kotor += $raw_score;
                    }
                }
                
                $p->skor_kategori = $temp_skor_kategori; 
                $p->total_minus = $p->denda ? $p->denda->sum('poin_minus') : 0;
                $p->total_skor = $total_kotor - $p->total_minus;
                
                return $p;
            })->sort(function($a, $b) use ($tieBreakersAktif) {
                // TIE-BREAKER LOGIC
                if ($a->total_skor != $b->total_skor) {
                    return $b->total_skor <=> $a->total_skor;
                }
                
                foreach ($tieBreakersAktif as $kat_id) {
                    $nilaiA = $a->skor_kategori[$kat_id] ?? 0;
                    $nilaiB = $b->skor_kategori[$kat_id] ?? 0;
                    if ($nilaiA != $nilaiB) {
                        return $nilaiB <=> $nilaiA;
                    }
                }
                return 0;
            })->values();
        }

        return view('livewire.rekap-juara', [
            'events' => Lomba::latest()->get(),
            'kategoris' => $kategoris, 
            'ranking_peserta' => $pesertas
        ]);
    }
}