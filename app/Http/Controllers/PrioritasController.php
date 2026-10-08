<?php

namespace App\Http\Controllers;

use App\Models\AirBersihKecamatan;
use App\Models\BantuanSosial;
use App\Models\IndikatorKecamatan;
use App\Models\KesejahteraanKecamatan;
use App\Models\PmksKecamatan;
use App\Services\SpasialService;

class PrioritasController extends Controller
{
    private const KEMISKINAN_DIM = ['kemiskinan' => 0.40, 'pmks' => 0.20, 'bencana' => 0.20, 'air' => 0.10, 'nakes' => 0.10];

    public function index(SpasialService $spasial)
    {
        // Tahun jangkar = tahun data desil terbaru yang juga tersedia di seluruh tabel (2024).
        $tahun = (int) min(
            KesejahteraanKecamatan::max('tahun') ?? 2024,
            BantuanSosial::max('tahun') ?? 2024,
            PmksKecamatan::max('tahun') ?? 2024,
            AirBersihKecamatan::max('tahun') ?? 2024,
        );

        $desil = KesejahteraanKecamatan::where('tahun', $tahun)->get();
        $desilLalu = KesejahteraanKecamatan::whereIn('tahun', [$tahun - 2, $tahun])->get();

        $bansos = BantuanSosial::where('tahun', $tahun)->get()->keyBy('kecamatan');
        $pmks = PmksKecamatan::where('tahun', $tahun)->get()->keyBy('kecamatan');
        $air = AirBersihKecamatan::where('tahun', $tahun)->get()->keyBy('kecamatan');
        $bencana = $this->indikatorMap('Kejadian Bencana', $tahun);
        $nakes = $this->indikatorMap('Jumlah Tenaga Kesehatan', $tahun);

        $totalDesil1 = $desil->sum('desil_1');
        $totalPenerima = $bansos->sum('jumlah_penerima');

        // ===== 1. Targeting (ketepatan sasaran) =====
        $targeting = [];
        foreach ($desil as $d) {
            $kec = $d->kecamatan;
            $penerima = (int) ($bansos[$kec]->jumlah_penerima ?? 0);
            $anggaran = (int) ($bansos[$kec]->anggaran_ribu_rupiah ?? 0);

            $shareMiskin = $totalDesil1 > 0 ? $d->desil_1 / $totalDesil1 : 0;
            $sharePenerima = $totalPenerima > 0 ? $penerima / $totalPenerima : 0;
            $indeks = $shareMiskin > 0 ? $sharePenerima / $shareMiskin : 0;

            $targeting[] = [
                'kecamatan' => $kec,
                'desil_1' => (int) $d->desil_1,
                'penerima' => $penerima,
                'anggaran_ribu' => $anggaran,
                'indeks_cakupan' => round($indeks, 2),
                'status' => $this->statusCakupan($indeks),
                'bantuan_per_penerima' => $penerima > 0 ? round($anggaran / $penerima) : 0,
            ];
        }
        usort($targeting, fn ($a, $b) => $b['indeks_cakupan'] <=> $a['indeks_cakupan']);

        // ===== 2. IKK (Indeks Komposit Kemiskinan Kecamatan) =====
        $dimensiRaw = [];
        foreach ($desil as $d) {
            $kec = $d->kecamatan;
            $totalPop = $d->desil_1 + $d->desil_2 + $d->desil_3 + $d->desil_4;
            $dimensiRaw[$kec] = [
                'kemiskinan' => $totalPop > 0 ? $d->desil_1 / $totalPop * 100 : 0,       // % sangat miskin (semakin besar = buruk)
                'pmks' => $totalPop > 0 ? ($pmks[$kec]->jumlah ?? 0) / $totalPop * 1000 : 0, // per 1000 (semakin besar = buruk)
                'bencana' => (float) ($bencana[$kec] ?? 0),                               // jumlah kejadian (besar = buruk)
                'air' => $this->airScore($air[$kec] ?? null, $totalPop),                   // 0-100, besar = buruk (tidak terlayani)
                'nakes' => $totalPop > 0 ? ($nakes[$kec] ?? 0) / $totalPop * 1000 : 0,     // per 1000 (besar = bagus)
            ];
        }

        $nakesRaw = array_column($dimensiRaw, 'nakes');
        $ikk = [];
        foreach ($desil as $d) {
            $kec = $d->kecamatan;

            $skorKemiskinan = $this->minMaxNorm($dimensiRaw[$kec]['kemiskinan'], $dimensiRaw, 'kemiskinan');
            $skorPmks = $this->minMaxNorm($dimensiRaw[$kec]['pmks'], $dimensiRaw, 'pmks');
            $skorBencana = $this->minMaxNorm($dimensiRaw[$kec]['bencana'], $dimensiRaw, 'bencana');
            $skorAir = $dimensiRaw[$kec]['air']; // sudah 0-100 oleh airScore
            // nakes: semakin banyak semakin baik -> balik arah
            $skorNakes = $this->minMaxNormInvert($dimensiRaw[$kec]['nakes'], $dimensiRaw, 'nakes');

            $skor = $skorKemiskinan * self::KEMISKINAN_DIM['kemiskinan']
                + $skorPmks * self::KEMISKINAN_DIM['pmks']
                + $skorBencana * self::KEMISKINAN_DIM['bencana']
                + $skorAir * self::KEMISKINAN_DIM['air']
                + $skorNakes * self::KEMISKINAN_DIM['nakes'];

            $ikk[] = [
                'kecamatan' => $kec,
                'skor' => round($skor, 1),
                'kemiskinan' => round($skorKemiskinan, 1),
                'pmks' => round($skorPmks, 1),
                'bencana' => round($skorBencana, 1),
                'air' => round($skorAir, 1),
                'nakes' => round($skorNakes, 1),
                'desil_1_rate' => round($dimensiRaw[$kec]['kemiskinan'], 1),
            ];
        }
        usort($ikk, fn ($a, $b) => $b['skor'] <=> $a['skor']);
        $ikkRank = [];
        foreach ($ikk as $i => $row) {
            $ikkRank[$row['kecamatan']] = $i + 1;
        }

        // ===== 3. Matriks Prioritas 2x2 (tingkat x tren) =====
        $desilAwal = KesejahteraanKecamatan::where('tahun', $tahun - 2)->get()->keyBy('kecamatan');
        $prioritas = [];
        foreach ($desil as $d) {
            $kec = $d->kecamatan;
            $awal = (int) ($desilAwal[$kec]->desil_1 ?? $d->desil_1);
            $tren = $awal > 0 ? ($d->desil_1 - $awal) / $awal * 100 : 0;
            $prioritas[] = [
                'kecamatan' => $kec,
                'tingkat' => $dimensiRaw[$kec]['kemiskinan'],
                'tren' => round($tren, 1),
            ];
        }
        $medianTingkat = $this->median(array_column($prioritas, 'tingkat'));
        $medianTren = $this->median(array_column($prioritas, 'tren'));
        foreach ($prioritas as &$p) {
            $p['kuadran'] = $this->kuadran($p['tingkat'], $p['tren'], $medianTingkat, $medianTren);
        }
        unset($p);
        usort($prioritas, fn ($a, $b) => $b['tingkat'] <=> $a['tingkat']);

        // ===== 4. Matriks Kemiskinan x Bencana =====
        $bencanaMatrix = [];
        foreach ($desil as $d) {
            $kec = $d->kecamatan;
            $totalPop = $d->desil_1 + $d->desil_2 + $d->desil_3 + $d->desil_4;
            $bencanaMatrix[] = [
                'kecamatan' => $kec,
                'kemiskinan' => $totalPop > 0 ? $d->desil_1 / $totalPop * 100 : 0,
                'bencana' => (int) ($bencana[$kec] ?? 0),
            ];
        }

        // ===== 5. Hotspot spasial (Moran's I + LISA) =====
        $values = [];
        $kanonikalKeNama = [];
        foreach ($desil as $d) {
            $totalPop = $d->desil_1 + $d->desil_2 + $d->desil_3 + $d->desil_4;
            $kanonikal = strtoupper(preg_replace('/\s+/', '', $d->kecamatan));
            $values[$kanonikal] = $totalPop > 0 ? $d->desil_1 / $totalPop : 0;
            $kanonikalKeNama[$kanonikal] = $d->kecamatan;
        }
        $centroids = $spasial->centroids();
        $W = $spasial->weightMatrix($centroids, 3);
        $moransI = $spasial->globalMoransI($values, $W);
        $lisaRaw = $spasial->lisa($values, $W);
        $lisa = [];
        foreach ($lisaRaw as $kec => $info) {
            $lisa[] = [
                'kecamatan' => $kanonikalKeNama[$kec] ?? $kec,
                'type' => $info['type'],
                'zi' => round($info['zi'] * 100, 2),
            ];
        }
        usort($lisa, fn ($a, $b) => strcmp($a['type'], $b['type']));

        return view('prioritas', compact(
            'tahun',
            'targeting',
            'ikk',
            'ikkRank',
            'prioritas',
            'bencanaMatrix',
            'moransI',
            'lisa'
        ));
    }

