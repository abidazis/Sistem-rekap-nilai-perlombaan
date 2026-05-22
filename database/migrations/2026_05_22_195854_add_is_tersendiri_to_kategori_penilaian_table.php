<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Paksa isi tabel 'kategori_penilaian' tanpa akhiran 's' sesuai database antum
        Schema::table('kategori_penilaian', function (Blueprint $table) {
            $table->boolean('is_tersendiri')->default(0)->after('is_umum');
        });
    }

    public function down(): void
    {
        Schema::table('kategori_penilaian', function (Blueprint $table) {
            $table->dropColumn('is_tersendiri');
        });
    }
};