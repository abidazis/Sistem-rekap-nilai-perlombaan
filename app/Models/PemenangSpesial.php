<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PemenangSpesial extends Model
{
    protected $guarded = [];

    public function peserta() {
        return $this->belongsTo(Peserta::class);
    }
    public function kategori() {
        return $this->belongsTo(KategoriPenilaian::class, 'kategori_penilaian_id');
    }
}