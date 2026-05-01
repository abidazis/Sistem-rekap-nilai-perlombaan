<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel LOMBA
        Schema::create('lomba', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lomba');
            $table->date('tanggal_pelaksanaan');
            $table->string('lokasi')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->integer('durasi_maksimal_detik')->default(600);
            $table->timestamps();
        });

        // 2. Tabel PESERTA
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lomba_id')->constrained('lomba')->onDelete('cascade');
            $table->integer('no_urut');
            $table->string('nama_sekolah');
            $table->string('nama_danton')->nullable();
            $table->integer('total_nilai_murni')->default(0);
            $table->integer('total_minus')->default(0);
            $table->integer('nilai_akhir')->default(0);
            $table->integer('durasi_tampil_detik')->default(0);
            $table->enum('status_tampil', ['belum', 'tampil', 'selesai'])->default('belum');
            $table->timestamps();
        });

        // 3. Tabel JURI
        Schema::create('juri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lomba_id')->constrained('lomba')->onDelete('cascade');
            $table->string('nama');
            $table->string('posisi')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->timestamps();
        });

        // 4. Tabel KATEGORI PENILAIAN
        Schema::create('kategori_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lomba_id')->constrained('lomba')->onDelete('cascade');
            $table->string('nama_kategori'); // PBB, VARIASI, DANTON
            $table->decimal('bobot_persen', 5, 2); // 70.00
            $table->timestamps();
        });

        // 5. Tabel ITEM PENILAIAN
        Schema::create('item_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_penilaian_id')->constrained('kategori_penilaian')->onDelete('cascade');
            $table->string('nama_gerakan');
            $table->integer('urutan')->default(1);
            $table->json('opsi_nilai')->nullable(); // Dropdown [16,18,20...]
            $table->integer('nilai_maksimal')->default(0);
            $table->timestamps();
        });

        // 6. Tabel NILAI
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->onDelete('cascade');
            $table->foreignId('juri_id')->constrained('juri')->onDelete('cascade');
            $table->foreignId('item_penilaian_id')->constrained('item_penilaian')->onDelete('cascade');
            $table->integer('nilai');
            $table->timestamps();
            $table->index(['peserta_id', 'juri_id']);
        });

        // 7. Tabel DENDA
        Schema::create('denda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->onDelete('cascade');
            $table->string('jenis_pelanggaran');
            $table->integer('poin_minus');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denda');
        Schema::dropIfExists('nilai');
        Schema::dropIfExists('item_penilaian');
        Schema::dropIfExists('kategori_penilaian');
        Schema::dropIfExists('juri');
        Schema::dropIfExists('peserta');
        Schema::dropIfExists('lomba');
    }
};