<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPenilaian extends Model
{
    use HasFactory;

    protected $table = 'item_penilaian';
    protected $guarded = [];

    // Mengubah JSON otomatis menjadi Array di Laravel
    protected $casts = [
        'opsi_nilai' => 'array',
    ];

    // 👇 PASTIKAN FUNGSI INI ADA 👇
    public function kategori()
    {
        return $this->belongsTo(KategoriPenilaian::class, 'kategori_penilaian_id', 'id');
    }
}