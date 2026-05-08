<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lomba extends Model
{
    // INI SOLUSINYA: Paksa Laravel baca tabel 'lomba', bukan 'lombas'
    protected $table = 'lomba'; 
    
    protected $guarded = [];

    public function pedomanDenda()
    {
        return $this->hasMany(PedomanDenda::class);
    }
}

