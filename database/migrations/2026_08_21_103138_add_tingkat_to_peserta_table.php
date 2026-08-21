<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            $table->enum('tingkat', ['SMP', 'SMA'])->default('SMP')->after('nama_danton');
        });
    }

    public function down(): void
    {
        Schema::table('peserta', function (Blueprint $table) {
            $table->dropColumn('tingkat');
        });
    }
};
