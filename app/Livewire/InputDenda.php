<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lomba;
use App\Models\Peserta;
use App\Models\Denda;
use App\Models\PedomanDenda; // Tambahkan ini
use Livewire\Attributes\Layout;

class InputDenda extends Component
{
    public $selected_lomba_id;
    public $selected_peserta_id;
    
    // Form Input
    public $selected_pedoman_id = ''; // Gunakan ID pedoman
    public $jenis_pelanggaran = '';
    public $poin_minus;
    public $keterangan;

    // Mode Master
    public $is_master_mode = false;
    public $nama_pelanggaran, $poin_master;

    public function mount()
    {
        $latest = Lomba::latest()->first();
        if($latest) {
            $this->selected_lomba_id = $latest->id;
        }
    }

    // MAGIC: Otomatis isi poin saat Pedoman dipilih
    public function updatedSelectedPedomanId($value)
    {
        if ($value && $value != 'manual') {
            $pedoman = PedomanDenda::find($value);
            if ($pedoman) {
                $this->jenis_pelanggaran = $pedoman->nama_pelanggaran;
                $this->poin_minus = $pedoman->poin_minus;
            }
        } else {
            $this->jenis_pelanggaran = '';
            $this->poin_minus = null;
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $pesertas = [];
        $denda_list = [];
        $pedomans = [];

        if($this->selected_lomba_id) {
            $pesertas = Peserta::where('lomba_id', $this->selected_lomba_id)->orderBy('no_urut', 'asc')->get();
            $pedomans = PedomanDenda::where('lomba_id', $this->selected_lomba_id)->get();
        }

        if($this->selected_peserta_id) {
            $denda_list = Denda::where('peserta_id', $this->selected_peserta_id)->latest()->get();
        }

        return view('livewire.input-denda', [
            'events' => Lomba::latest()->get(),
            'pesertas' => $pesertas,
            'denda_list' => $denda_list,
            'pedomans' => $pedomans
        ]);
    }

    public function simpan()
    {
        $this->validate([
            'selected_peserta_id' => 'required',
            'jenis_pelanggaran' => 'required',
            'poin_minus' => 'required|numeric|min:1',
        ]);

        Denda::create([
            'peserta_id' => $this->selected_peserta_id,
            'jenis_pelanggaran' => $this->jenis_pelanggaran,
            'poin_minus' => $this->poin_minus,
            'keterangan' => $this->keterangan
        ]);

        session()->flash('message', 'Pelanggaran / Minus Poin berhasil ditambahkan!');
        
        $this->reset(['jenis_pelanggaran', 'poin_minus', 'keterangan', 'selected_pedoman_id']);
    }

    public function hapus($id)
    {
        Denda::find($id)->delete();
        session()->flash('message', 'Data denda dibatalkan/dihapus.');
    }

    public function toggleMasterMode() 
    { 
        $this->is_master_mode = !$this->is_master_mode; 
    }

    public function simpanMaster()
    {
        $this->validate([
            'nama_pelanggaran' => 'required',
            'poin_master' => 'required|numeric',
        ]);

        PedomanDenda::create([
            'lomba_id' => $this->selected_lomba_id,
            'nama_pelanggaran' => $this->nama_pelanggaran,
            'poin_minus' => $this->poin_master
        ]);

        $this->reset(['nama_pelanggaran', 'poin_master']);
        session()->flash('message', 'Pedoman berhasil ditambah!');
    }

    public function hapusMaster($id) 
    { 
        PedomanDenda::find($id)->delete(); 
    }
}