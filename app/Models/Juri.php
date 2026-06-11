<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Ganti ini
use Illuminate\Notifications\Notifiable;

class Juri extends Authenticatable // Ganti ini
{
    use Notifiable;

    protected $table = 'juri';
    protected $guarded = [];
    protected $fillable = [
        'lomba_id', 
        'nama', 
        'posisi', 
        'username',
        'password',
        'kategori_ids'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'kategori_ids' => 'array',
    ];

    public function lomba() 
    { 
        return $this->belongsTo(Lomba::class); 
    }
}