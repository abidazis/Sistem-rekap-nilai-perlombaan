<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom username dan role
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('role')->default('juri')->after('password');
            
            // Opsional: Buat kolom email jadi nullable agar tidak wajib diisi ke depannya
            $table->string('email')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role']);
        });
    }
};