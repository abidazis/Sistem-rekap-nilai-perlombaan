<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lomba;
use App\Models\Peserta;
use App\Models\KategoriPenilaian;
use App\Models\Nilai;
use App\Models\Denda;
use App\Models\Juri;
use Livewire\Attributes\Layout;

class RekapJuara extends Component
{
    public $selected_lomba_id;
    public $selected_tingkat = 'SMP';
    public $mode_tampilan = 'utama';

    public $format_juara = 'all_harapan';
    public $tie_breakers = [];

    /**
     * ============================================================
     * MOUNT
     * ============================================================
     */
    public function mount()
    {
        $latest = Lomba::latest()->first();

        if ($latest) {
            $this->selected_lomba_id = $latest->id;

            $this->loadPengaturan();
        }
    }

    /**
     * ============================================================
     * EVENT - GANTI LOMBA
     * ============================================================
     */
    public function updatedSelectedLombaId()
    {
        $this->loadPengaturan();

        // Jangan membawa kategori dari event sebelumnya
        $this->mode_tampilan = 'utama';
    }

    /**
     * ============================================================
     * LOAD PENGATURAN LOMBA
     * ============================================================
     */
    public function loadPengaturan()
    {
        $lomba = Lomba::find($this->selected_lomba_id);

        if ($lomba) {

            $this->format_juara =
                $lomba->format_juara ?? 'all_harapan';

            $this->tie_breakers =
                is_array($lomba->tie_breakers)
                    ? $lomba->tie_breakers
                    : [];

        } else {

            $this->format_juara = 'all_harapan';

            $this->tie_breakers = [];
        }
    }

    /**
     * ============================================================
     * AMBIL JURI YANG MEMANG DITUGASKAN KE KATEGORI
     * ============================================================
     *
     * Ini adalah FIX UTAMA.
     *
     * Contoh event 8:
     *
     * Kategori PBB = ID 53
     *
     * Juri:
     *
     * Juri 40:
     * ["54","53"]
     *
     * Juri 41:
     * ["53","54"]
     *
     * Juri 42:
     * ["57","58"]
     *
     * Maka untuk PBB:
     *
     * Juri 40 + Juri 41
     *
     * Juri 42 TIDAK BOLEH masuk.
     */
    private function getJuriUntukKategori($kategoriId)
    {
        if (!$this->selected_lomba_id) {
            return collect();
        }

        $juris = Juri::query()
            ->where(
                'lomba_id',
                $this->selected_lomba_id
            )
            ->get();

        return $juris->filter(function ($juri) use ($kategoriId) {

            /*
             * kategori_ids disimpan sebagai JSON.
             *
             * Contoh:
             *
             * ["54","53"]
             *
             * atau:
             *
             * ["57","58"]
             */

            $kategoriIds = $juri->kategori_ids;

            /*
             * Jika model belum melakukan cast JSON,
             * decode manual.
             */
            if (is_string($kategoriIds)) {

                $kategoriIds =
                    json_decode(
                        $kategoriIds,
                        true
                    );
            }

            if (!is_array($kategoriIds)) {
                return false;
            }

            /*
             * Samakan semua menjadi integer.
             */
            $kategoriIds = array_map(
                'intval',
                $kategoriIds
            );

            return in_array(
                (int) $kategoriId,
                $kategoriIds,
                true
            );
        })->values();
    }

