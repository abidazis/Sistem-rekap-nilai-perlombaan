<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads; 
use App\Models\Lomba;
use App\Models\KategoriPenilaian;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

class MasterEvent extends Component
{
    use WithFileUploads;

    public $nama_lomba, $tanggal_pelaksanaan, $lokasi, $durasi_maksimal_detik = 600;
    public $lomba_id;
    
    public $logo; 
    public $logo_lama; 
    
    public $format_juara = 'all_harapan';
    public $urutan_juara_teks; 
    public $kuota_juara = 0; // 0 = All Trophy
    public $tie_breakers = [];

    public $is_create = false;
    public $is_edit = false;

    #[Layout('layouts.app')]
    public function render()
    {
        $kategoris = [];
        if ($this->lomba_id) {
            $kategoris = KategoriPenilaian::where('lomba_id', $this->lomba_id)->get();
        }

        return view('livewire.master-event', [
            'events' => Lomba::latest()->get(),
            'kategoris' => $kategoris 
        ]);
    }

    public function create()
    {
        $this->resetFields();
        $this->urutan_juara_teks = "Juara Utama 1\nJuara Utama 2\nJuara Utama 3\nJuara Harapan 1\nJuara Harapan 2\nJuara Harapan 3";
        $this->is_create = true;
        $this->is_edit = false;
    }

    public function store()
    {
        $this->validate([
            'nama_lomba' => 'required',
            'tanggal_pelaksanaan' => 'required|date',
            'lokasi' => 'required',
            'durasi_maksimal_detik' => 'required|numeric',
            'logo' => 'nullable|image|max:2048', 
            'urutan_juara_teks' => 'required',
            'kuota_juara' => 'nullable|numeric'
        ]);

        $logoPath = null;
        if ($this->logo) {
            $logoPath = $this->logo->store('event-logos', 'public');
        }

        $urutanArray = array_values(array_filter(array_map('trim', explode("\n", $this->urutan_juara_teks))));

        Lomba::create([
            'nama_lomba' => $this->nama_lomba,
            'tanggal_pelaksanaan' => $this->tanggal_pelaksanaan,
            'lokasi' => $this->lokasi,
            'durasi_maksimal_detik' => $this->durasi_maksimal_detik,
            'logo' => $logoPath,
            'urutan_juara' => $urutanArray,
            'kuota_juara' => $this->kuota_juara ?: 0,
            'format_juara' => $this->format_juara,
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
        $this->logo_lama = $lomba->logo;
        
        $this->format_juara = $lomba->format_juara ?? 'all_harapan';
        $this->kuota_juara = $lomba->kuota_juara ?? 0;
        $this->tie_breakers = is_array($lomba->tie_breakers) ? $lomba->tie_breakers : [];
        
        $this->urutan_juara_teks = is_array($lomba->urutan_juara) ? implode("\n", $lomba->urutan_juara) : "Juara Utama 1\nJuara Utama 2\nJuara Utama 3";

        $this->is_create = true; 
        $this->is_edit = true;
    }

    public function update()
    {
        $this->validate([
            'nama_lomba' => 'required',
            'tanggal_pelaksanaan' => 'required|date',
            'lokasi' => 'required',
            'durasi_maksimal_detik' => 'required|numeric',
            'logo' => 'nullable|image|max:2048',
            'urutan_juara_teks' => 'required',
            'kuota_juara' => 'nullable|numeric'
        ]);

        $lomba = Lomba::find($this->lomba_id);
        $urutanArray = array_values(array_filter(array_map('trim', explode("\n", $this->urutan_juara_teks))));

        $dataUpdate = [
            'nama_lomba' => $this->nama_lomba,
            'tanggal_pelaksanaan' => $this->tanggal_pelaksanaan,
            'lokasi' => $this->lokasi,
            'durasi_maksimal_detik' => $this->durasi_maksimal_detik,
            'urutan_juara' => $urutanArray,
            'kuota_juara' => $this->kuota_juara ?: 0,
            'format_juara' => $this->format_juara,
            'tie_breakers' => $this->tie_breakers
        ];

        if ($this->logo) {
            if ($lomba->logo) {
                Storage::disk('public')->delete($lomba->logo);
            }
            $dataUpdate['logo'] = $this->logo->store('event-logos', 'public');
        }

        $lomba->update($dataUpdate);

        session()->flash('message', 'Event Berhasil Diupdate!');
        $this->cancel();
    }

    public function delete($id)
    {
        $lomba = Lomba::find($id);
        if ($lomba->logo) {
            Storage::disk('public')->delete($lomba->logo);
        }
        $lomba->delete();
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
        $this->logo = null;
        $this->logo_lama = null;
        $this->durasi_maksimal_detik = 600;
        $this->format_juara = 'all_harapan';
        $this->kuota_juara = 0;
        $this->tie_breakers = [];
        $this->urutan_juara_teks = '';
    }
}