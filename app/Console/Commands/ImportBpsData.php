<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IndikatorMakro;

class ImportBpsData extends Command
{
    protected $signature = 'bps:import {--path= : Root folder master data (EXCEL + CSV)}';
    protected $description = 'Impor indikator makro BPS (perbandingan antar-wilayah) ke tabel indikator_makros';

    /**
     * Definisi dataset BPS.
     * - file      : path relatif terhadap root master data
     * - indikator : nama_indikator yang disimpan
     * - kategori  : pengelompokan (Kemiskinan, Ekonomi, Pendidikan, Kesehatan, Ketimpangan)
     * - satuan    : satuan nilai untuk ditampilkan
     * - delimiter : pemisah kolom (default ',')
     * - year_col / value_col / region_col : indeks kolom (0-based)
     */
    private array $datasets = [
        [
            'file' => '47. BPS/Perbandingan Garis Kemiskinan/Data Garis Kemiskinan CSV.csv',
            'indikator' => 'Garis Kemiskinan',
            'kategori' => 'Kemiskinan',
            'satuan' => 'Rp/kapita/bln',
            'year_col' => 0, 'value_col' => 1, 'region_col' => 2,
        ],
        [
            'file' => '47. BPS/Perbandingan Indeks Kedalaman Kemiskinan/Data Indeks Kedalaman Kemiskinan CSV.csv',
            'indikator' => 'Indeks Kedalaman Kemiskinan (P1)',
            'kategori' => 'Kemiskinan',
            'satuan' => 'Indeks',
            'year_col' => 0, 'value_col' => 1, 'region_col' => 2,
        ],
        [
            'file' => '47. BPS/Perbandingan Indeks Keparahan Kemiskinan/Data Indeks Keparahan Kemiskinan CSV.csv',
            'indikator' => 'Indeks Keparahan Kemiskinan (P2)',
            'kategori' => 'Kemiskinan',
            'satuan' => 'Indeks',
            'year_col' => 0, 'value_col' => 1, 'region_col' => 2,
        ],
        [
            'file' => '47. BPS/Perbandingan Indeks Gini/Nilai Indeks Gini CSV.csv',
            'indikator' => 'Indeks Gini',
            'kategori' => 'Ketimpangan',
            'satuan' => 'Indeks',
            'year_col' => 0, 'value_col' => 1, 'region_col' => 2,
        ],
        [
            'file' => '47. BPS/Perbandingan Pengeluaran Perkapita/Pengeluaran Perkapita CSV.csv',
            'indikator' => 'Pengeluaran Perkapita',
            'kategori' => 'Ekonomi',
            'satuan' => 'Rp ribu/thn',
            'year_col' => 0, 'value_col' => 1, 'region_col' => 2,
        ],
        [
            'file' => '47. BPS/Laju Pertumbuhan Ekonomi Banjarengara/Laju Pertumbuhan Ekonomi CSV.csv',
            'indikator' => 'Laju Pertumbuhan Ekonomi',
            'kategori' => 'Ekonomi',
            'satuan' => '%',
            'year_col' => 0, 'value_col' => 1, 'region_col' => 2,
        ],
        [
            'file' => '47. BPS/Perbandingan Indeks Pembangunan Manusia/Nilai Indeks Pembangunan Manusia CSV.csv',
            'indikator' => 'Indeks Pembangunan Manusia',
            'kategori' => 'Pendidikan',
            'satuan' => 'Indeks',
            'year_col' => 0, 'value_col' => 1, 'region_col' => 2,
        ],
        [
            'file' => '47. BPS/Perbandingan Harapan Lama Sekolah/Data Harapan Lama Sekolah CSV.csv',
            'indikator' => 'Harapan Lama Sekolah',
            'kategori' => 'Pendidikan',
            'satuan' => 'Tahun',
            'year_col' => 0, 'value_col' => 1, 'region_col' => 2,
        ],
        [
            'file' => '47. BPS/Perbandingan Rata Rata Lama Sekolah/Data Rata-rata Lama Sekolah CSV.csv',
            'indikator' => 'Rata-rata Lama Sekolah',
            'kategori' => 'Pendidikan',
            'satuan' => 'Tahun',
            'year_col' => 0, 'value_col' => 1, 'region_col' => 2,
        ],
        [
            'file' => '47. BPS/Perbandingan Usia Harapan Hidup/Usia Harapan Hidup CSV.csv',
            'indikator' => 'Usia Harapan Hidup',
            'kategori' => 'Kesehatan',
            'satuan' => 'Tahun',
            'year_col' => 0, 'value_col' => 1, 'region_col' => 2,
        ],
        [
            'file' => '47. BPS/Perbandingan Laju Inflasi/Perbandingan Laju Inflasi CSV.csv',
            'indikator' => 'Laju Inflasi',
            'kategori' => 'Ekonomi',
            'satuan' => '%',
            'delimiter' => ';',
            'region_col' => 0, 'value_col' => 1, 'year_col' => 2,
        ],
    ];