    private function indikatorMap(string $namaIndikator, int $tahun): array
    {
        return IndikatorKecamatan::where('nama_indikator', $namaIndikator)
            ->where('tahun', $tahun)
            ->pluck('nilai', 'kecamatan')
            ->map(fn ($v) => (float) $v)
            ->toArray();
    }

    /** Skor akses air: 0 = terlayani penuh, 100 = tidak terlayani. */
    private function airScore($row, int $totalPop): float
    {
        $pelanggan = (int) ($row->pelanggan ?? 0);
        if ($pelanggan <= 0 || $totalPop <= 0) {
            return 100.0;
        }
        // cakupan normal: andai 1000 pelanggan / 1000 penduduk dianggap terlayani penuh
        $cakupan = min($pelanggan / $totalPop * 1000, 1000);

        return round(100 - ($cakupan / 10), 1); // 0-100, besar = buruk
    }

    private function minMaxNorm(float $value, array $data, string $key): float
    {
        $vals = array_map(fn ($d) => (float) $d[$key], $data);
        $min = min($vals);
        $max = max($vals);

        return $max - $min == 0 ? 0 : ($value - $min) / ($max - $min) * 100;
    }

    private function minMaxNormInvert(float $value, array $data, string $key): float
    {
        return 100 - $this->minMaxNorm($value, $data, $key);
    }

    private function statusCakupan(float $indeks): string
    {
        return match (true) {
            $indeks < 0.85 => 'Kurang Terjangkau',
            $indeks <= 1.15 => 'Proporsional',
            default => 'Lebih Terjangkau',
        };
    }

    private function kuadran(float $tingkat, float $tren, float $medTingkat, float $medTren): string
    {
        return match (true) {
            $tingkat >= $medTingkat && $tren >= $medTren => 'Prioritas Utama',
            $tingkat >= $medTingkat && $tren < $medTren => 'Membaik (Tetap Diawasi)',
            $tingkat < $medTingkat && $tren >= $medTren => 'Waspada (Mulai Naik)',
            default => 'Pantau (Stabil)',
        };
    }

    private function median(array $arr): float
    {
        sort($arr);
        $n = count($arr);
        if ($n === 0) {
            return 0;
        }
        $mid = intdiv($n, 2);

        return $n % 2 ? $arr[$mid] : ($arr[$mid - 1] + $arr[$mid]) / 2;
    }
}