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

    // ==========================================
    // PERBAIKAN 1: GABUNGKAN FUNGSI YANG KEMBAR
    // ==========================================
    public function updatedSelectedPesertaId($peserta_id) 
    { 
        // Load nilai yang sudah pernah diisi
        $this->loadExistingValues(); 

        // Load waktu tampil jika sudah pernah diisi (Khusus Timer)
        if($peserta_id) {
            $peserta = \App\Models\Peserta::find($peserta_id);
            if($peserta && $peserta->durasi_tampil_detik) {
                $this->menit_tampil = floor($peserta->durasi_tampil_detik / 60);
                $this->detik_tampil = $peserta->durasi_tampil_detik % 60;
            } else {
                $this->menit_tampil = 0;
                $this->detik_tampil = 0;
            }
        }
    }

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
        $juriTerpilih = Juri::find($this->selected_juri_id);
        $isTimer = $juriTerpilih && (str_contains(strtolower($juriTerpilih->posisi), 'admin') || str_contains(strtolower($juriTerpilih->posisi), 'timer'));

        // Aturan validasi dinamis (Jika bukan timer, wajib isi array inputs)
        $rules = [
            'selected_juri_id' => 'required',
            'selected_peserta_id' => 'required',
        ];
        
        if (!$isTimer) {
            $rules['inputs'] = 'required|array';
        }

        $this->validate($rules, [
            'selected_juri_id.required' => 'Pilih Juri dulu bro!',
            'selected_peserta_id.required' => 'Peserta belum dipilih!',
            'inputs.required' => 'Belum ada satupun nilai yang dipilih!',
        ]);

        // ==========================================
        // PROSES SIMPAN NILAI JURI (SELAIN TIMER)
        // ==========================================
        if (!$isTimer) {
            // 1. FILTER INPUT (Buang yang kosong, tapi biarkan angka 0)
            $inputs_valid = is_array($this->inputs) ? array_filter($this->inputs, function($val) {
                return $val !== "" && $val !== null; 
            }) : [];

            // 2. CEK APAKAH ADA ISINYA
            if (count($inputs_valid) == 0) {
                session()->flash('error', 'GAGAL! Belum ada satupun tombol nilai yang ditekan!');
                return;
            }

            // 3. PROSES SIMPAN AMAN
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
        }

        // ==========================================
        // PROSES SIMPAN WAKTU & DENDA (KHUSUS TIMER)
        // ==========================================
        if ($isTimer) {
            $total_detik = ((int)$this->menit_tampil * 60) + (int)$this->detik_tampil;
            $peserta = Peserta::find($this->selected_peserta_id);
            
            if ($peserta) {
                // Simpan Durasi ke database
                $peserta->update(['durasi_tampil_detik' => $total_detik]);

                // =========================================================
                // RUMUS AUTO DENDA WAKTU (SUDAH DISUNTIK JENIS PELANGGARAN)
                // =========================================================
                $waktu_maks_detik = 480; // Batas: 8 Menit (480 detik)
                
                if ($total_detik > $waktu_maks_detik) {
                    $kelebihan_detik = $total_detik - $waktu_maks_detik;
                    
                    // Denda -1 poin untuk SETIAP kelipatan 5 detik lebih
                    $kelipatan = ceil($kelebihan_detik / 5); 
                    $poin_minus = $kelipatan * 1; 

                    \App\Models\Denda::updateOrCreate(
                        [
                            'peserta_id' => $peserta->id, 
                            'jenis_pelanggaran' => 'WAKTU TAMPIL' // <--- INI DIA OBATNYA BRO!
                        ],
                        [
                            'keterangan' => "OVER TIME ($kelebihan_detik dtk)",
                            'poin_minus' => $poin_minus
                        ]
                    );
                } else {
                    // Jika waktu diedit menjadi aman, hapus denda over timenya otomatis
                    \App\Models\Denda::where('peserta_id', $peserta->id)
                                     ->where('jenis_pelanggaran', 'WAKTU TAMPIL') // Hapus khusus denda waktu
                                     ->delete();
                }
            }
        }

        // 4. UPDATE STATUS PESERTA
        Peserta::where('id', $this->selected_peserta_id)->update(['status_tampil' => 'selesai']);

        session()->flash('message', '🔥 Data Berhasil Disimpan ke Database!');

        // ==========================================
        // PERBAIKAN 2: RESET HARUS DI LAKUKAN PALING AKHIR
        // ==========================================
        $this->inputs = []; 
        $this->menit_tampil = 0;
        $this->detik_tampil = 0;
        $this->selected_peserta_id = ''; 
    }
}