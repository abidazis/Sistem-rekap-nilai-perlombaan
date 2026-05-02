<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\MasterEvent;
use App\Livewire\MasterKategori;
use App\Livewire\MasterFormatNilai;
use App\Livewire\InputNilai;
use App\Livewire\MasterPeserta;
use App\Livewire\MasterJuri;
use App\Livewire\RekapJuara;
use App\Http\Controllers\CetakController;

// Redirect halaman awal ke Dashboard
Route::get('/', Dashboard::class)->name('dashboard');

// Menu-menu Master
Route::get('/master-event', MasterEvent::class)->name('master.event');
Route::get('/master-kategori', MasterKategori::class)->name('master.kategori');
Route::get('/master-format-nilai', MasterFormatNilai::class)->name('master.format');

// Menu Input Nilai (Operasional)
Route::get('/input-nilai', InputNilai::class)->name('input.nilai');

Route::get('/master-peserta', MasterPeserta::class)->name('master.peserta');

Route::get('/master-juri', MasterJuri::class)->name('master.juri');

Route::get('/rekap-juara', RekapJuara::class)->name('rekap.juara');

Route::get('/input-denda', \App\Livewire\InputDenda::class)->name('input.denda');

// Route untuk Cetak Rekapitulasi
Route::get('/cetak-klasemen/{lomba_id}', [CetakController::class, 'cetakKlasemen']);
Route::get('/cetak-peserta/{peserta_id}', [CetakController::class, 'cetakPeserta']);
Route::get('/cetak-utama/{lomba_id}', [CetakController::class, 'cetakUtama']);
Route::get('/cetak-kategori/{lomba_id}', [CetakController::class, 'cetakKategori']);
Route::get('/cetak-ljk/{lomba_id}', [CetakController::class, 'cetakLJK']);