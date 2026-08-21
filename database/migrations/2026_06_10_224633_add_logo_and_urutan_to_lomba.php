<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lomba', function (Blueprint $table) {
            if (!Schema::hasColumn('lomba', 'logo')) {
                $table->string('logo')->nullable()->after('lokasi');
            }
            if (!Schema::hasColumn('lomba', 'format_juara')) {
                $table->string('format_juara')->nullable()->after('logo');
            }
            if (!Schema::hasColumn('lomba', 'urutan_juara')) {
                $table->json('urutan_juara')->nullable()->after('format_juara');
            }
        });
    }

    public function down()
    {
        Schema::table('lomba', function (Blueprint $table) {
            if (Schema::hasColumn('lomba', 'urutan_juara')) {
                $table->dropColumn('urutan_juara');
            }
            if (Schema::hasColumn('lomba', 'format_juara')) {
                $table->dropColumn('format_juara');
            }
            if (Schema::hasColumn('lomba', 'logo')) {
                $table->dropColumn('logo');
            }
        });
    }
};
