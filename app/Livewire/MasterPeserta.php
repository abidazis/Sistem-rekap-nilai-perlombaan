<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads; // 👈 1. INI WAJIB ADA DI ATAS
use App\Models\Lomba;
use App\Models\Peserta;
use Livewire\Attributes\Layout;

class MasterPeserta extends Component
{
    use WithFileUploads; // 👈 2. DAN INI WAJIB ADA DI DALAM CLASS!

    public $selected_lomba_id;
    
    // Properti Import
    public $file_import_peserta; 

    // Properti Form Manual & Import
    public $peserta_id;
    public $no_urut;
    public $nama_sekolah;
    public $nama_danton;
    public $tingkat = 'SMP'; // 👈 DEFAULT TINGKAT

    public $is_create = false;
    public $is_edit = false;

    public function mount()
    {
        $latest = Lomba::latest()->first();
        if ($latest) {
            $this->selected_lomba_id = $latest->id;
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.master-peserta', [
            'events' => Lomba::latest()->get(),
            'pesertas' => Peserta::where('lomba_id', $this->selected_lomba_id)
                                 ->orderBy('tingkat', 'asc') // Urutkan tingkat dulu
                                 ->orderBy('no_urut', 'asc') // Baru urutkan nomornya
                                 ->get()
        ]);
    }

    // ==========================================
    // FITUR IMPORT CSV PESERTA
    // ==========================================
    public function importDataPeserta()
    {
        $this->validate([
            'selected_lomba_id' => 'required',
            'file_import_peserta' => 'required', 
            'tingkat' => 'required', // 👈 Pastikan tingkat dipilih sebelum import
        ], [
            'selected_lomba_id.required' => 'Pilih Event Lomba di atas dulu!',
            'file_import_peserta.required' => 'File belum siap! Tunggu tulisan loading hilang.',
            'tingkat.required' => 'Pilih Tingkat Sekolah dulu bro!',
        ]);

        try {
            $path = $this->file_import_peserta->getRealPath();
            $lines = file($path);

            $delimiter = ',';
            if (count($lines) > 0 && strpos($lines[0], ';') !== false) {
                $delimiter = ';';
            }

            if (count($lines) > 0 && !is_numeric(explode($delimiter, $lines[0])[0])) {
                array_shift($lines);
            }

            foreach ($lines as $line) {
                $row = str_getcsv($line, $delimiter);
                
                if (count($row) >= 2) {
                    $no_urut = trim($row[0]);
                    $nama_sekolah = trim($row[1]);
                    $nama_danton = isset($row[2]) ? trim($row[2]) : null;

                    if (!empty($nama_sekolah) && is_numeric($no_urut)) {
                        Peserta::updateOrCreate(
                            [
                                'lomba_id' => $this->selected_lomba_id,
                                'no_urut' => $no_urut, 
                                'tingkat' => $this->tingkat, // 👈 Pisahkan validasi per tingkat
                            ],
                            [
                                'nama_sekolah' => strtoupper($nama_sekolah),
                                'nama_danton' => strtoupper($nama_danton),
                            ]
                        );
                    }
                }
            }

            session()->flash('message', '🔥 BERHASIL! Data Peserta CSV telah di-import ke tingkat ' . $this->tingkat . '!');
            $this->file_import_peserta = null; 
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    // ==========================================
    // FITUR CRUD MANUAL (Jaga-jaga kalau ada peserta nyusul)
    // ==========================================
    public function create()
    {
        if(!$this->selected_lomba_id) {
            session()->flash('error', 'Pilih Event Lomba dulu!');
            return;
        }
        $this->resetInput();
        $this->is_create = true;
    }

    public function store()
    {
        $this->validate([
            'no_urut' => 'required|numeric',
            'nama_sekolah' => 'required',
            'tingkat' => 'required'
        ]);

        Peserta::create([
            'lomba_id' => $this->selected_lomba_id,
            'no_urut' => $this->no_urut,
            'nama_sekolah' => strtoupper($this->nama_sekolah),
            'nama_danton' => strtoupper($this->nama_danton),
            'tingkat' => $this->tingkat // 👈 SUNTIKAN TINGKAT
        ]);

        session()->flash('message', 'Peserta berhasil ditambahkan!');
        $this->cancel();
    }

    public function edit($id)
    {
        $peserta = Peserta::find($id);
        $this->peserta_id = $id;
        $this->no_urut = $peserta->no_urut;
        $this->nama_sekolah = $peserta->nama_sekolah;
        $this->nama_danton = $peserta->nama_danton;
        $this->tingkat = $peserta->tingkat ?? 'SMP'; // 👈 TARIK DATA TINGKAT

        $this->is_create = true;
        $this->is_edit = true;
    }

    public function update()
    {
        $this->validate([
            'no_urut' => 'required|numeric',
            'nama_sekolah' => 'required',
            'tingkat' => 'required'
        ]);

        Peserta::find($this->peserta_id)->update([
            'no_urut' => $this->no_urut,
            'nama_sekolah' => strtoupper($this->nama_sekolah),
            'nama_danton' => strtoupper($this->nama_danton),
            'tingkat' => $this->tingkat // 👈 UPDATE DATA TINGKAT
        ]);

        session()->flash('message', 'Peserta berhasil diupdate!');
        $this->cancel();
    }

    public function delete($id)
    {
        Peserta::find($id)->delete();
        session()->flash('message', 'Peserta berhasil dihapus!');
    }

    public function cancel()
    {
        $this->is_create = false;
        $this->is_edit = false;
        $this->resetInput();
    }

    private function resetInput()
    {
        $this->peserta_id = null;
        $this->no_urut = '';
        $this->nama_sekolah = '';
        $this->nama_danton = '';
        $this->tingkat = 'SMP'; // 👈 KEMBALIKAN KE DEFAULT
    }
}