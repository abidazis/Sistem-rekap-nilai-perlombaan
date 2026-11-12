<?php

namespace Tests;

use App\Models\Denda;
use App\Models\ItemPenilaian;
use App\Models\Juri;
use App\Models\KategoriPenilaian;
use App\Models\Lomba;
use App\Models\Nilai;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Support\Str;

trait CreatesScenario
{
    protected function makeUser(string $username = 'admin', string $role = 'admin', string $password = 'password123'): User
    {
        return User::create([
            'name' => ucfirst($username),
            'username' => $username,
            'email' => $username . '@pandara.local',
            'password' => $password,
            'role' => $role,
        ]);
    }

    protected function makeEvent(array $attributes = []): Lomba
    {
        return Lomba::create(array_merge([
            'nama_lomba' => 'Lomba Uji ' . Str::random(4),
            'tanggal_pelaksanaan' => '2026-11-10',
            'lokasi' => 'Jakarta',
            'status_aktif' => true,
            'durasi_maksimal_detik' => 600,
            'format_juara' => 'all_harapan',
            'kuota_juara' => 0,
            'urutan_juara' => ['Juara Utama 1', 'Juara Utama 2', 'Juara Utama 3'],
            'tie_breakers' => [],
        ], $attributes));
    }

    protected function makeKategori(
        Lomba $lomba,
        string $nama,
        bool $utama = false,
        bool $umum = false,
        bool $tersendiri = false,
        float $bobot = 20
    ): KategoriPenilaian {
        return KategoriPenilaian::create([
            'lomba_id' => $lomba->id,
            'nama_kategori' => strtoupper($nama),
            'bobot_persen' => $tersendiri ? 0 : $bobot,
            'is_utama' => $tersendiri ? 0 : ($utama ? 1 : 0),
            'is_umum' => $tersendiri ? 0 : ($umum ? 1 : 0),
            'is_tersendiri' => $tersendiri ? 1 : 0,
        ]);
    }

    protected function makeItem(KategoriPenilaian $kategori, string $nama = 'GERAKAN', array $opsi = ['16', '18', '20'], int $urutan = 1): ItemPenilaian
    {
        return ItemPenilaian::create([
            'kategori_penilaian_id' => $kategori->id,
            'nama_gerakan' => strtoupper($nama),
            'urutan' => $urutan,
            'opsi_nilai' => $opsi,
        ]);
    }

    protected function makePeserta(Lomba $lomba, int $no = 1, string $tingkat = 'SMP', string $nama = null): Peserta
    {
        return Peserta::create([
            'lomba_id' => $lomba->id,
            'no_urut' => $no,
            'nama_sekolah' => strtoupper($nama ?? ('SEKOLAH ' . $no)),
            'nama_danton' => 'DANTON ' . $no,
            'tingkat' => $tingkat,
        ]);
    }

    protected function makeJuri(Lomba $lomba, string $nama, array $kategoriIds = [], string $posisi = 'Juri', string $username = null): Juri
    {
        return Juri::create([
            'lomba_id' => $lomba->id,
            'nama' => $nama,
            'posisi' => $posisi,
            'username' => $username ?? ('juri_' . Str::random(8)),
            'password' => bcrypt('password123'),
            'kategori_ids' => array_map('strval', $kategoriIds),
        ]);
    }

    protected function giveScore(Peserta $peserta, Juri $juri, ItemPenilaian $item, int $nilai): Nilai
    {
        return Nilai::create([
            'peserta_id' => $peserta->id,
            'juri_id' => $juri->id,
            'item_penilaian_id' => $item->id,
            'nilai' => $nilai,
        ]);
    }

    protected function giveDenda(Peserta $peserta, string $jenis = 'PELANGGARAN', int $poin = 5): Denda
    {
        return Denda::create([
            'peserta_id' => $peserta->id,
            'jenis_pelanggaran' => $jenis,
            'poin_minus' => $poin,
            'keterangan' => $jenis,
        ]);
    }
}