    /**
     * ============================================================
     * HITUNG NILAI KATEGORI
     * ============================================================
     *
     * RUMUS FINAL:
     *
     * TOTAL KATEGORI =
     *
     * SUM nilai item
     * dari JURI yang ditugaskan
     * ke kategori tersebut.
     *
     * TIDAK ADA AVERAGE.
     *
     * TIDAK ADA PEMBAGIAN JUMLAH JURI.
     *
     * TIDAK MENGAMBIL JURI KATEGORI LAIN.
     */
    private function hitungNilaiKategori(
        $pesertaId,
        $kategori
    ) {

        /*
         * ========================================================
         * 1. AMBIL ITEM KATEGORI
         * ========================================================
         */
        $itemIds = $kategori->items
            ->pluck('id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->values()
            ->toArray();

        if (empty($itemIds)) {
            return 0;
        }

        /*
         * ========================================================
         * 2. AMBIL JURI KHUSUS KATEGORI INI
         * ========================================================
         */
        $juriKategori =
            $this->getJuriUntukKategori(
                $kategori->id
            );

        /*
         * Tidak ada juri untuk kategori.
         */
        if ($juriKategori->isEmpty()) {
            return 0;
        }

        /*
         * Ambil ID juri.
         */
        $juriIds = $juriKategori
            ->pluck('id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->values()
            ->toArray();

        /*
         * ========================================================
         * 3. SUM NILAI
         * ========================================================
         *
         * PERHATIKAN:
         *
         * whereIn juri_id WAJIB ada.
         *
         * Ini yang memperbaiki bug 1700 -> 1136.
         */
        $total = Nilai::query()
            ->where(
                'peserta_id',
                $pesertaId
            )
            ->whereIn(
                'juri_id',
                $juriIds
            )
            ->whereIn(
                'item_penilaian_id',
                $itemIds
            )
            ->sum('nilai');

        return (float) $total;
    }

    /**
     * ============================================================
     * HITUNG TOTAL MINUS / DENDA
     * ============================================================
     */
    private function hitungTotalMinus($pesertaId)
    {
        return (float) Denda::query()
            ->where(
                'peserta_id',
                $pesertaId
            )
            ->sum('poin_minus');
    }

    /**
     * ============================================================
     * RENDER
     * ============================================================
     */
    #[Layout('layouts.app')]
    public function render()
    {
        $pesertas = collect();

        $semua_kategori = collect();

        $kolom_kategori_tampil = collect();

        /*
         * ========================================================
         * EVENT DIPILIH
         * ========================================================
         */
        if ($this->selected_lomba_id) {

            /*
             * ====================================================
             * AMBIL SEMUA KATEGORI
             * ====================================================
             */
            $semua_kategori =
                KategoriPenilaian::query()
                    ->where(
                        'lomba_id',
                        $this->selected_lomba_id
                    )
                    ->with('items')
                    ->orderByDesc('bobot_persen')
                    ->orderBy('id')
                    ->get();

            /*
             * ====================================================
             * TENTUKAN KATEGORI YANG DITAMPILKAN
             * ====================================================
             */
            if ($this->mode_tampilan === 'utama') {

                $kolom_kategori_tampil =
                    $semua_kategori
                        ->where(
                            'is_utama',
                            true
                        )
                        ->values();

            } elseif (
                $this->mode_tampilan === 'umum'
            ) {

                $kolom_kategori_tampil =
                    $semua_kategori
                        ->where(
                            'is_umum',
                            true
                        )
                        ->values();

            } else {

                $kolom_kategori_tampil =
                    $semua_kategori
                        ->where(
                            'id',
                            (int) $this->mode_tampilan
                        )
                        ->values();
            }

            /*
             * ====================================================
             * AMBIL PESERTA
             * ====================================================
             */
            $all_peserta =
                Peserta::query()
                    ->where(
                        'lomba_id',
                        $this->selected_lomba_id
                    )
                    ->where(
                        'tingkat',
                        $this->selected_tingkat
                    )
                    ->orderBy(
                        'no_urut',
                        'asc'
                    )
                    ->get();

            /*
             * ====================================================
             * TIE BREAKER
             * ====================================================
             */
            $tieBreakersAktif =
                collect(
                    $this->tie_breakers
                )
                    ->filter(function ($id) {
                        return is_numeric($id);
                    })
                    ->map(function ($id) {
                        return (int) $id;
                    })
                    ->values()
                    ->toArray();

            /*
             * ====================================================
             * HITUNG PESERTA
             * ====================================================
             */
            $pesertas =
                $all_peserta->map(
                    function ($p)
                    use (
                        $semua_kategori,
                        $kolom_kategori_tampil
                    ) {

                        /*
                         * =========================================
                         * NILAI PER KATEGORI
                         * =========================================
                         */
                        $skorKategori = [];

                        foreach (
                            $semua_kategori
                            as $kategori
                        ) {

                            $skorKategori[
                                $kategori->id
                            ] =
                                $this->hitungNilaiKategori(
                                    $p->id,
                                    $kategori
                                );
                        }

                        /*
                         * =========================================
                         * TOTAL KOTOR
                         * =========================================
                         */
                        $totalKotor = 0;

                        foreach (
                            $kolom_kategori_tampil
                            as $kategoriTampil
                        ) {

                            $totalKotor +=
                                (float) (
                                    $skorKategori[
                                        $kategoriTampil->id
                                    ] ?? 0
                                );
                        }

                        /*
                         * =========================================
                         * DENDA
                         * =========================================
                         */
                        $totalMinus =
                            $this->hitungTotalMinus(
                                $p->id
                            );

                        /*
                         * =========================================
                         * TOTAL AKHIR
                         * =========================================
                         */
                        if (
                            $this->mode_tampilan !==
                                'utama'
                            &&
                            $this->mode_tampilan !==
                                'umum'
                        ) {

                            $totalSkor =
                                $totalKotor;

                        } else {

                            $totalSkor =
                                $totalKotor -
                                $totalMinus;
                        }

                        /*
                         * =========================================
                         * SIMPAN KE OBJECT
                         * =========================================
                         */
                        $p->skor_kategori =
                            $skorKategori;

                        $p->total_kotor =
                            (float) $totalKotor;

                        $p->total_minus =
                            (float) $totalMinus;

                        $p->total_skor =
                            (float) $totalSkor;

                        return $p;
                    }
                );

            /*
             * ====================================================
             * SORT RANKING
             * ====================================================
             */
            $pesertas =
                $pesertas
                    ->sort(
                        function ($a, $b)
                        use (
                            $tieBreakersAktif
                        ) {

                            /*
                             * TOTAL TERBESAR
                             */
                            if (
                                (float) $a->total_skor
                                !==
                                (float) $b->total_skor
                            ) {

                                return
                                    (float) $b->total_skor
                                    <=>
                                    (float) $a->total_skor;
                            }

                            /*
                             * TIE BREAKER
                             */
                            foreach (
                                $tieBreakersAktif
                                as $kategoriId
                            ) {

                                $nilaiA =
                                    (float) (
                                        $a->skor_kategori[
                                            $kategoriId
                                        ] ?? 0
                                    );

                                $nilaiB =
                                    (float) (
                                        $b->skor_kategori[
                                            $kategoriId
                                        ] ?? 0
                                    );

                                if (
                                    $nilaiA !==
                                    $nilaiB
                                ) {

                                    return
                                        $nilaiB
                                        <=>
                                        $nilaiA;
                                }
                            }

                            /*
                             * Kalau benar-benar sama,
                             * gunakan nomor urut.
                             */
                            return
                                ((int) $a->no_urut)
                                <=>
                                ((int) $b->no_urut);
                        }
                    )
                    ->values();
        }

        /*
         * ========================================================
         * RETURN VIEW
         * ========================================================
         */
        return view(
            'livewire.rekap-juara',
            [
                'events' =>
                    Lomba::query()
                        ->latest()
                        ->get(),

                'semua_kategori' =>
                    $semua_kategori,

                'kolom_kategori_tampil' =>
                    $kolom_kategori_tampil,

                'ranking_peserta' =>
                    $pesertas,
            ]
        );
    }
}