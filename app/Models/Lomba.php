<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lomba extends Model
{
    // KITA HAPUS $fillable KARENA BIKIN ERROR SILENT!
    // Cukup gunakan $guarded = [] agar semua kolom dari form otomatis diizinkan masuk ke database
    protected $guarded = [];

    // Casts untuk memastikan array tie-breaker terbaca benar
    protected $casts = [
        'tie_breakers' => 'array',
        'urutan_juara' => 'array',
    ];
    
    // Paksa Laravel baca tabel 'lomba', bukan 'lombas'
    protected $table = 'lomba'; 

    public function pedomanDenda()
    {
        return $this->hasMany(PedomanDenda::class);
    }
}