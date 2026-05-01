<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPenilaian extends Model
{
    protected $table = 'kategori_penilaian';
    protected $guarded = [];

    // Relasi ke Lomba (Parent)
    public function lomba() 
    { 
        return $this->belongsTo(Lomba::class); 
    }

    // INI YANG KURANG TADI (Relasi ke ItemPenilaian/Child)
    public function items() 
    { 
        return $this->hasMany(ItemPenilaian::class); 
    }
}