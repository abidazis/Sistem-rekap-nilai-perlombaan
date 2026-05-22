<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pemenang_spesials', function (Blueprint $table) {
            $table->id();
            // Kita pakai unsignedBigInteger murni agar terhindar dari error tebakan nama tabel Laravel
            $table->unsignedBigInteger('lomba_id');
            $table->string('tingkat');
            $table->unsignedBigInteger('kategori_penilaian_id');
            $table->integer('rank'); 
            $table->unsignedBigInteger('peserta_id');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('pemenang_spesials');
    }
};
