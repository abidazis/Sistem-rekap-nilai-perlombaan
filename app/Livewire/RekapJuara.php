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
    public $selected_tingkat = 'SMP';
    public $mode_tampilan = 'utama'; // 👈 DEFAULT FILTER LEADERBOARD
    
    public $format_juara = 'all_harapan';
    public $tie_breakers = []; 

    public function mount()
    {
        $latest = Lomba::latest()->first();
        if($latest) {
            $this->selected_lomba_id = $latest->id;
            $this->loadPengaturan();
        }
    }

    public function updatedSelectedLombaId() { $this->loadPengaturan(); }
    
    // Saat ganti mode, kembalikan ke default jika perlu (opsional)
    public function updatedModeTampilan() {}

    public function loadPengaturan()
    {
        $lomba = Lomba::find($this->selected_lomba_id);
        if($lomba) {
            $this->format_juara = $lomba->format_juara ?? 'all_harapan';
            $this->tie_breakers = is_array($lomba->tie_breakers) ? $lomba->tie_breakers : [];
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $pesertas = collect();
        $semua_kategori = collect(); 
        $kolom_kategori_tampil = collect(); // Kolom yang muncul di tabel sesuai mode

        if ($this->selected_lomba_id) {
            // Ambil semua kategori
            $semua_kategori = KategoriPenilaian::where('lomba_id', $this->selected_lomba_id)
                            ->orderBy('bobot_persen', 'desc')
                            ->with('items')->get();
            
            // 1. FILTER KOLOM YANG TAMPIL DI TABEL BERDASARKAN MODE
            if ($this->mode_tampilan == 'utama') {
                $kolom_kategori_tampil = $semua_kategori->where('is_utama', true);
            } elseif ($this->mode_tampilan == 'umum') {
                $kolom_kategori_tampil = $semua_kategori->where('is_umum', true);
            } else {
                // Mode spesifik 1 kategori (misal milih "PBB" saja)
                $kolom_kategori_tampil = $semua_kategori->where('id', $this->mode_tampilan);
            }

            // Ambil Peserta
            $all_peserta = Peserta::where('lomba_id', $this->selected_lomba_id)
                            ->where('tingkat', $this->selected_tingkat)
                            ->with(['nilai', 'denda'])->get();
            
            // Pastikan tie breakers di-casting ke Integer agar bisa nyambung saat nyari ID
            $tieBreakersAktif = array_map('intval', array_filter($this->tie_breakers));

            $pesertas = $all_peserta->map(function($p) use ($semua_kategori, $kolom_kategori_tampil) {
                $total_kotor_mode_ini = 0;
                $temp_skor_kategori = []; 
                $nilai_peserta = $p->nilai ?? collect();

                // Hitung nilai SEMUA kategori untuk jaga-jaga Tie-Breaker
                foreach($semua_kategori as $kat) {
                    $item_ids = $kat->items->pluck('id')->map(fn($id) => (int)$id)->toArray();
                    $raw_score = $nilai_peserta->filter(function($n) use ($item_ids) {
                        return in_array((int)$n->item_penilaian_id, $item_ids);
                    })->sum('nilai');
                    
                    $temp_skor_kategori[$kat->id] = $raw_score; 
                }

                // Hitung Total Kotor HANYA berdasarkan kolom yang sedang tampil di Mode saat ini
                foreach($kolom_kategori_tampil as $kat_tampil) {
                    $total_kotor_mode_ini += $temp_skor_kategori[$kat_tampil->id];
                }
                
                $p->skor_kategori = $temp_skor_kategori; 
                $p->total_minus = $p->denda ? $p->denda->sum('poin_minus') : 0;
                
                // Jika mode kategori spesifik, tidak usah kurangi minus. Minus hanya untuk Utama/Umum
                if ($this->mode_tampilan != 'utama' && $this->mode_tampilan != 'umum') {
                    $p->total_skor = $total_kotor_mode_ini; 
                } else {
                    $p->total_skor = $total_kotor_mode_ini - $p->total_minus;
                }
                
                return $p;

            })->sort(function($a, $b) use ($tieBreakersAktif) {
                // 2. LOGIKA TIE-BREAKER SAKTI
                if ($a->total_skor != $b->total_skor) {
                    return $b->total_skor <=> $a->total_skor;
                }
                
                // Kalau Total Skor sama, Adu Tie-Breaker dari Prioritas Master Event
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
            'semua_kategori' => $semua_kategori, 
            'kolom_kategori_tampil' => $kolom_kategori_tampil,
            'ranking_peserta' => $pesertas
        ]);
    }
}