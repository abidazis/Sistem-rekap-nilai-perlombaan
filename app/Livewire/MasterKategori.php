<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KategoriPenilaian;
use App\Models\Lomba;
use Livewire\Attributes\Layout;

class MasterKategori extends Component
{
    // Filter Utama
    public $selected_lomba_id;

    // Form Fields
    public $nama_kategori, $bobot_persen;
    public $kategori_id;
    
    // Status Aturan Juara
    public $is_utama = false;
    public $is_umum = false;
    
    // Mode
    public $is_create = false;
    public $is_edit = false;

    public function mount()
    {
        $latest_lomba = Lomba::latest()->first();
        if ($latest_lomba) {
            $this->selected_lomba_id = $latest_lomba->id;
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.master-kategori', [
            'events' => Lomba::orderBy('created_at', 'desc')->get(),
            'kategoris' => KategoriPenilaian::where('lomba_id', $this->selected_lomba_id)
                           ->orderBy('bobot_persen', 'desc')
                           ->get()
        ]);
    }

    public function create()
    {
        if(!$this->selected_lomba_id) {
            session()->flash('error', 'Silakan pilih event/lomba terlebih dahulu!');
            return;
        }

        $this->resetInput();
        $this->is_create = true;
        $this->is_edit = false;
    }

    public function store()
    {
        // 1. VALIDASI DI SINI TEMPATNYA BRO!
        $this->validate([
            'nama_kategori' => 'required',
            'bobot_persen' => 'required|numeric|min:0|max:100',
            'is_utama' => 'nullable|boolean', 
            'is_umum' => 'nullable|boolean',
        ]);

        // 2. SIMPAN KE DATABASE PAKAI LOGIKA 1 ATAU 0
        KategoriPenilaian::create([
            'lomba_id' => $this->selected_lomba_id,
            'nama_kategori' => strtoupper($this->nama_kategori),
            'bobot_persen' => $this->bobot_persen,
            'is_utama' => $this->is_utama ? 1 : 0, 
            'is_umum' => $this->is_umum ? 1 : 0,
        ]);

        session()->flash('message', 'Kategori Berhasil Ditambahkan!');
        $this->cancel();
    }

    public function edit($id)
    {
        $kategori = KategoriPenilaian::find($id);
        $this->kategori_id = $id;
        $this->nama_kategori = $kategori->nama_kategori;
        $this->bobot_persen = $kategori->bobot_persen;
        
        $this->is_utama = $kategori->is_utama;
        $this->is_umum = $kategori->is_umum;

        $this->is_create = true;
        $this->is_edit = true;
    }

    public function update()
    {
        $this->validate([
            'nama_kategori' => 'required',
            'bobot_persen' => 'required|numeric|min:0|max:100',
            'is_utama' => 'nullable|boolean',
            'is_umum' => 'nullable|boolean',
        ]);

        $kategori = KategoriPenilaian::find($this->kategori_id);
        $kategori->update([
            'nama_kategori' => strtoupper($this->nama_kategori),
            'bobot_persen' => $this->bobot_persen,
            'is_utama' => $this->is_utama ? 1 : 0,
            'is_umum' => $this->is_umum ? 1 : 0,
        ]);

        session()->flash('message', 'Kategori Berhasil Diupdate!');
        $this->cancel();
    }

    public function delete($id)
    {
        KategoriPenilaian::find($id)->delete();
        session()->flash('message', 'Kategori Berhasil Dihapus!');
    }

    public function cancel()
    {
        $this->is_create = false;
        $this->is_edit = false;
        $this->resetInput();
    }

    private function resetInput()
    {
        $this->nama_kategori = '';
        $this->bobot_persen = '';
        $this->is_utama = false;
        $this->is_umum = false;
    }
}