<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lomba', function (Blueprint $table) {
            if (!Schema::hasColumn('lomba', 'tie_breakers')) {
                $table->json('tie_breakers')->nullable()->after('kuota_juara');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lomba', function (Blueprint $table) {
            if (Schema::hasColumn('lomba', 'tie_breakers')) {
                $table->dropColumn('tie_breakers');
            }
        });
    }
};
