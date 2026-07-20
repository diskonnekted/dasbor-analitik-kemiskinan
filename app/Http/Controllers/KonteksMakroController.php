<?php

namespace App\Http\Controllers;

use App\Models\IndikatorMakro;

class KonteksMakroController extends Controller
{
    /** Wilayah fokus dan urutan tampil pembanding. */
    private string $fokus = 'Banjarnegara';
    private array $pembanding = ['Cilacap', 'Purbalingga', 'Banyumas', 'Wonosobo', 'Jawa Tengah', 'Nasional'];

    /**
     * Indikator yang nilainya "lebih tinggi lebih baik".
     * Sisanya (kemiskinan, gini, inflasi) "lebih rendah lebih baik".
     */
    private array $higherBetter = [
        'Pengeluaran Perkapita',
        'Laju Pertumbuhan Ekonomi',
        'Indeks Pembangunan Manusia',
        'Harapan Lama Sekolah',
        'Rata-rata Lama Sekolah',
        'Usia Harapan Hidup',
    ];

    public function index()
    {
        if (IndikatorMakro::count() === 0) {
            return $this->responsiveView('makro', ['error' => 'Data indikator makro belum tersedia. Jalankan php artisan bps:import.']);
        }

        $tahunTerbaru = IndikatorMakro::max('tahun');
        $tahunAwal = IndikatorMakro::min('tahun');

        $indikatorMeta = IndikatorMakro::selectRaw('nama_indikator, kategori, satuan')
            ->groupBy('nama_indikator', 'kategori', 'satuan')
            ->orderBy('kategori')
            ->orderBy('nama_indikator')
            ->get();

        // --- 1. Kartu benchmarking per indikator (tahun terbaru per indikator) ---
        $kartu = [];
        foreach ($indikatorMeta as $meta) {
            $nama = $meta->nama_indikator;
            $maxTahun = IndikatorMakro::where('nama_indikator', $nama)->max('tahun');

            $nilaiWilayah = IndikatorMakro::where('nama_indikator', $nama)
                ->where('tahun', $maxTahun)
                ->whereNotNull('nilai')
                ->pluck('nilai', 'wilayah');

            $nilaiFokus = $nilaiWilayah[$this->fokus] ?? null;
            if ($nilaiFokus === null) {
                continue;
            }

            $higherBetter = in_array($nama, $this->higherBetter, true);

            // Peringkat Banjarnegara di antara 5 kabupaten (tanpa Jateng & Nasional)
            $kabupaten = $nilaiWilayah->except(['Jawa Tengah', 'Nasional']);
            $rank = $this->hitungPeringkat($kabupaten->toArray(), (float) $nilaiFokus, $higherBetter);

            $kartu[] = [
                'indikator' => $nama,
                'kategori' => $meta->kategori,
                'satuan' => $meta->satuan,
                'tahun' => $maxTahun,
                'nilai_fokus' => (float) $nilaiFokus,
                'nilai_jateng' => isset($nilaiWilayah['Jawa Tengah']) ? (float) $nilaiWilayah['Jawa Tengah'] : null,
                'nilai_nasional' => isset($nilaiWilayah['Nasional']) ? (float) $nilaiWilayah['Nasional'] : null,
                'higher_better' => $higherBetter,
                'rank' => $rank['rank'],
                'total' => $rank['total'],
                'vs_nasional' => $this->bandingkan((float) $nilaiFokus, $nilaiWilayah['Nasional'] ?? null, $higherBetter),
            ];
        }

        // --- 2. Data tren & perbandingan untuk indikator terpilih (default: IPM) ---
        $indikatorList = $indikatorMeta->pluck('nama_indikator')->toArray();
        $terpilih = request('indikator');
        if (!in_array($terpilih, $indikatorList, true)) {
            $terpilih = in_array('Indeks Pembangunan Manusia', $indikatorList, true)
                ? 'Indeks Pembangunan Manusia'
                : ($indikatorList[0] ?? null);
        }

        $chart = $this->siapkanChart($terpilih);

        // --- 3. Skor ringkas: berapa indikator Banjarnegara di atas nasional ---
        $unggul = 0;
        $tertinggal = 0;
        foreach ($kartu as $k) {
            if ($k['vs_nasional'] === 'unggul') $unggul++;
            elseif ($k['vs_nasional'] === 'tertinggal') $tertinggal++;
        }

        $ringkasan = [
            'tahun' => $tahunTerbaru,
            'rentang' => $tahunAwal . '–' . $tahunTerbaru,
            'jml_indikator' => count($kartu),
            'unggul' => $unggul,
            'tertinggal' => $tertinggal,
        ];

        return $this->responsiveView('makro', compact(
            'ringkasan', 'kartu', 'chart', 'indikatorList', 'terpilih'
        ));
    }

