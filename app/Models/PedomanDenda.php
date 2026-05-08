<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedomanDenda extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk membuka kunci kolom
    protected $fillable = [
        'lomba_id',
        'nama_pelanggaran',
        'poin_minus'
    ];

    // Relasi ke Lomba (Opsional tapi bagus untuk ada)
    public function lomba()
    {
        return $this->belongsTo(Lomba::class);
    }
}