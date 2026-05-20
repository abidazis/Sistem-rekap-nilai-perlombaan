<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $fillable = [
    'lomba_id', 
    'no_urut', 
    'nama_sekolah', 
    'nama_danton', 
    'status_tampil', 
    'durasi_tampil_detik', 
    'tingkat'
];
    protected $table = 'peserta';
    protected $guarded = [];
    
    public function lomba() { return $this->belongsTo(Lomba::class); }

    // TAMBAHKAN INI:
    public function nilai() {
        return $this->hasMany(Nilai::class, 'peserta_id');
    }

    public function denda()
    {
        return $this->hasMany(Denda::class, 'peserta_id', 'id');
    }
}