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
use App\Http\Controllers\LoginController;

Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/input-nilai', InputNilai::class)->name('input.nilai');
    Route::get('/rekap-juara', RekapJuara::class)->name('rekap.juara'); // Leaderboard

    // KHUSUS ADMIN (Posisi mengandung kata 'admin')
    Route::middleware('role:admin')->group(function () {
        Route::get('/master-event', MasterEvent::class)->name('master.event');
        Route::get('/master-kategori', MasterKategori::class)->name('master.kategori');
        Route::get('/master-format-nilai', MasterFormatNilai::class)->name('master.format');
        Route::get('/master-peserta', MasterPeserta::class)->name('master.peserta');
        Route::get('/master-juri', MasterJuri::class)->name('master.juri');
        Route::get('/input-denda', \App\Livewire\InputDenda::class)->name('input.denda');
        
        // Cetak-cetak
        Route::get('/export-excel/{lomba_id}', [CetakController::class, 'exportExcel']);
    });
});

// Login & Logout (Buat controller/Livewire sederhana untuk ini)
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');