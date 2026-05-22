<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lomba;
use App\Models\Peserta;
use App\Models\KategoriPenilaian;
use App\Models\PemenangSpesial;
use Livewire\Attributes\Layout;

class InputJuaraSpesial extends Component
{
    public $selected_lomba_id;
    public $selected_tingkat = 'SMP';
    public $selected_kategori_id;
    
    // Variabel untuk nyimpen ID Peserta yang juara
    public $juara_1, $juara_2, $juara_3;

    public function mount()
    {
        $latest = Lomba::latest()->first();
        if ($latest) {
            $this->selected_lomba_id = $latest->id;
        }
    }

    // Auto load data kalau dropdown di atas diganti
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['selected_lomba_id', 'selected_tingkat', 'selected_kategori_id'])) {
            $this->loadDataJuara();
        }
    }

    public function loadDataJuara()
    {
        $this->reset(['juara_1', 'juara_2', 'juara_3']);

        if ($this->selected_lomba_id && $this->selected_tingkat && $this->selected_kategori_id) {
            $winners = PemenangSpesial::where('lomba_id', $this->selected_lomba_id)
                ->where('tingkat', $this->selected_tingkat)
                ->where('kategori_penilaian_id', $this->selected_kategori_id)
                ->get();
            
            foreach ($winners as $w) {
                if ($w->rank == 1) $this->juara_1 = $w->peserta_id;
                if ($w->rank == 2) $this->juara_2 = $w->peserta_id;
                if ($w->rank == 3) $this->juara_3 = $w->peserta_id;
            }
        }
    }

    public function simpanJuara()
    {
        $this->validate([
            'selected_lomba_id' => 'required',
            'selected_tingkat' => 'required',
            'selected_kategori_id' => 'required',
        ]);

        // Bersihkan dulu data juara sebelumnya di kategori ini (replace)
        PemenangSpesial::where('lomba_id', $this->selected_lomba_id)
            ->where('tingkat', $this->selected_tingkat)
            ->where('kategori_penilaian_id', $this->selected_kategori_id)
            ->delete();

        // Insert Juara 1, 2, 3 kalau formnya diisi
        if ($this->juara_1) PemenangSpesial::create(['lomba_id' => $this->selected_lomba_id, 'tingkat' => $this->selected_tingkat, 'kategori_penilaian_id' => $this->selected_kategori_id, 'rank' => 1, 'peserta_id' => $this->juara_1]);
        if ($this->juara_2) PemenangSpesial::create(['lomba_id' => $this->selected_lomba_id, 'tingkat' => $this->selected_tingkat, 'kategori_penilaian_id' => $this->selected_kategori_id, 'rank' => 2, 'peserta_id' => $this->juara_2]);
        if ($this->juara_3) PemenangSpesial::create(['lomba_id' => $this->selected_lomba_id, 'tingkat' => $this->selected_tingkat, 'kategori_penilaian_id' => $this->selected_kategori_id, 'rank' => 3, 'peserta_id' => $this->juara_3]);

        session()->flash('message', '🏆 Juara Spesial Berhasil Disimpan!');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        // 1. INI YANG KETINGGALAN BRO! Ambil data Lomba untuk Dropdown
        $events = Lomba::orderBy('created_at', 'desc')->get();
        
        $kategoris = [];
        $pesertas = [];

        if ($this->selected_lomba_id) {
            $kategoris = KategoriPenilaian::where('lomba_id', $this->selected_lomba_id)
                ->where('is_tersendiri', true)
                ->get();
        }

        if ($this->selected_lomba_id && $this->selected_tingkat) {
            $pesertas = Peserta::where('lomba_id', $this->selected_lomba_id)
                ->where('tingkat', $this->selected_tingkat)
                ->orderBy('no_urut', 'asc')
                ->get();
        }

        // 2. PASTIKAN $events IKUT MASUK KE DALAM COMPACT!
        return view('livewire.input-juara-spesial', compact('events', 'kategoris', 'pesertas'));
    }
}