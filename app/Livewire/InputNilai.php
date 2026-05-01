<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lomba;
use App\Models\Peserta;
use App\Models\Nilai;
use App\Models\Juri;
use App\Models\KategoriPenilaian;
use App\Models\ItemPenilaian;
use Livewire\Attributes\Layout;

class InputNilai extends Component
{
    public $selected_lomba_id;
    public $selected_juri_id;
    public $selected_peserta_id;

    public $inputs = [];

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
        $kategoris = [];
        $juris = [];
        $pesertas = [];
        
        if ($this->selected_lomba_id) {
            $juris = Juri::where('lomba_id', $this->selected_lomba_id)->get();
            $pesertas = Peserta::where('lomba_id', $this->selected_lomba_id)
                          ->orderBy('no_urut', 'asc')
                          ->get();

            $kategoris = KategoriPenilaian::with(['items' => function($q) {
                            $q->orderBy('urutan', 'asc');
                         }])
                         ->where('lomba_id', $this->selected_lomba_id)
                         ->get();
        }

        return view('livewire.input-nilai', [
            'events' => Lomba::latest()->get(),
            'juris' => $juris,
            'pesertas' => $pesertas,
            'struktur_penilaian' => $kategoris
        ]);
    }

    public function updatedSelectedPesertaId() { $this->loadExistingValues(); }
    public function updatedSelectedJuriId() { $this->loadExistingValues(); }

    public function loadExistingValues()
    {
        $this->inputs = []; 

        if ($this->selected_peserta_id && $this->selected_juri_id) {
            $existing_scores = Nilai::where('peserta_id', $this->selected_peserta_id)
                                    ->where('juri_id', $this->selected_juri_id)
                                    ->get();
                                    
            foreach ($existing_scores as $score) {
                $this->inputs[$score->item_penilaian_id] = $score->nilai;
            }
        }
    }

    public function simpan()
    {
        $this->validate([
            'selected_juri_id' => 'required',
            'selected_peserta_id' => 'required',
            'inputs' => 'required|array',
        ], [
            'selected_juri_id.required' => 'Pilih Juri dulu bro!',
            'selected_peserta_id.required' => 'Peserta belum dipilih!',
        ]);

        // 1. VALIDASI: HITUNG TOTAL GERAKAN YANG HARUS DINILAI
        $total_item_wajib = ItemPenilaian::join('kategori_penilaian', 'item_penilaian.kategori_penilaian_id', '=', 'kategori_penilaian.id')
            ->where('kategori_penilaian.lomba_id', $this->selected_lomba_id)
            ->count();

        // 2. FILTER INPUT (Buang yang kosong, tapi biarkan angka 0)
        $inputs_valid = array_filter($this->inputs, function($val) {
            return $val !== "" && $val !== null; 
        });

        // 3. CEK KESESUAIAN
        if (count($inputs_valid) < $total_item_wajib) {
            session()->flash('error', 'GAGAL! Ada item gerakan yang terlewat belum dinilai. Jika pasukan tidak bergerak, wajib pilih nilai 0!');
            return;
        }

        // 4. PROSES SIMPAN AMAN
        foreach ($inputs_valid as $item_id => $nilai) {
            Nilai::updateOrCreate(
                [
                    'peserta_id' => $this->selected_peserta_id,
                    'item_penilaian_id' => $item_id,
                    'juri_id' => $this->selected_juri_id 
                ],
                [
                    'nilai' => $nilai
                ]
            );
        }

        Peserta::where('id', $this->selected_peserta_id)->update(['status_tampil' => 'selesai']);

        session()->flash('message', 'Nilai Berhasil Disimpan!');

        $this->inputs = []; 
        $this->selected_peserta_id = ''; 
    }
}