<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Melengkapi data resmi BPS yang belum ada di database.
     *
     * Sumber: BPS Kabupaten Banjarnegara
     *  - "Kabupaten Banjarnegara Dalam Angka 2025" (Tabel 4.4.1 & 4.4.2, 2016–2024)
     *  - "Kabupaten Banjarnegara Dalam Angka 2026" (Tabel 4.4.1, 4.4.2, & 4.4.3, 2017–2025)
     */
    public function up(): void
    {
        // --- Makro kemiskinan (jumlah penduduk miskin jiwa, %, P1, P2) ---
        $macro = [
            2016 => ['jumlah_penduduk_miskin' => 158200, 'persentase' => 17.46, 'indeks_kedalaman' => 3.03, 'indeks_keparahan' => 0.80],
            2017 => ['jumlah_penduduk_miskin' => 156800, 'persentase' => 17.21, 'indeks_kedalaman' => 3.25, 'indeks_keparahan' => 0.84],
            2025 => ['jumlah_penduduk_miskin' => 124660, 'persentase' => 13.28, 'indeks_kedalaman' => 1.80, 'indeks_keparahan' => 0.37],
        ];

        foreach ($macro as $tahun => $data) {
            DB::table('kemiskinan_data')->updateOrInsert(
                ['tahun' => $tahun],
                array_merge($data, ['updated_at' => now(), 'created_at' => now()])
            );
        }

        // --- P3KE 2025 (individu) per kecamatan: desil_1..desil_4 ---
        // Sumber: Kab. Banjarnegara Dalam Angka 2026, Tabel 4.4.3 (Hasil DTSEN/P3KE 2025).
        // Total kabupaten terverifikasi: D1 173.221, D2 132.974, D3 100.575, D4 84.496.
        $p3ke2025 = [
            'SUSUKAN'           => [15094, 9252, 6438, 5255],
            'PURWAREJA KLAMPOK' => [7443, 6091, 4233, 3627],
            'MANDIRAJA'         => [12260, 9357, 7031, 6049],
            'PURWANEGARA'       => [14492, 10192, 7527, 6173],
            'BAWANG'            => [7298, 6469, 5321, 4511],
            'BANJARNEGARA'      => [7116, 5804, 4921, 4441],
            'PAGEDONGAN'        => [8949, 5882, 4603, 3790],
            'SIGALUH'           => [4255, 3664, 2345, 2218],
            'MADUKARA'          => [6510, 5547, 4527, 3810],
            'BANJARMANGU'       => [7315, 6235, 4879, 4191],
            'WANADADI'          => [6153, 4785, 3452, 2645],
            'RAKIT'             => [8099, 6758, 5064, 4446],
            'PUNGGELAN'         => [18378, 13527, 10130, 8326],
            'KARANGKOBAR'       => [5709, 4426, 2914, 2814],
            'PAGENTAN'          => [7756, 5414, 3894, 3206],
            'PEJAWARAN'         => [10315, 7545, 5608, 4222],
            'BATUR'             => [5629, 5107, 4009, 3576],
            'WANAYASA'          => [10539, 8477, 6399, 4775],
            'KALIBENING'        => [7016, 5876, 5191, 4377],
            'PANDANARUM'        => [2895, 2566, 2089, 2044],
        ];

        foreach ($p3ke2025 as $kecamatan => [$d1, $d2, $d3, $d4]) {
            DB::table('kesejahteraan_kecamatans')->updateOrInsert(
                ['kecamatan' => $kecamatan, 'tahun' => 2025],
                [
                    'desil_1' => $d1,
                    'desil_2' => $d2,
                    'desil_3' => $d3,
                    'desil_4' => $d4,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        // Balikkan hanya baris yang ditambahkan.
        DB::table('kemiskinan_data')->whereIn('tahun', [2016, 2017, 2025])->delete();
        DB::table('kesejahteraan_kecamatans')->where('tahun', 2025)->delete();
    }
};