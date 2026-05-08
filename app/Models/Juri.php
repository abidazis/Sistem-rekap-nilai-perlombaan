<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juri extends Model
{
    protected $fillable = [
        'lomba_id', 
        'nama', 
        'posisi', 
        'username',
        'password',
        'kategori_ids'
    ];

    // TAMBAHKAN FUNGSI CAST INI AGAR OTOMATIS JADI ARRAY
    protected $casts = [
        'kategori_ids' => 'array',
    ];
    // INI KUNCINYA: Paksa baca tabel 'juri' (bukan 'juris')
    protected $table = 'juri'; 
    
    protected $guarded = [];

    // Relasi ke Lomba
    public function lomba() 
    { 
        return $this->belongsTo(Lomba::class); 
    }
}