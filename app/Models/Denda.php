<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    use HasFactory;

    // INI KUNCINYA: Kasih tau Laravel nama tabel aslinya, jangan ditambah 's'!
    protected $table = 'denda'; 
    protected $guarded = [];

    // Relasi balik ke Peserta
    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }
}