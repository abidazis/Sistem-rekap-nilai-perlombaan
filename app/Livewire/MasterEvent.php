<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lomba;
use Livewire\Attributes\Layout;

class MasterEvent extends Component
{
    // 1. Variabel Form
    public $nama_lomba, $tanggal_pelaksanaan, $lokasi, $durasi_maksimal_detik = 600;
    public $lomba_id;
    
    // 2. Mode (Apakah sedang nambah data atau tidak)
    public $is_create = false;
    public $is_edit = false;

    // Supaya layout tetap jalan
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.master-event', [
            // Ambil data terbaru dari database
            'events' => Lomba::latest()->get()
        ]);
    }

    // Fungsi: Munculkan Form Tambah
    public function create()
    {
        $this->resetFields();
        $this->is_create = true;
        $this->is_edit = false;
    }

    // Fungsi: Simpan Data Baru
    public function store()
    {
        // Validasi
        $this->validate([
            'nama_lomba' => 'required',
            'tanggal_pelaksanaan' => 'required|date',
            'lokasi' => 'required',
            'durasi_maksimal_detik' => 'required|numeric',
        ]);

        // Simpan ke Database
        Lomba::create([
            'nama_lomba' => $this->nama_lomba,
            'tanggal_pelaksanaan' => $this->tanggal_pelaksanaan,
            'lokasi' => $this->lokasi,
            'durasi_maksimal_detik' => $this->durasi_maksimal_detik,
            'status_aktif' => true
        ]);

        session()->flash('message', 'Event Berhasil Ditambahkan!');
        $this->cancel(); // Tutup form
    }

    // Fungsi: Edit Data
    public function edit($id)
    {
        $lomba = Lomba::find($id);
        $this->lomba_id = $id;
        $this->nama_lomba = $lomba->nama_lomba;
        $this->tanggal_pelaksanaan = $lomba->tanggal_pelaksanaan;
        $this->lokasi = $lomba->lokasi;
        $this->durasi_maksimal_detik = $lomba->durasi_maksimal_detik;

        $this->is_create = true; // Kita pakai form yang sama
        $this->is_edit = true;
    }

    // Fungsi: Update Data
    public function update()
    {
        $this->validate([
            'nama_lomba' => 'required',
            'tanggal_pelaksanaan' => 'required|date',
        ]);

        $lomba = Lomba::find($this->lomba_id);
        $lomba->update([
            'nama_lomba' => $this->nama_lomba,
            'tanggal_pelaksanaan' => $this->tanggal_pelaksanaan,
            'lokasi' => $this->lokasi,
            'durasi_maksimal_detik' => $this->durasi_maksimal_detik,
        ]);

        session()->flash('message', 'Event Berhasil Diupdate!');
        $this->cancel();
    }

    // Fungsi: Hapus Data
    public function delete($id)
    {
        Lomba::find($id)->delete();
        session()->flash('message', 'Event Berhasil Dihapus!');
    }

    // Fungsi: Batal / Tutup Form
    public function cancel()
    {
        $this->is_create = false;
        $this->is_edit = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->nama_lomba = '';
        $this->tanggal_pelaksanaan = '';
        $this->lokasi = '';
        $this->durasi_maksimal_detik = 600;
    }
}