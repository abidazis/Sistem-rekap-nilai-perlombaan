<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lomba', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('lokasi');
            $table->json('urutan_juara')->nullable()->after('format_juara');
        });
    }

    public function down()
    {
        Schema::table('lomba', function (Blueprint $table) {
            $table->dropColumn(['logo', 'urutan_juara']);
        });
    }
};
