<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IndikatorKecamatan;
use App\Models\KesejahteraanKecamatan;

class ImportMasterData extends Command
{
    protected $signature = 'masterdata:import {--path= : Root folder master data (EXCEL + CSV)}';
    protected $description = 'Impor indikator per-kecamatan dari master data CSV ke tabel indikator_kecamatans';

    /**
     * Definisi dataset yang diimpor.
     * - file: path relatif terhadap root master data
     * - indikator: nama_indikator yang disimpan
     * - kategori: kategori indikator (untuk pengelompokan di Analisa)
     * - value_col: indeks kolom nilai (0-based); atau
     * - sum_cols: array indeks kolom yang dijumlahkan menjadi nilai
     * - year_col: indeks kolom tahun
     */
    private array $datasets = [
        [
            'file' => '07 DINSOS 2018-2024/Data Penyandang Masalah Sosial/Banyaknya Penyandang Masalah Sosial CSV.csv',
            'indikator' => 'Penyandang Masalah Sosial',
            'kategori' => 'Sosial',
            'value_col' => 6,
            'year_col' => 7,
        ],
        [
            'file' => '07 DINSOS 2018-2024/Data Penyandang Cacat Usia Produktif/Banyaknya Penyandang Cacat Usia Produktif CSV.csv',
            'indikator' => 'Penyandang Disabilitas Usia Produktif',
            'kategori' => 'Sosial',
            'sum_cols' => [1, 2, 3, 4],
            'year_col' => 5,
        ],
        [
            'file' => '10 Dispermades 2018-2024/Banyaknya Akseptor Aktif dan Akseptor Baru Dirinci per Kecamatan/Banyaknya Akseptor Aktif dan Akseptor Baru CSV.csv',
            'indikator' => 'Akseptor KB Aktif',
            'kategori' => 'Sosial',
            'value_col' => 1,
            'year_col' => 3,
        ],
        [
            'file' => '02 DINDIKPORA 2018-2024/Data Dindikpora SD Th.2016-2025/t_Jumlah_sekolah_sd_CSV.csv',
            'indikator' => 'Jumlah Murid SD',
            'kategori' => 'Pendidikan',
            'sum_cols' => [5, 6],
            'year_col' => 7,
        ],
        [
            'file' => '03. DINKES 2018-2023/Jumlah Rumah Sakit, Rumah Bersalin, Puskesmas, Klinik, Posyandu, dan Polindes/t_tempat_pelayanan_kesehatan_dinkes_CSV.csv',
            'indikator' => 'Jumlah Posyandu',
            'kategori' => 'Kesehatan',
            'value_col' => 4,
            'year_col' => 7,
        ],
        [
            'file' => '03. DINKES 2018-2023/Jumlah Kasus Penyakit/t_penyakit_dinkes_CSV.csv',
            'indikator' => 'Kasus TB Paru',
            'kategori' => 'Kesehatan',
            'value_col' => 2,
            'year_col' => 12,
        ],
    ];

    /** Alias nama kecamatan (tanpa spasi, uppercase) -> nama kanonik di database. */
    private array $kecAliases = [
        'PURWAREJAKLAMPOK' => 'PURWOREJA KLAMPOK',
        'PUWONEGORO' => 'PURWANEGARA',
        'PURWONEGORO' => 'PURWANEGARA',
    ];

    private array $canonicalByNoSpace = [];

    public function handle(): int
    {
        $root = $this->option('path') ?: 'I:\\analiotik\\master data\\EXCEL + CSV\\EXCEL + CSV';
        $root = rtrim($root, '\\/');

        $this->buildCanonicalMap();
        if (empty($this->canonicalByNoSpace)) {
            $this->error('Tabel kesejahteraan_kecamatans kosong. Jalankan opendata:sync dulu.');
            return self::FAILURE;
        }

        $totalRows = 0;
        foreach ($this->datasets as $ds) {
            $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $ds['file']);
            if (!is_file($path)) {
                $this->warn("Lewati (tidak ditemukan): {$ds['file']}");
                continue;
            }

            $rows = $this->importFile($path, $ds);
            $totalRows += $rows;
            $this->info("OK  {$ds['indikator']}: {$rows} baris");
        }

        $this->info("Selesai. Total {$totalRows} baris indikator diproses.");
        return self::SUCCESS;
    }

    private function buildCanonicalMap(): void
    {
        $names = KesejahteraanKecamatan::distinct()->pluck('kecamatan')->toArray();
        foreach ($names as $name) {
            $key = strtoupper(preg_replace('/\s+/', '', $name));
            $this->canonicalByNoSpace[$key] = strtoupper($name);
        }
    }

    private function normalizeKecamatan(string $raw): ?string
    {
        $key = strtoupper(preg_replace('/\s+/', '', trim($raw)));
        if ($key === '') {
            return null;
        }
        if (isset($this->kecAliases[$key])) {
            return $this->kecAliases[$key];
        }
        return $this->canonicalByNoSpace[$key] ?? null;
    }

    private function normalizeNumber(string $raw): float
    {
        $clean = trim($raw);
        if ($clean === '' || $clean === '-') {
            return 0.0;
        }
        // Hilangkan spasi & pemisah ribuan (koma), sisakan digit, titik, minus.
        $clean = str_replace([' ', ','], ['', ''], $clean);
        return is_numeric($clean) ? (float) $clean : 0.0;
    }

    private function normalizeYear(string $raw): ?int
    {
        // Tangani format "" 2,018 "" -> 2018
        $digits = preg_replace('/[^0-9]/', '', $raw);
        if (strlen($digits) < 4) {
            return null;
        }
        $year = (int) substr($digits, 0, 4);
        return ($year >= 2000 && $year <= 2100) ? $year : null;
    }

    private function importFile(string $path, array $ds): int
    {
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return 0;
        }

        $count = 0;
        $isHeader = true;
        while (($cols = fgetcsv($handle)) !== false) {
            if ($isHeader) {
                $isHeader = false;
                continue;
            }
            if (count($cols) < 2 || trim((string) $cols[0]) === '') {
                continue;
            }

            $kecamatan = $this->normalizeKecamatan((string) $cols[0]);
            $tahun = isset($cols[$ds['year_col']]) ? $this->normalizeYear((string) $cols[$ds['year_col']]) : null;
            if ($kecamatan === null || $tahun === null) {
                continue;
            }

            if (isset($ds['sum_cols'])) {
                $nilai = 0.0;
                foreach ($ds['sum_cols'] as $c) {
                    $nilai += $this->normalizeNumber((string) ($cols[$c] ?? ''));
                }
            } else {
                $nilai = $this->normalizeNumber((string) ($cols[$ds['value_col']] ?? ''));
            }

            IndikatorKecamatan::updateOrCreate(
                ['tahun' => $tahun, 'kecamatan' => $kecamatan, 'nama_indikator' => $ds['indikator']],
                ['kategori' => $ds['kategori'], 'nilai' => $nilai]
            );
            $count++;
        }

        fclose($handle);
        return $count;
    }
}
