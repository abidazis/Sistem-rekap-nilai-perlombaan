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

    // 👇 1. TAMBAHKAN KAMUS ATURAN DENDA EVENT INI DI SINI 👇
    public $daftar_pelanggaran = [
        'Terlambat saat daftar ulang' => 10,
        'Kekurangan anggota saat tampil' => 50,
        'Mengganti anggota secara ilegal' => 50,
        'Tidak mengikuti apel pembukaan' => 50,
        'Tidak memasuki DP I (3x panggilan)' => 100,
        'Tampil melebihi waktu' => 1,
        'Melewati garis batas' => 10,
        'Tidak mempunyai surat keterangan' => 30,
        'Administrasi daftar ulang tidak lengkap' => 50,
        'Peserta pingsan saat tampil' => 50,
        'Danton pingsan saat tampil' => 100,
        'Lainnya (Isi Manual)' => ''
    ];

    public function mount()
    {
        $latest = Lomba::latest()->first();
        if($latest) {
            $this->selected_lomba_id = $latest->id;
        }
    }

    // 👇 2. FUNGSI MAGIC LIVEWIRE: OTOMATIS MENGISI POIN SAAT DROPDOWN DIPILIH 👇
    public function updatedJenisPelanggaran($value)
    {
        // Jika pelanggaran ada di kamus, isi otomatis poinnya
        if (array_key_exists($value, $this->daftar_pelanggaran)) {
            $this->poin_minus = $this->daftar_pelanggaran[$value];
        } else {
            $this->poin_minus = null; // Kosongkan jika pilih "Lainnya"
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