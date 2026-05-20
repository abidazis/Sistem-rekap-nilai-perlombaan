<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lomba;
use App\Models\KategoriPenilaian;
use Livewire\Attributes\Layout;

class MasterEvent extends Component
{
    // 1. Variabel Form Event
    public $nama_lomba, $tanggal_pelaksanaan, $lokasi, $durasi_maksimal_detik = 600;
    public $lomba_id;
    
    // 2. Variabel Pengaturan Tambahan (Klasemen & Tie Breaker)
    public $format_juara = 'all_harapan';
    public $tie_breakers = []; // Akan berisi array dari id kategori yang dipilih

    // 3. Mode (Apakah sedang nambah data atau tidak)
    public $is_create = false;
    public $is_edit = false;

    #[Layout('layouts.app')]
    public function render()
    {
        // Ambil daftar kategori berdasarkan lomba yang sedang di-edit (jika ada)
        $kategoris = [];
        if ($this->lomba_id) {
            $kategoris = KategoriPenilaian::where('lomba_id', $this->lomba_id)->get();
        }

        return view('livewire.master-event', [
            'events' => Lomba::latest()->get(),
            'kategoris' => $kategoris // Lempar ke view
        ]);
    }

    public function create()
    {
        $this->resetFields();
        $this->is_create = true;
        $this->is_edit = false;
    }

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
            'format_juara' => $this->format_juara, // Default masuk
            'status_aktif' => true
        ]);

        session()->flash('message', 'Event Berhasil Ditambahkan!');
        $this->cancel();
    }

    public function edit($id)
    {
        $lomba = Lomba::find($id);
        $this->lomba_id = $id;
        $this->nama_lomba = $lomba->nama_lomba;
        $this->tanggal_pelaksanaan = $lomba->tanggal_pelaksanaan;
        $this->lokasi = $lomba->lokasi;
        $this->durasi_maksimal_detik = $lomba->durasi_maksimal_detik;
        
        // Load data pengaturan tambahan
        $this->format_juara = $lomba->format_juara ?? 'all_harapan';
        $this->tie_breakers = is_array($lomba->tie_breakers) ? $lomba->tie_breakers : [];

        $this->is_create = true; 
        $this->is_edit = true;
    }

    public function update()
    {
        // 🚨 PERBAIKAN: Masukkan variabel durasi_maksimal_detik ke validasi!
        $this->validate([
            'nama_lomba' => 'required',
            'tanggal_pelaksanaan' => 'required|date',
            'lokasi' => 'required',
            'durasi_maksimal_detik' => 'required|numeric',
        ]);

        $lomba = Lomba::find($this->lomba_id);
        $lomba->update([
            'nama_lomba' => $this->nama_lomba,
            'tanggal_pelaksanaan' => $this->tanggal_pelaksanaan,
            'lokasi' => $this->lokasi,
            'durasi_maksimal_detik' => $this->durasi_maksimal_detik,
            'format_juara' => $this->format_juara,
            'tie_breakers' => $this->tie_breakers // Simpan array ke DB
        ]);

        session()->flash('message', 'Event Berhasil Diupdate!');
        $this->cancel();
    }

    public function delete($id)
    {
        Lomba::find($id)->delete();
        session()->flash('message', 'Event Berhasil Dihapus!');
    }

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
        $this->format_juara = 'all_harapan';
        $this->tie_breakers = [];
    }
}