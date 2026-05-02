<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Juri;
use App\Models\Lomba;
use App\Models\KategoriPenilaian; // JANGAN LUPA IMPORT INI
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;

class MasterJuri extends Component
{
    // Filter Event
    public $selected_lomba_id;

    // Form Fields
    public $nama, $posisi, $username, $password;
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
            'username' => 'required|unique:juri,username', // Pastikan nama tabelnya 'juris' kalau pakai standar laravel
            'password' => 'required|min:4',
            'posisi' => 'required'
        ]);

        Juri::create([
            'lomba_id' => $this->selected_lomba_id,
            'nama' => $this->nama,
            'posisi' => $this->posisi, // Misal: Juri PBB 1
            'username' => $this->username,
            'password' => Hash::make($this->password), // Enkripsi password
            'kategori_ids' => $this->kategori_ids // Simpan centangan (BARU)
        ]);

        session()->flash('message', 'Akun Juri Berhasil Dibuat beserta Hak Akses Kategori!');
        $this->cancel();
    }

    public function edit($id)
    {
        $juri = Juri::find($id);
        $this->juri_id = $id;
        $this->nama = $juri->nama;
        $this->posisi = $juri->posisi;
        $this->username = $juri->username;
        
        // Load centangan yang udah ada di database ke form edit (BARU)
        $this->kategori_ids = $juri->kategori_ids ?? [];
        
        // Password sengaja dikosongkan biar aman

        $this->is_create = true;
        $this->is_edit = true;
    }

    public function update()
    {
        $rules = [
            'nama' => 'required',
            'posisi' => 'required',
            'username' => 'required|unique:juri,username,'.$this->juri_id, // Pastikan nama tabelnya 'juris'
        ];

        // Password validasi hanya kalau diisi (ganti password)
        if ($this->password) {
            $rules['password'] = 'min:4';
        }

        $this->validate($rules);

        $data = [
            'nama' => $this->nama,
            'posisi' => $this->posisi,
            'username' => $this->username,
            'kategori_ids' => $this->kategori_ids // Update centangan (BARU)
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

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
        $this->username = '';
        $this->password = '';
        $this->kategori_ids = []; // Kosongkan centangan (BARU)
    }
}