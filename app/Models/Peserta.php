<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $fillable = [
    'lomba_id',
    'no_urut',
    'nama_sekolah',
    'nama_danton',
    'status_tampil',
    'durasi_tampil_detik',
    'tingkat'
];
    protected $table = 'peserta';
    protected $guarded = [];

    public function lomba() { return $this->belongsTo(Lomba::class); }

    public function nilai() {
        return $this->hasMany(Nilai::class, 'peserta_id');
    }

    public function denda()
    {
        return $this->hasMany(Denda::class, 'peserta_id', 'id');
    }

    // Accessor untuk format durasi menit:detik
    public function getDurasiFormatAttribute()
    {
        $detik = (int) ($this->durasi_tampil_detik ?? 0);
        if ($detik <= 0) {
            return '-';
        }
        $menit = floor($detik / 60);
        $sisa_detik = $detik % 60;
        return sprintf('%02d:%02d', $menit, $sisa_detik);
    }
}