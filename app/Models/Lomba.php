<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lomba extends Model
{
    protected $fillable = [
        'nama_lomba', 
        'tanggal_pelaksanaan',
        'keterangan', 
        'status',
        'format_juara',
        'tie_breakers',
        'waktu_tampil'
    ];

    // TAMBAHKAN BLOK CASTS INI
    protected $casts = [
        'tie_breakers' => 'array',
    ];
    
    // INI SOLUSINYA: Paksa Laravel baca tabel 'lomba', bukan 'lombas'
    protected $table = 'lomba'; 
    
    protected $guarded = [];

    public function pedomanDenda()
    {
        return $this->hasMany(PedomanDenda::class);
    }
}

