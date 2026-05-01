<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads; // Wajib untuk import file
use App\Models\Lomba;
use App\Models\KategoriPenilaian;
use App\Models\ItemPenilaian;
use Livewire\Attributes\Layout;

class MasterFormatNilai extends Component
{
    use WithFileUploads;

    // Filter
    public $selected_lomba_id;
    public $selected_kategori_id;

    // Form Input Manual
    public $nama_gerakan;
    public $urutan = 1;
    public $item_id;
    
    // Properti Kotak Nilai
    public $nilai_ks;
    public $nilai_k;
    public $nilai_c;
    public $nilai_b;
    public $nilai_sb;

    // Properti Import CSV
    public $file_import;

    // Mode
    public $is_create = false;
    public $is_edit = false;

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
        if ($this->selected_lomba_id && KategoriPenilaian::where('lomba_id', $this->selected_lomba_id)->where('id', $this->selected_kategori_id)->doesntExist()) {
            $this->selected_kategori_id = null;
        }

        return view('livewire.master-format-nilai', [
            'events' => Lomba::latest()->get(),
            'kategoris' => KategoriPenilaian::where('lomba_id', $this->selected_lomba_id)->get(),
            'items' => ItemPenilaian::where('kategori_penilaian_id', $this->selected_kategori_id)
                       ->orderBy('urutan', 'asc')
                       ->get()
        ]);
    }

    public function create()
    {
        if(!$this->selected_kategori_id) {
            session()->flash('error', 'Pilih Kategori dulu bos!');
            return;
        }
        $this->resetInput();
        $lastItem = ItemPenilaian::where('kategori_penilaian_id', $this->selected_kategori_id)
                    ->orderBy('urutan', 'desc')->first();
        $this->urutan = $lastItem ? $lastItem->urutan + 1 : 1;
        
        $this->is_create = true;
    }

    public function store()
    {
        $this->validate([
            'nama_gerakan' => 'required',
            'urutan' => 'required|numeric',
        ]);

        $semua_nilai = $this->nilai_ks . ',' . $this->nilai_k . ',' . $this->nilai_c . ',' . $this->nilai_b . ',' . $this->nilai_sb;
        $opsi_array = $this->convertStringToArray($semua_nilai);

        if(empty($opsi_array)) {
            session()->flash('error', 'Minimal isi satu opsi nilai!');
            return;
        }

        ItemPenilaian::create([
            'kategori_penilaian_id' => $this->selected_kategori_id,
            'nama_gerakan' => strtoupper($this->nama_gerakan),
            'urutan' => $this->urutan,
            'opsi_nilai' => $opsi_array 
        ]);

        session()->flash('message', 'Item Penilaian Berhasil Ditambahkan!');
        $this->cancel();
    }

    public function edit($id)
    {
        $item = ItemPenilaian::find($id);
        $this->item_id = $id;
        $this->nama_gerakan = $item->nama_gerakan;
        $this->urutan = $item->urutan;
        
        $arr = $item->opsi_nilai ?? [];
        $this->resetKotakNilai();
        $this->nilai_ks = implode(', ', $arr);

        $this->is_create = true;
        $this->is_edit = true;
    }

    public function update()
    {
        $this->validate([
            'nama_gerakan' => 'required',
            'urutan' => 'required|numeric',
        ]);

        $semua_nilai = $this->nilai_ks . ',' . $this->nilai_k . ',' . $this->nilai_c . ',' . $this->nilai_b . ',' . $this->nilai_sb;
        $opsi_array = $this->convertStringToArray($semua_nilai);

        if(empty($opsi_array)) {
            session()->flash('error', 'Minimal isi satu opsi nilai!');
            return;
        }

        ItemPenilaian::find($this->item_id)->update([
            'nama_gerakan' => strtoupper($this->nama_gerakan),
            'urutan' => $this->urutan,
            'opsi_nilai' => $opsi_array
        ]);

        session()->flash('message', 'Item Berhasil Diupdate!');
        $this->cancel();
    }

    public function delete($id)
    {
        ItemPenilaian::find($id)->delete();
        session()->flash('message', 'Item Dihapus!');
    }

    public function cancel()
    {
        $this->is_create = false;
        $this->is_edit = false;
        $this->resetInput();
    }

    private function resetInput()
    {
        $this->nama_gerakan = '';
        $this->urutan = 1;
        $this->resetKotakNilai();
    }

    private function resetKotakNilai()
    {
        $this->nilai_ks = '';
        $this->nilai_k = '';
        $this->nilai_c = '';
        $this->nilai_b = '';
        $this->nilai_sb = '';
    }

    // ==========================================
    // FITUR IMPORT CSV
    // ==========================================
    public function importData()
    {
        $this->validate([
            'selected_kategori_id' => 'required',
            'file_import' => 'required', 
        ], [
            'selected_kategori_id.required' => 'Pilih Kategori di atas dulu!',
            'file_import.required' => 'File belum siap! Tunggu tulisan loading hilang, baru klik Import.',
        ]);

        try {
            $path = $this->file_import->getRealPath();
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
                
                if (count($row) >= 7) {
                    $urutan = trim($row[0]);
                    $nama_gerakan = trim($row[1]);
                    
                    $semua_nilai = trim($row[2]) . ' ' . trim($row[3]) . ' ' . trim($row[4]) . ' ' . trim($row[5]) . ' ' . trim($row[6]);
                    $opsi_array = $this->convertStringToArray($semua_nilai);

                    if (!empty($nama_gerakan) && count($opsi_array) > 0) {
                        ItemPenilaian::create([
                            'kategori_penilaian_id' => $this->selected_kategori_id,
                            'nama_gerakan' => strtoupper($nama_gerakan),
                            'urutan' => is_numeric($urutan) ? $urutan : 0,
                            'opsi_nilai' => $opsi_array
                        ]);
                    }
                }
            }

            session()->flash('message', '🔥 BERHASIL! Puluhan Gerakan CSV telah di-import dengan mulus!');
            $this->file_import = null; 
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    // FUNGSI PINTAR (HANYA ADA SATU FUNGSI INI SEKARANG)
    private function convertStringToArray($string)
    {
        $array = preg_split('/[\s,]+/', $string);
        $array = array_filter($array, function($value) {
            return $value !== ''; 
        });
        return array_values($array); 
    }
}