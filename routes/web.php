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
use App\Livewire\InputDenda;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\LoginController;

// ==========================================
// ROUTE LOGIN & LOGOUT (Bisa diakses tanpa login)
// ==========================================
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ==========================================
// ROUTE SISTEM UTAMA (Wajib Login)
// ==========================================
Route::middleware('auth')->group(function () {
    
    // Bisa diakses Admin & Tim Rekap
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/input-nilai', InputNilai::class)->name('input.nilai');
    Route::get('/rekap-juara', RekapJuara::class)->name('rekap.juara'); // Leaderboard

    // KHUSUS ADMIN (Akses Master Data & Cetak-cetak)
    Route::middleware('role:admin')->group(function () {
        Route::get('/master-event', MasterEvent::class)->name('master.event');
        Route::get('/master-kategori', MasterKategori::class)->name('master.kategori');
        Route::get('/master-format-nilai', MasterFormatNilai::class)->name('master.format');
        Route::get('/master-peserta', MasterPeserta::class)->name('master.peserta');
        Route::get('/master-juri', MasterJuri::class)->name('master.juri');
        Route::get('/input-denda', InputDenda::class)->name('input.denda');
        
        // Fitur Cetak & Export
        Route::get('/cetak-klasemen/{lomba_id}', [CetakController::class, 'cetakKlasemen']);
        Route::get('/cetak-peserta/{peserta_id}', [CetakController::class, 'cetakPeserta']);
        Route::get('/cetak-utama/{lomba_id}', [CetakController::class, 'cetakUtama']);
        Route::get('/cetak-kategori/{lomba_id}/{tingkat}', [CetakController::class, 'cetakKategori']);
        Route::get('/cetak-ljk/{lomba_id}', [CetakController::class, 'cetakLJK']);
        Route::get('/export-excel/{lomba_id}/{tingkat}', [CetakController::class, 'exportExcel']);
        Route::get('/export-pengumuman/{lomba_id}/{tingkat}', [App\Http\Controllers\CetakController::class, 'exportPengumuman']);
        Route::get('/cetak-pengumuman-pdf/{lomba_id}/{tingkat}', [App\Http\Controllers\CetakController::class, 'cetakPengumumanPDF']);
    });
});