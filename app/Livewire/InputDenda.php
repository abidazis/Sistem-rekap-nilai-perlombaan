<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lomba;
use App\Models\Peserta;
use App\Models\Denda;
use Livewire\Attributes\Layout;

class InputDenda extends Component
{
    public $selected_lomba_id;
    public $selected_peserta_id;
    
    // Form Input
    public $jenis_pelanggaran = '';
    public $poin_minus;
    public $keterangan;

    public function mount()
    {
        $latest = Lomba::latest()->first();
        if($latest) {
            $this->selected_lomba_id = $latest->id;
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $pesertas = [];
        $denda_list = [];

        if($this->selected_lomba_id) {
            $pesertas = Peserta::where('lomba_id', $this->selected_lomba_id)->orderBy('no_urut', 'asc')->get();
        }

        if($this->selected_peserta_id) {
            $denda_list = Denda::where('peserta_id', $this->selected_peserta_id)->latest()->get();
        }

        return view('livewire.input-denda', [
            'events' => Lomba::latest()->get(),
            'pesertas' => $pesertas,
            'denda_list' => $denda_list
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
        
        // Reset form
        $this->jenis_pelanggaran = '';
        $this->poin_minus = '';
        $this->keterangan = '';
    }

    public function hapus($id)
    {
        Denda::find($id)->delete();
        session()->flash('message', 'Data denda dibatalkan/dihapus.');
    }
}