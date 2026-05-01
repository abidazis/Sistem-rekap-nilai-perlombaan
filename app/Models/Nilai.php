<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilai';
    protected $guarded = [];

    // Relasi ke Peserta
    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    // Relasi ke Juri
    public function juri()
    {
        return $this->belongsTo(Juri::class);
    }

    // 👇 INI DIA KABEL YANG COPOT TADI BRO! 👇
    public function item()
    {
        return $this->belongsTo(ItemPenilaian::class, 'item_penilaian_id', 'id');
    }
}