<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lomba', function (Blueprint $table) {
            if (!Schema::hasColumn('lomba', 'kuota_juara')) {
                $table->integer('kuota_juara')->default(0)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('lomba', function (Blueprint $table) {
            if (Schema::hasColumn('lomba', 'kuota_juara')) {
                $table->dropColumn('kuota_juara');
            }
        });
    }
};