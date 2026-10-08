<?php

namespace App\Services;

/**
 * Statistik spasial ringan (tanpa library eksternal):
 *  - Centroid tiap kecamatan dari GeoJSON
 *  - Matriks bobot knn (k tetangga terdekat, row-standardized)
 *  - Global Moran's I + uji permutasi
 *  - LISA (Local Moran's I) untuk klasifikasi hotspot: HH / HL / LH / LL
 */
class SpasialService
{
    private string $geojsonPath;

    public function __construct(?string $geojsonPath = null)
    {
        $this->geojsonPath = $geojsonPath ?? public_path('geojson/peta_kecamatan.geojson');
    }

    /** @return array<string, array{x:float,y:float}> key = kecamatan (UPPERCASE) */
    public function centroids(): array
    {
        $raw = file_get_contents($this->geojsonPath);
        $json = json_decode($raw, true);

        $result = [];

        foreach (($json['features'] ?? []) as $feature) {
            // kanonikal: uppercase + hapus semua spasi (GeoJSON memakai "PURWAREJAKLAMPOK" tanpa spasi)
            $name = strtoupper(preg_replace('/\s+/', '', (string) ($feature['properties']['Kecamatan'] ?? $feature['properties']['Name'] ?? '')));
            if ($name === '') {
                continue;
            }

            $points = [];
            $this->collectPoints($feature['geometry']['coordinates'] ?? [], $points);
            if (empty($points)) {
                continue;
            }

            $n = count($points);
            $sx = 0;
            $sy = 0;
            foreach ($points as $pt) {
                $sx += $pt[0];
                $sy += $pt[1];
            }

            $result[$name] = ['x' => $sx / $n, 'y' => $sy / $n];
        }

        return $result;
    }

    /** @return array<string, array<string, float>> matriks bobot baris-terstandarisasi */
    public function weightMatrix(array $centroids, int $k = 3): array
    {
        $names = array_keys($centroids);
        $adjacency = [];

        foreach ($names as $i) {
            $dists = [];
            foreach ($names as $j) {
                if ($i === $j) {
                    continue;
                }
                $dx = $centroids[$i]['x'] - $centroids[$j]['x'];
                $dy = $centroids[$i]['y'] - $centroids[$j]['y'];
                $dists[$j] = sqrt($dx * $dx + $dy * $dy);
            }
            asort($dists);
            $neighbors = array_slice(array_keys($dists), 0, $k, true);
            $adjacency[$i] = $neighbors;
        }

        // matriks bobot W (baris-terstandarisasi)
        $W = [];
        foreach ($adjacency as $i => $nbrs) {
            $sum = count($nbrs);
            foreach ($nbrs as $j) {
                $W[$i][$j] = 1 / $sum;
            }
        }

        return $W;
    }

    /** Global Moran's I. values = ['kecamatan' => float]. */
    public function globalMoransI(array $values, array $W): array
    {
        $names = array_keys($values);
        $n = count($names);
        $mean = array_sum($values) / $n;

        $z = [];
        foreach ($names as $i) {
            $z[$i] = $values[$i] - $mean;
        }

        $s0 = 0;
        $numerator = 0.0;
        $denom = 0.0;
        foreach ($names as $i) {
            $denom += $z[$i] * $z[$i];
            foreach (($W[$i] ?? []) as $j => $w) {
                $s0 += $w;
                $numerator += $w * $z[$i] * $z[$j];
            }
        }

        // s0 seharusnya = n untuk baris-terstandarisasi; amankan pembagian nol
        $I = $denom == 0 ? 0 : ($n / max($s0, 1e-9)) * ($numerator / $denom);

        // uji permutasi (999 ulangan)
        $count = 0;
        $iters = 999;
        for ($it = 0; $it < $iters; $it++) {
            $vals = array_values($values);
            shuffle($vals);
            $shuffled = array_combine($names, $vals);
            $permI = $this->moransIStatistic($names, $mean, $shuffled, $W, $s0);
            if (abs($permI) >= abs($I)) {
                $count++;
            }
        }

        return [
            'I' => $I,
            'expected' => -1 / ($n - 1),
            'p_value' => ($count + 1) / ($iters + 1),
            'interpretation' => $I > 0 ? 'Klaster spasial (autokorelasi positif)' : ($I < 0 ? 'Tersebar (autokorelasi negatif)' : 'Acak'),
        ];
    }

    /** LISA — klasifikasi hotspot tiap kecamatan. */
    public function lisa(array $values, array $W): array
    {
        $names = array_keys($values);
        $mean = array_sum($values) / count($names);

        $raw = [];
        foreach ($names as $i) {
            $zi = $values[$i] - $mean;
            $lag = 0.0;
            foreach (($W[$i] ?? []) as $j => $w) {
                $lag += $w * ($values[$j] - $mean);
            }
            $raw[$i] = $zi * $lag;
        }

        // standarisasi hanya untuk ambang tanda; klasifikasi berbasis tanda (zi, lag)
        $out = [];
        foreach ($names as $i) {
            $zi = $values[$i] - $mean;
            $lag = 0.0;
            foreach (($W[$i] ?? []) as $j => $w) {
                $lag += $w * ($values[$j] - $mean);
            }
            $out[$i] = [
                'zi' => $zi,
                'spatial_lag' => $lag,
                'type' => $this->classify($zi, $lag),
            ];
        }

        return $out;
    }

    private function classify(float $zi, float $lag): string
    {
        // hanya kategorikan jika |zi| dan |lag| melewati ambang kecil relatif
        $eps = 1e-9;
        if (abs($zi) < $eps) {
            return 'Tidak Signifikan';
        }

        return match (true) {
            $zi > 0 && $lag > 0 => 'HH (Hotspot)',
            $zi < 0 && $lag < 0 => 'LL (Coldspot)',
            $zi > 0 && $lag < 0 => 'HL (Outlier Tinggi)',
            default => 'LH (Outlier Rendah)',
        };
    }

    private function moransIStatistic(array $names, float $mean, array $values, array $W, float $s0): float
    {
        $n = count($names);
        $num = 0.0;
        $denom = 0.0;
        foreach ($names as $i) {
            $zi = $values[$i] - $mean;
            $denom += $zi * $zi;
            foreach (($W[$i] ?? []) as $j => $w) {
                $num += $w * $zi * ($values[$j] - $mean);
            }
        }

        return $denom == 0 ? 0 : ($n / max($s0, 1e-9)) * ($num / $denom);
    }

    /** Rekursif kumpulkan semua pasangan koordinat [lng, lat] (menangani Polygon & MultiPolygon). */
    private function collectPoints(array $node, array &$points): void
    {
        if (isset($node[0]) && is_numeric($node[0]) && isset($node[1]) && is_numeric($node[1])) {
            $points[] = [(float) $node[0], (float) $node[1]];

            return;
        }

        foreach ($node as $child) {
            if (is_array($child)) {
                $this->collectPoints($child, $points);
            }
        }
    }
}