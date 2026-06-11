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
    
    public $selected_lomba_id = '';
    public $selected_tingkat = 'SMP'; 
    public $selected_kategori_id = '';     
    public $selected_juri_id = '';
    public $selected_peserta_id = '';

    public $inputs = [];
    
    // 🚀 VARIABEL KUNCI UNTUK JURI
    public $is_juri_locked = false;
    public $nama_juri_locked = '';

    public function mount()
    {
        // 1. Set default lomba
        $latest = Lomba::latest()->first();
        if($latest) {
            $this->selected_lomba_id = $latest->id;
        }

        // 2. CEK MUTLAK: Apakah username yang login ada di tabel Juri?
        if (auth()->check()) {
            $juri_login = Juri::where('username', auth()->user()->username)->first();
            
            if ($juri_login) {
                // JIKA ADA, KUNCI SEMUANYA!
                $this->is_juri_locked = true;
                $this->nama_juri_locked = $juri_login->nama . ' (' . $juri_login->posisi . ')';
                $this->selected_juri_id = $juri_login->id;
                $this->selected_lomba_id = $juri_login->lomba_id;
            }
        }
    }

    public function updatedSelectedLombaId() {
        $this->selected_kategori_id = '';
        if (!$this->is_juri_locked) {
            $this->selected_juri_id = '';
        }
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
            
            $juri_login = null;
            if ($this->is_juri_locked && auth()->check()) {
                $juri_login = Juri::where('username', auth()->user()->username)->first();
            }

            // 🚀 1. FILTER JURI
            $queryJuri = Juri::where('lomba_id', $this->selected_lomba_id);

            if ($this->is_juri_locked && $juri_login) {
                // Kunci Query Juri
                $queryJuri->where('id', $juri_login->id);
            } else {
                // Logika Admin/Tim Rekap
                if (!empty($this->selected_kategori_id)) {
                    $queryJuri->where(function($q) {
                        $q->whereJsonContains('kategori_ids', (string) $this->selected_kategori_id)
                          ->orWhereJsonContains('kategori_ids', (int) $this->selected_kategori_id)
                          ->orWhere('posisi', 'like', '%timer%')
                          ->orWhere('posisi', 'like', '%admin%');
                    });
                }
            }
            $juris = $queryJuri->get();
            
            // 🚀 2. KODE FILTER PESERTA
            $pesertas = Peserta::where('lomba_id', $this->selected_lomba_id)
                          ->where('tingkat', $this->selected_tingkat)
                          ->orderBy('no_urut', 'asc')
                          ->get();

            // 🚀 3. FILTER ALL KATEGORI
            $queryAllKategori = KategoriPenilaian::where('lomba_id', $this->selected_lomba_id);
            if ($this->is_juri_locked && $juri_login) {
                $queryAllKategori->whereIn('id', $juri_login->kategori_ids ?? []);
            }
            $all_kategoris = $queryAllKategori->get();

            // 🚀 4. KODE STRUKTUR PENILAIAN
            $queryStruktur = KategoriPenilaian::where('lomba_id', $this->selected_lomba_id);
            if ($this->is_juri_locked && $juri_login) {
                $queryStruktur->whereIn('id', $juri_login->kategori_ids ?? []);
            }
            if (!empty($this->selected_kategori_id)) {
                $queryStruktur->where('id', $this->selected_kategori_id);
            }
            $kategoris = $queryStruktur->get();

            foreach ($kategoris as $kat) {
                $items = ItemPenilaian::where('kategori_penilaian_id', $kat->id)->orderBy('urutan', 'asc')->get();
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
        }

        $this->validate($rules, [
            'selected_juri_id.required' => 'Pilih Juri dulu bro!',
            'selected_peserta_id.required' => 'Peserta belum dipilih!',
            'inputs.required' => 'Belum ada satupun nilai yang diisi!',
        ]);

        if (!$isTimer) {
            // ====================================================================
            // 🚀 FITUR CROSCHECK: PASTIKAN SEMUA KOLOM TERISI (TIDAK ADA YANG BOLONG)
            // ====================================================================
            
            // 1. Cari tahu berapa total kolom (item gerakan) yang WAJIB diisi
            $queryStruktur = KategoriPenilaian::where('lomba_id', $this->selected_lomba_id);
            if ($this->is_juri_locked) {
                $queryStruktur->whereIn('id', $juriTerpilih->kategori_ids ?? []);
            }
            if (!empty($this->selected_kategori_id)) {
                $queryStruktur->where('id', $this->selected_kategori_id);
            }
            
            $kategori_ids = $queryStruktur->pluck('id');
            // Hitung total gerakan dari kategori yang tampil di layar Juri
            $total_wajib_isi = ItemPenilaian::whereIn('kategori_penilaian_id', $kategori_ids)->count();

            // 2. Hitung berapa inputan yang sudah benar-benar ada isinya (bukan string kosong/null)
            $inputs_valid = is_array($this->inputs) ? array_filter($this->inputs, function($val) {
                return $val !== "" && $val !== null; 
            }) : [];

            // 3. Bandingkan! Jika yang diisi kurang dari total kolom wajib, TOLAK!
            if (count($inputs_valid) < $total_wajib_isi) {
                $kolom_kosong = $total_wajib_isi - count($inputs_valid);
                session()->flash('error', "⚠️ GAGAL DISIMPAN! Masih ada $kolom_kosong baris nilai yang terlewat/kosong. Pastikan semua gerakan dinilai sebelum klik Simpan.");
                return; // Hentikan proses simpan
            }

            // Jika lolos pengecekan 100%, baru simpan ke database
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
                
                $lomba = Lomba::find($this->selected_lomba_id);
                $waktu_maks_detik = $lomba ? $lomba->durasi_maksimal_detik : 600; 

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
        session()->flash('message', '🔥 Sempurna! Data Berhasil Disimpan ke Database!');

        $this->inputs = []; 
        $this->menit_tampil = 0;
        $this->detik_tampil = 0;
        $this->selected_peserta_id = ''; 
    }
}