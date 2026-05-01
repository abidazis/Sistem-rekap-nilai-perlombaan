<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $table = 'peserta';
    protected $guarded = [];
    
    public function lomba() { return $this->belongsTo(Lomba::class); }

    // TAMBAHKAN INI:
    public function nilai() { return $this->hasMany(Nilai::class); }

    public function denda()
    {
        return $this->hasMany(Denda::class, 'peserta_id', 'id');
    }
}