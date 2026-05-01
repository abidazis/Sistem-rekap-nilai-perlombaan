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
    
    // Mode
    public $is_create = false;
    public $is_edit = false;

    public function mount()
    {
        // Otomatis pilih event terakhir biar user gak usah klik-klik dulu
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
            // Ambil kategori HANYA milik event yang dipilih
            'kategoris' => KategoriPenilaian::where('lomba_id', $this->selected_lomba_id)
                           ->orderBy('bobot_persen', 'desc')
                           ->get()
        ]);
    }

    // Fungsi: Buka Form Tambah
    public function create()
    {
        // Validasi: Harus pilih event dulu
        if(!$this->selected_lomba_id) {
            session()->flash('error', 'Silakan pilih event/lomba terlebih dahulu!');
            return;
        }

        $this->resetInput();
        $this->is_create = true;
        $this->is_edit = false;
    }

    // Fungsi: Simpan Data
    public function store()
    {
        $this->validate([
            'nama_kategori' => 'required',
            'bobot_persen' => 'required|numeric|min:0|max:100',
        ]);

        KategoriPenilaian::create([
            'lomba_id' => $this->selected_lomba_id, // Otomatis masuk ke event yg dipilih
            'nama_kategori' => strtoupper($this->nama_kategori),
            'bobot_persen' => $this->bobot_persen
        ]);

        session()->flash('message', 'Kategori Berhasil Ditambahkan!');
        $this->cancel();
    }

    // Fungsi: Edit Data
    public function edit($id)
    {
        $kategori = KategoriPenilaian::find($id);
        $this->kategori_id = $id;
        $this->nama_kategori = $kategori->nama_kategori;
        $this->bobot_persen = $kategori->bobot_persen;

        $this->is_create = true;
        $this->is_edit = true;
    }

    // Fungsi: Update Data
    public function update()
    {
        $this->validate([
            'nama_kategori' => 'required',
            'bobot_persen' => 'required|numeric|min:0|max:100',
        ]);

        $kategori = KategoriPenilaian::find($this->kategori_id);
        $kategori->update([
            'nama_kategori' => strtoupper($this->nama_kategori),
            'bobot_persen' => $this->bobot_persen
        ]);

        session()->flash('message', 'Kategori Berhasil Diupdate!');
        $this->cancel();
    }

    // Fungsi: Hapus Data
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
    }
}