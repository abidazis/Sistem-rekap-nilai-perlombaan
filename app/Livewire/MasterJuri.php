<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Juri;
use App\Models\Lomba;
use App\Models\KategoriPenilaian; // JANGAN LUPA IMPORT INI
use Livewire\Attributes\Layout;

class MasterJuri extends Component
{
    // Filter Event
    public $selected_lomba_id;

    // Form Fields
    public $nama, $posisi;
    public $juri_id; // Untuk Edit
    
    // Array untuk menyimpan centangan tugas juri (BARU)
    public $kategori_ids = [];

    // Mode
    public $is_create = false;
    public $is_edit = false;

    public function mount()
    {
        // Auto pilih event terakhir
        $latest = Lomba::latest()->first();
        if($latest) {
            $this->selected_lomba_id = $latest->id;
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.master-juri', [
            'events' => Lomba::latest()->get(),
            'juris' => Juri::where('lomba_id', $this->selected_lomba_id)->get(),
            // Load daftar kategori milik event ini untuk ditampilkan sbg checkbox (BARU)
            'kategoris' => KategoriPenilaian::where('lomba_id', $this->selected_lomba_id)->get()
        ]);
    }

    public function create()
    {
        if(!$this->selected_lomba_id) {
            session()->flash('error', 'Pilih Event dulu bos!');
            return;
        }
        $this->resetInput();
        $this->is_create = true;
    }

    public function store()
    {
        $this->validate([
            'nama' => 'required',
            'posisi' => 'required'
        ]);

        // Simpan data juri (tanpa akun login/username & password)
        \App\Models\Juri::create([
            'lomba_id' => $this->selected_lomba_id,
            'nama' => $this->nama,
            'posisi' => $this->posisi,
            'kategori_ids' => $this->kategori_ids
        ]);

        session()->flash('message', 'Data Juri Berhasil Ditambahkan!');
        $this->cancel();
    }

    public function edit($id)
    {
        $juri = Juri::find($id);
        $this->juri_id = $id;
        $this->nama = $juri->nama;
        $this->posisi = $juri->posisi;

        // Load centangan yang udah ada di database ke form edit (BARU)
        $this->kategori_ids = $juri->kategori_ids ?? [];

        $this->is_create = true;
        $this->is_edit = true;
    }

    public function update()
    {
        $this->validate([
            'nama' => 'required',
            'posisi' => 'required',
        ]);

        $data = [
            'nama' => $this->nama,
            'posisi' => $this->posisi,
            'kategori_ids' => $this->kategori_ids // Update centangan (BARU)
        ];

        Juri::find($this->juri_id)->update($data);

        session()->flash('message', 'Data & Hak Akses Juri Berhasil Diupdate!');
        $this->cancel();
    }

    public function delete($id)
    {
        // Cegah hapus Juri ID 1 kalau itu dipakai sistem default
        if($id == 1) {
             session()->flash('error', 'Juri Default (ID 1) tidak boleh dihapus!');
             return;
        }
        
        Juri::find($id)->delete();
        session()->flash('message', 'Akun Juri Dihapus!');
    }

    public function cancel()
    {
        $this->is_create = false;
        $this->is_edit = false;
        $this->resetInput();
    }

    private function resetInput()
    {
        $this->nama = '';
        $this->posisi = '';
        $this->kategori_ids = []; // Kosongkan centangan (BARU)
    }
}