    /** Normalisasi nama wilayah (perbaikan typo & standarisasi). */
    private array $wilayahAliases = [
        'JAWA TEGAH' => 'Jawa Tengah',
        'JAWA TENGAH' => 'Jawa Tengah',
        'NASIONAL' => 'Nasional',
        'BANJARNEGARA' => 'Banjarnegara',
        'CILACAP' => 'Cilacap',
        'PURBALINGGA' => 'Purbalingga',
        'BANYUMAS' => 'Banyumas',
        'WONOSOBO' => 'Wonosobo',
    ];

    public function handle(): int
    {
        $root = $this->option('path') ?: 'I:\\analiotik\\master data\\EXCEL + CSV\\EXCEL + CSV';
        $root = rtrim($root, '\\/');

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

        $this->info("Selesai. Total {$totalRows} baris indikator makro diproses.");
        return self::SUCCESS;
    }

    private function normalizeWilayah(string $raw): ?string
    {
        $key = strtoupper(trim(preg_replace('/\s+/', ' ', $raw)));
        if ($key === '') {
            return null;
        }
        return $this->wilayahAliases[$key] ?? ucwords(strtolower($key));
    }

    private function normalizeNumber(string $raw): ?float
    {
        $clean = trim($raw);
        if ($clean === '' || $clean === '-') {
            return null;
        }
        $clean = str_replace([' ', ','], ['', ''], $clean);
        return is_numeric($clean) ? (float) $clean : null;
    }

    private function normalizeYear(string $raw): ?int
    {
        $digits = preg_replace('/[^0-9]/', '', $raw);
        if (strlen($digits) < 4) {
            return null;
        }
        $year = (int) substr($digits, 0, 4);
        return ($year >= 2000 && $year <= 2100) ? $year : null;
    }

    private function importFile(string $path, array $ds): int
    {
        $delimiter = $ds['delimiter'] ?? ',';
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return 0;
        }

        $count = 0;
        $isHeader = true;
        while (($cols = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($isHeader) {
                $isHeader = false;
                continue;
            }
            if (count($cols) < 3) {
                continue;
            }

            $wilayah = $this->normalizeWilayah((string) ($cols[$ds['region_col']] ?? ''));
            $tahun = $this->normalizeYear((string) ($cols[$ds['year_col']] ?? ''));
            if ($wilayah === null || $tahun === null) {
                continue;
            }
            $nilai = $this->normalizeNumber((string) ($cols[$ds['value_col']] ?? ''));

            IndikatorMakro::updateOrCreate(
                ['tahun' => $tahun, 'wilayah' => $wilayah, 'nama_indikator' => $ds['indikator']],
                ['kategori' => $ds['kategori'], 'nilai' => $nilai, 'satuan' => $ds['satuan']]
            );
            $count++;
        }

        fclose($handle);
        return $count;
    }
}
