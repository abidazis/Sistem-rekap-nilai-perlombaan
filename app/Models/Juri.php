<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juri extends Model
{
    // INI KUNCINYA: Paksa baca tabel 'juri' (bukan 'juris')
    protected $table = 'juri'; 
    
    protected $guarded = [];

    // Relasi ke Lomba
    public function lomba() 
    { 
        return $this->belongsTo(Lomba::class); 
    }
}