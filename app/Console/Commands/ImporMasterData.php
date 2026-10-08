<?php

namespace App\Console\Commands;

use App\Models\AirBersihKecamatan;
use App\Models\PmksKecamatan;
use Illuminate\Console\Command;

class ImporMasterData extends Command
{
    protected $signature = 'masterdata:impor
        {--dir=storage/app/masterdata : Direktori berisi CSV master data}';

    protected $description = 'Impor data multidimensional (PMKS & akses air bersih) dari CSV master data';

    private const KECAMATAN_MAP = [
        'PWJ. KLAMPOK' => 'PURWAREJA KLAMPOK',
        'PURWONEGORO' => 'PURWANEGARA',
        'B A W A N G' => 'BAWANG',
        'BAWANG' => 'BAWANG',
        'BANJARNEGARA' => 'BANJARNEGARA',
        'BANJARMANGU' => 'BANJARMANGU',
        'MANDIRAJA' => 'MANDIRAJA',
        'SUSUKAN' => 'SUSUKAN',
        'WANADADI' => 'WANADADI',
        'PUNGGELAN' => 'PUNGGELAN',
        'KARANGKOBAR' => 'KARANGKOBAR',
        'PEJAWARAN' => 'PEJAWARAN',
        'WANAYASA' => 'WANAYASA',
        'KALIBENING' => 'KALIBENING',
    ];

    public function handle(): int
    {
        $dir = $this->option('dir');

        $this->imporPMKS($dir . '/pmks.csv');
        $this->imporAirBersih($dir . '/air_bersih.csv');

        $this->newLine();
        $this->info('Impor selesai.');
        $this->table(
            ['Tabel', 'Jumlah Baris'],
            [
                ['pmks_kecamatans', PmksKecamatan::count()],
                ['air_bersih_kecamatans', AirBersihKecamatan::count()],
            ]
        );

        return self::SUCCESS;
    }

    private function imporPMKS(string $file): void
    {
        if (! is_file($file)) {
            $this->error("File tidak ditemukan: {$file}");

            return;
        }

        $fh = fopen($file, 'r');
        fgetcsv($fh); // skip header
        $n = 0;

        while (($row = fgetcsv($fh)) !== false) {
            $kecamatan = strtoupper(trim($row[0] ?? ''));
            if ($kecamatan === '') {
                continue;
            }
            $tahun = (int) trim($row[7] ?? 0);
            if ($tahun < 2000) {
                continue;
            }

            PmksKecamatan::updateOrCreate(
                ['kecamatan' => $kecamatan, 'tahun' => $tahun],
                [
                    'tuna_susila' => $this->toInt($row[1] ?? null),
                    'cacat_netra' => $this->toInt($row[2] ?? null),
                    'cacat_bisu_tuli' => $this->toInt($row[3] ?? null),
                    'anak_terlantar' => $this->toInt($row[4] ?? null),
                    'lansia_terlantar' => $this->toInt($row[5] ?? null),
                    'jumlah' => $this->toInt($row[6] ?? null),
                ]
            );
            $n++;
        }
        fclose($fh);

        $this->info("PMKS: {$n} baris diimpor.");
    }

    private function imporAirBersih(string $file): void
    {
        if (! is_file($file)) {
            $this->error("File tidak ditemukan: {$file}");

            return;
        }

        $fh = fopen($file, 'r');
        fgetcsv($fh); // skip header
        $n = 0;

        while (($row = fgetcsv($fh)) !== false) {
            $kecamatan = $this->normalizeKecamatan($row[0] ?? '');
            if ($kecamatan === '') {
                continue;
            }
            $tahun = (int) trim($row[4] ?? 0);
            if ($tahun < 2000) {
                continue;
            }

            // Nilai (Rp) tahun 2018 keliru di sumber (menyalin kolom air disalurkan).
            $nilai = $tahun === 2018 ? null : $this->toIntNullable($row[3] ?? null);

            AirBersihKecamatan::updateOrCreate(
                ['kecamatan' => $kecamatan, 'tahun' => $tahun],
                [
                    'pelanggan' => $this->toIntNullable($row[1] ?? null),
                    'air_disalurkan_m3' => $this->toFloatNullable($row[2] ?? null),
                    'nilai_rupiah' => $nilai,
                ]
            );
            $n++;
        }
        fclose($fh);

        $this->info("Air bersih: {$n} baris diimpor.");
    }

    private function normalizeKecamatan(string $value): string
    {
        $upper = strtoupper(trim($value));

        return self::KECAMATAN_MAP[$upper] ?? $upper;
    }

    /** Ubah nilai teks menjadi integer; nilai kosong / '- ' menjadi 0. */
    private function toInt(?string $value): int
    {
        $clean = preg_replace('/[^0-9]/', '', (string) $value);

        return $clean === '' ? 0 : (int) $clean;
    }

    /** Ubah nilai teks menjadi integer nullable; nilai kosong / '- ' menjadi null. */
    private function toIntNullable(?string $value): ?int
    {
        $clean = preg_replace('/[^0-9]/', '', (string) $value);

        return $clean === '' ? null : (int) $clean;
    }

    private function toFloatNullable(?string $value): ?float
    {
        $clean = preg_replace('/[^0-9]/', '', (string) $value);

        return $clean === '' ? null : (float) $clean;
    }
}