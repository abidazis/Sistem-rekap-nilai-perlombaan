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
        Schema::table('juri', function (Blueprint $table) {
            $table->json('kategori_ids')->nullable()->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('juri', function (Blueprint $table) {
            $table->dropColumn('kategori_ids');
        });
    }
};
