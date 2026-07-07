<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Http;
use App\Models\KemiskinanData;

class SyncOpenData extends Command
{
    protected $signature = 'opendata:sync';
    protected $description = 'Sinkronisasi data kemiskinan dari Open Data Banjarnegara';

    public function handle()
    {
        $this->info('Memulai sinkronisasi dari Open Data Banjarnegara...');

        // Resource IDs
        $resPenduduk = '2e743a40-f0ce-4efa-94b5-8ae7a915ee31';
        $resKedalaman = '6ced48ec-f109-4488-ae71-a084e29603f1';
        $resKeparahan = '0f8d091d-54ed-4a3b-88ce-a228727f11e8';

        $baseUrl = 'https://opendata.banjarnegarakab.go.id/api/3/action/datastore_search';

        // 1. Fetch Penduduk Miskin (Total Banjarnegara only has Banjarnegara data based on schema)
        $this->info('Mengambil Data Penduduk Miskin...');
        $respPenduduk = Http::get($baseUrl, ['resource_id' => $resPenduduk]);
        $dataPenduduk = $respPenduduk->json('result.records') ?? [];

        foreach ($dataPenduduk as $row) {
            KemiskinanData::updateOrCreate(
                ['tahun' => $row['Tahun']],
                [
                    'jumlah_penduduk_miskin' => (float) $row['Jumlah Penduduk miskin (ribu jiwa)'] * 1000,
                    'persentase' => (float) $row['Prosentase penduduk miskin (persen)'],
                ]
            );
        }

        // 2. Fetch Indeks Kedalaman
        $this->info('Mengambil Data Indeks Kedalaman...');
        $respKedalaman = Http::get($baseUrl, ['resource_id' => $resKedalaman]);
        $dataKedalaman = $respKedalaman->json('result.records') ?? [];

        foreach ($dataKedalaman as $row) {
            if (($row['Kabupaten'] ?? '') === 'Banjarnegara') {
                KemiskinanData::updateOrCreate(
                    ['tahun' => $row['Tahun']],
                    [
                        'indeks_kedalaman' => (float) $row['Indeks Kedalaman Kemiskinan'],
                    ]
                );
            }
        }

        // 3. Fetch Indeks Keparahan
        $this->info('Mengambil Data Indeks Keparahan...');
        $respKeparahan = Http::get($baseUrl, ['resource_id' => $resKeparahan]);
        $dataKeparahan = $respKeparahan->json('result.records') ?? [];

        foreach ($dataKeparahan as $row) {
            if (($row['Kabupaten'] ?? '') === 'Banjarnegara') {
                KemiskinanData::updateOrCreate(
                    ['tahun' => $row['Tahun']],
                    [
                        'indeks_keparahan' => (float) $row['Indeks Keparahan Kemiskinan'],
                    ]
                );
            }
        }

        // 4. Fetch Kesejahteraan Individu per Kecamatan
        $this->info('Mengambil Data Kesejahteraan per Kecamatan...');
        $resKesejahteraan = '94913155-a8d5-448e-afd4-c6b939172635';
        $respKesejahteraan = Http::get($baseUrl, ['resource_id' => $resKesejahteraan, 'limit' => 1000]);
        $dataKesejahteraan = $respKesejahteraan->json('result.records') ?? [];

        foreach ($dataKesejahteraan as $row) {
            \App\Models\KesejahteraanKecamatan::updateOrCreate(
                [
                    'tahun' => $row['Tahun'],
                    'kecamatan' => strtoupper($row['Kecamatan'])
                ],
                [
                    'desil_1' => (int) $row['Status Kesejahteraan 1'],
                    'desil_2' => (int) $row['Status Kesejahteraan 2'],
                    'desil_3' => (int) $row['Status Kesejahteraan 3'],
                    'desil_4' => (int) $row['Status Kesejahteraan 4'],
                ]
            );
        }

        // 5. Fetch Bantuan Sosial (Bansos)
        $this->info('Mengambil Data Penerimaan Bansos...');
        $resBansos = '02e21318-bf27-4bd9-abab-a21ffa1a6993';
        $respBansos = Http::get($baseUrl, ['resource_id' => $resBansos, 'limit' => 1000]);
        $dataBansos = $respBansos->json('result.records') ?? [];

        foreach ($dataBansos as $row) {
            \App\Models\BantuanSosial::updateOrCreate(
                [
                    'tahun' => $row['Tahun'],
                    'kecamatan' => strtoupper($row['Kecamatan'])
                ],
                [
                    'jumlah_penerima' => (int) $row['Penerima Bantuan (Kepala Keluarga)'],
                    'anggaran_ribu_rupiah' => (int) $row['Anggaran Bantuan Sosial Pangan (ribu rupiah)'],
                ]
            );
        }

        // 6. Fetch Dataset Indikator Kecamatan (Analisa Korelasi)
        $this->info('Mengambil Data Indikator Kecamatan (Analisa Korelasi)...');

        // A. Dana Desa
        $respDanaDesa = Http::get($baseUrl, ['resource_id' => '525adb63-e41e-49a6-9067-f3a552e23649', 'limit' => 1000]);
        $dataDanaDesa = $respDanaDesa->json('result.records') ?? [];
        foreach ($dataDanaDesa as $row) {
            \App\Models\IndikatorKecamatan::updateOrCreate(
                ['tahun' => $row['Tahun'], 'kecamatan' => strtoupper($row['Kecamatan']), 'nama_indikator' => 'Alokasi Dana Desa'],
                ['kategori' => 'Pemerintah', 'nilai' => (float) $row['Alokasi Dana']]
            );
        }

        // B. Bencana Alam
        $respBencana = Http::get($baseUrl, ['resource_id' => '9f2daa38-dd6a-4df5-9b4e-cff693327c67', 'limit' => 1000]);
        $dataBencana = $respBencana->json('result.records') ?? [];
        foreach ($dataBencana as $row) {
            $totalBencana = 0;
            $fields = ['Banjir', 'Gempa Bumi', 'Tanah Longsor', 'Angin Kencang', 'Kebakaran'];
            foreach ($fields as $field) {
                $val = $row[$field] ?? '-';
                if ($val !== '-') $totalBencana += (int) $val;
            }
            \App\Models\IndikatorKecamatan::updateOrCreate(
                ['tahun' => $row['Tahun'], 'kecamatan' => strtoupper($row['Kecamatan']), 'nama_indikator' => 'Kejadian Bencana'],
                ['kategori' => 'Lingkungan', 'nilai' => $totalBencana]
            );
        }

        // C. Tenaga Kesehatan
        $respNakes = Http::get($baseUrl, ['resource_id' => 'd5bd29e4-1fe3-4876-9f1c-fa922bfebb21', 'limit' => 1000]);
        $dataNakes = $respNakes->json('result.records') ?? [];
        foreach ($dataNakes as $row) {
            \App\Models\IndikatorKecamatan::updateOrCreate(
                ['tahun' => $row['Tahun'], 'kecamatan' => strtoupper($row['Kecamatan']), 'nama_indikator' => 'Jumlah Tenaga Kesehatan'],
                ['kategori' => 'Kesehatan', 'nilai' => (int) $row['Total']]
            );
        }

        // D. Bantuan Sosial (from previous fetch $dataBansos)
        foreach ($dataBansos as $row) {
            \App\Models\IndikatorKecamatan::updateOrCreate(
                ['tahun' => $row['Tahun'], 'kecamatan' => strtoupper($row['Kecamatan']), 'nama_indikator' => 'Penerima Bantuan Sosial'],
                ['kategori' => 'Sosial', 'nilai' => (int) $row['Penerima Bantuan (Kepala Keluarga)']]
            );
        }

        $this->info('Sinkronisasi selesai!');
    }
}
