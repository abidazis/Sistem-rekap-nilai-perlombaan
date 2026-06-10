<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemenang_spesials', function (Blueprint $table) {
            // Menambahkan kolom keterangan yang boleh kosong (nullable)
            $table->string('keterangan')->nullable()->after('peserta_id');
        });
    }

    public function down(): void
    {
        Schema::table('pemenang_spesials', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};