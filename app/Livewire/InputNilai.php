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
    public $menit_tampil = 0;
    public $detik_tampil = 0;
    
    // Pastikan semua default value aman
    public $selected_lomba_id = '';
    public $selected_tingkat = 'SMP'; // Default tingkat
    public $selected_kategori_id = '';     
    public $selected_juri_id = '';
    public $selected_peserta_id = '';

    public $inputs = [];

    public function mount()
    {
        $latest = Lomba::latest()->first();
        if($latest) {
            $this->selected_lomba_id = $latest->id;
        }
    }

    public function updatedSelectedLombaId() {
        $this->selected_kategori_id = '';
        $this->selected_juri_id = '';
        $this->selected_peserta_id = '';
        $this->inputs = [];
    }

    public function updatedSelectedTingkat() {
        $this->selected_peserta_id = '';
        $this->inputs = [];
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $juris = [];
        $pesertas = [];
        $all_kategoris = [];
        $struktur_penilaian = [];
        
        if (!empty($this->selected_lomba_id)) {
            $juris = Juri::where('lomba_id', $this->selected_lomba_id)->get();
            
            $pesertas = Peserta::where('lomba_id', $this->selected_lomba_id)
                          ->where('tingkat', $this->selected_tingkat)
                          ->orderBy('no_urut', 'asc')
                          ->get();

            $all_kategoris = KategoriPenilaian::where('lomba_id', $this->selected_lomba_id)->get();

            $queryStruktur = KategoriPenilaian::where('lomba_id', $this->selected_lomba_id);
            if (!empty($this->selected_kategori_id)) {
                $queryStruktur->where('id', $this->selected_kategori_id);
            }
            $kategoris = $queryStruktur->get();

            // 🚀 PERBAIKAN: Langsung tembak ke nama kolom aslinya
            foreach ($kategoris as $kat) {
                $items = ItemPenilaian::where('kategori_penilaian_id', $kat->id)
                         ->orderBy('urutan', 'asc')
                         ->get();
                
                $kat->daftar_item = $items; 
                $struktur_penilaian[] = $kat;
            }
        }

        return view('livewire.input-nilai', [
            'events' => Lomba::latest()->get(),
            'juris' => $juris,
            'pesertas' => $pesertas,
            'all_kategoris' => $all_kategoris,
            'struktur_penilaian' => $struktur_penilaian
        ]);
    }

    public function updatedSelectedPesertaId($peserta_id) 
    { 
        $this->loadExistingValues(); 
        if(!empty($peserta_id)) {
            $peserta = Peserta::find($peserta_id);
            if($peserta) {
                $durasi = $peserta->durasi_tampil_detik ?? 0;
                $this->menit_tampil = floor($durasi / 60);
                $this->detik_tampil = $durasi % 60;
            }
        }
    }

    public function updatedSelectedJuriId() { $this->loadExistingValues(); }
    public function updatedSelectedKategoriId() { $this->loadExistingValues(); }

    public function loadExistingValues()
    {
        $this->inputs = []; 
        if (!empty($this->selected_peserta_id) && !empty($this->selected_juri_id)) {
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
        $juriTerpilih = Juri::find($this->selected_juri_id);
        $isTimer = $juriTerpilih && (str_contains(strtolower($juriTerpilih->posisi), 'admin') || str_contains(strtolower($juriTerpilih->posisi), 'timer'));

        $rules = [
            'selected_juri_id' => 'required',
            'selected_peserta_id' => 'required',
        ];
        
        if (!$isTimer) {
            $rules['inputs'] = 'required|array';
            $rules['selected_kategori_id'] = 'required';
        }

        $this->validate($rules, [
            'selected_juri_id.required' => 'Pilih Juri dulu bro!',
            'selected_peserta_id.required' => 'Peserta belum dipilih!',
            'selected_kategori_id.required' => 'Pilih Kategori Penilaian dulu!',
            'inputs.required' => 'Belum ada satupun nilai yang ditekan!',
        ]);

        if (!$isTimer) {
            $inputs_valid = is_array($this->inputs) ? array_filter($this->inputs, function($val) {
                return $val !== "" && $val !== null; 
            }) : [];

            if (count($inputs_valid) == 0) {
                session()->flash('error', 'GAGAL! Belum ada nilai yang diisi!');
                return;
            }

            foreach ($inputs_valid as $item_id => $nilai) {
                Nilai::updateOrCreate(
                    [
                        'peserta_id' => $this->selected_peserta_id,
                        'item_penilaian_id' => $item_id,
                        'juri_id' => $this->selected_juri_id 
                    ],
                    ['nilai' => $nilai]
                );
            }
        }

        if ($isTimer) {
            $total_detik = ((int)$this->menit_tampil * 60) + (int)$this->detik_tampil;
            $peserta = Peserta::find($this->selected_peserta_id);
            
            if ($peserta) {
                $peserta->update(['durasi_tampil_detik' => $total_detik]);
                $waktu_maks_detik = 480; 

                if ($total_detik > $waktu_maks_detik) {
                    $kelebihan_detik = $total_detik - $waktu_maks_detik;
                    $kelipatan = ceil($kelebihan_detik / 5); 
                    $poin_minus = $kelipatan * 1; 

                    \App\Models\Denda::updateOrCreate(
                        ['peserta_id' => $peserta->id, 'jenis_pelanggaran' => 'WAKTU TAMPIL'],
                        ['keterangan' => "OVER TIME ($kelebihan_detik dtk)", 'poin_minus' => $poin_minus]
                    );
                } else {
                    \App\Models\Denda::where('peserta_id', $peserta->id)->where('jenis_pelanggaran', 'WAKTU TAMPIL')->delete();
                }
            }
        }

        Peserta::where('id', $this->selected_peserta_id)->update(['status_tampil' => 'selesai']);
        session()->flash('message', '🔥 Data Berhasil Disimpan ke Database!');

        $this->inputs = []; 
        $this->menit_tampil = 0;
        $this->detik_tampil = 0;
        $this->selected_peserta_id = ''; 
    }
}