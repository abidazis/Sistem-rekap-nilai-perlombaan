<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Peserta; 
use App\Models\Lomba; 
use App\Models\Juri; 
use Illuminate\Support\Facades\DB;

class AuditJuri extends Component
{
    public $lomba_id;

    public function mount()
    {
        $lomba = Lomba::first();
        if ($lomba) {
            $this->lomba_id = $lomba->id;
        }
    }

    public function render()
    {
        $lombas = Lomba::all();
        
        $pesertas = collect();
        $juris = collect();
        $statistikJuri = [];

        if ($this->lomba_id) {
            $pesertas = Peserta::where('lomba_id', $this->lomba_id)->get();
            $juris = Juri::where('lomba_id', $this->lomba_id)->get();

            // 1. TAMBAHKAN 'posisi' KE DALAM STATISTIK
            foreach ($juris as $juri) {
                $namaJuri = $juri->nama ?? 'Juri';
                
                $statistikJuri[$juri->id] = [
                    'nama' => $namaJuri,
                    'posisi' => $juri->posisi ?? 'Tugas Tidak Diketahui', // Ambil dari tabel juri
                    'total_akumulasi' => 0,
                    'rata_rata' => 0,
                    'jumlah_dinilai' => 0,
                    'persentase_bar' => 0
                ];
            }

            $pesertas = $pesertas->map(function ($peserta) use ($juris, &$statistikJuri) {
                $totalKeseluruhan = 0;
                $nilaiPerJuri = [];

                foreach ($juris as $juri) {
                    $totalNilaiJuri = DB::table('nilai')
                                        ->where('peserta_id', $peserta->id)
                                        ->where('juri_id', $juri->id)
                                        ->sum('nilai'); 

                    $nilaiPerJuri[$juri->id] = $totalNilaiJuri;
                    $totalKeseluruhan += $totalNilaiJuri;

                    if ($totalNilaiJuri > 0) {
                        $statistikJuri[$juri->id]['total_akumulasi'] += $totalNilaiJuri;
                        $statistikJuri[$juri->id]['jumlah_dinilai'] += 1;
                    }
                }

                $peserta->nilai_per_juri = $nilaiPerJuri;
                $peserta->total_keseluruhan = $totalKeseluruhan;

                return $peserta;
            });

            $maxTotal = 1; 
            foreach ($statistikJuri as $id => $stat) {
                if ($stat['jumlah_dinilai'] > 0) {
                    $statistikJuri[$id]['rata_rata'] = round($stat['total_akumulasi'] / $stat['jumlah_dinilai'], 2);
                }
                if ($stat['total_akumulasi'] > $maxTotal) {
                    $maxTotal = $stat['total_akumulasi'];
                }
            }

            foreach ($statistikJuri as $id => $stat) {
                $statistikJuri[$id]['persentase_bar'] = ($stat['total_akumulasi'] / $maxTotal) * 100;
            }

            $pesertas = $pesertas->sortByDesc('total_keseluruhan')->values();
        }

        return view('livewire.audit-juri', [
            'lombas' => $lombas,
            'juris' => $juris,
            'pesertas' => $pesertas,
            'statistikJuri' => $statistikJuri
        ])->layout('layouts.app'); 
    }
}