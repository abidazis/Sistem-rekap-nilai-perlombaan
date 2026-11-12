<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registrasi juri sekarang hanya menyimpan nama & jabatan,
     * tanpa akun login (username/password).
     */
    public function up(): void
    {
        Schema::table('juri', function (Blueprint $table) {
            $table->string('username')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('juri', function (Blueprint $table) {
            $table->string('username')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