    /** Peringkat wilayah fokus di antara kabupaten (1 = terbaik). */
    private function hitungPeringkat(array $nilaiByWilayah, float $nilaiFokus, bool $higherBetter): array
    {
        $nilai = array_values($nilaiByWilayah);
        rsort($nilai);
        if (!$higherBetter) {
            sort($nilai); // lebih kecil = lebih baik → urut menaik
        }
        $total = count($nilai);
        $rank = 1;
        foreach ($nilai as $v) {
            if ($higherBetter ? ($v > $nilaiFokus) : ($v < $nilaiFokus)) {
                $rank++;
            }
        }
        return ['rank' => $rank, 'total' => $total];
    }

    /** Status Banjarnegara vs pembanding: unggul / tertinggal / setara. */
    private function bandingkan(float $fokus, $pembanding, bool $higherBetter): string
    {
        if ($pembanding === null) {
            return 'na';
        }
        $pembanding = (float) $pembanding;
        if (abs($fokus - $pembanding) < 0.0001) {
            return 'setara';
        }
        $lebihTinggi = $fokus > $pembanding;
        if ($higherBetter) {
            return $lebihTinggi ? 'unggul' : 'tertinggal';
        }
        return $lebihTinggi ? 'tertinggal' : 'unggul';
    }

    /** Siapkan data Chart.js: tren fokus + perbandingan tahun terbaru. */
    private function siapkanChart(?string $indikator): array
    {
        if ($indikator === null) {
            return ['labels' => [], 'trenFokus' => [], 'trenNasional' => [], 'trenJateng' => [],
                    'bandingLabels' => [], 'bandingNilai' => [], 'indikator' => null, 'satuan' => ''];
        }

        $meta = IndikatorMakro::where('nama_indikator', $indikator)->first();
        $satuan = $meta->satuan ?? '';

        // Tren tahunan: Banjarnegara vs Jateng vs Nasional
        $tahunList = IndikatorMakro::where('nama_indikator', $indikator)
            ->distinct()->orderBy('tahun')->pluck('tahun')->toArray();

        $ambilTren = function (string $wilayah) use ($indikator, $tahunList) {
            $data = IndikatorMakro::where('nama_indikator', $indikator)
                ->where('wilayah', $wilayah)
                ->pluck('nilai', 'tahun');
            return array_map(fn ($t) => isset($data[$t]) ? (float) $data[$t] : null, $tahunList);
        };

        // Perbandingan antar-wilayah pada tahun terbaru
        $maxTahun = IndikatorMakro::where('nama_indikator', $indikator)->max('tahun');
        $banding = IndikatorMakro::where('nama_indikator', $indikator)
            ->where('tahun', $maxTahun)
            ->whereNotNull('nilai')
            ->orderBy('nilai', 'desc')
            ->pluck('nilai', 'wilayah');

        return [
            'labels' => $tahunList,
            'trenFokus' => $ambilTren($this->fokus),
            'trenJateng' => $ambilTren('Jawa Tengah'),
            'trenNasional' => $ambilTren('Nasional'),
            'bandingLabels' => $banding->keys()->toArray(),
            'bandingNilai' => array_map(fn ($v) => (float) $v, $banding->values()->toArray()),
            'bandingTahun' => $maxTahun,
            'indikator' => $indikator,
            'satuan' => $satuan,
        ];
    }
}
