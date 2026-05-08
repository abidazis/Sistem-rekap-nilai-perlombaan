<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pedoman_dendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lomba_id')->constrained('lomba')->onDelete('cascade');
            $table->string('nama_pelanggaran'); // Contoh: Terlambat daftar ulang
            $table->integer('poin_minus');      // Contoh: 10
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('pedoman_dendas');
    }
};
