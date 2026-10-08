<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KesejahteraanKecamatan;
use App\Models\BantuanSosial;

class ClusterController extends Controller
{
    public function index()
    {
        // Tahun jangkar = tahun terakhir yang datanya lengkap di tabel kesejahteraan DAN bansos.
        $maxTahun = (int) min(KesejahteraanKecamatan::max('tahun'), BantuanSosial::max('tahun'));
        $kesejahteraan = KesejahteraanKecamatan::where('tahun', $maxTahun)->get();
        $bansos = BantuanSosial::where('tahun', $maxTahun)->get()->keyBy('kecamatan');

        if ($kesejahteraan->isEmpty() || $bansos->isEmpty()) {
            return redirect()->back()->with('error', 'Data tidak cukup untuk klastering.');
        }

        $datasetMap = [];
        foreach ($kesejahteraan as $k) {
            $kec = strtoupper(trim($k->kecamatan));
            
            // Normalisasi typo
            if (in_array($kec, ['PUWONEGORO', 'PURWONEGORO'])) {
                $kec = 'PURWANEGARA';
            }

            // Cegah duplikasi nama kecamatan (ambil data pertama saja jika ada dobel)
            if (isset($datasetMap[$kec])) {
                continue;
            }

            $x = $k->desil_1; // X: Kesejahteraan Terendah (Desil 1, Jiwa)
            $y = isset($bansos[$kec]) ? $bansos[$kec]->jumlah_penerima : 0; // Y: Penerima Bansos (KK)
            
            $datasetMap[$kec] = [
                'kecamatan' => $kec,
                'x' => $x,
                'y' => $y,
                'cluster' => null
            ];
        }
        $dataset = array_values($datasetMap);

        // --- K-Means Algorithm (k=3) ---
        $k = 3;
        // Inisialisasi Centroid (random atau ambil sample)
        // Kita sorting berdasarkan X (kesejahteraan terendah) untuk menentukan centroid awal agar konsisten
        usort($dataset, function($a, $b) { return $a['x'] <=> $b['x']; });
        $n = count($dataset);
        
        $centroids = [
            ['x' => $dataset[0]['x'], 'y' => $dataset[0]['y']], // Centroid Rendah
            ['x' => $dataset[(int)floor($n/2)]['x'], 'y' => $dataset[(int)floor($n/2)]['y']], // Centroid Menengah
            ['x' => $dataset[$n-1]['x'], 'y' => $dataset[$n-1]['y']], // Centroid Tinggi
        ];

        $iterations = 0;
        $maxIterations = 100;
        $changed = true;

        while ($changed && $iterations < $maxIterations) {
            $changed = false;
            $iterations++;
            $clusterCounts = [0, 0, 0];
            $clusterSums = [
                ['x' => 0, 'y' => 0],
                ['x' => 0, 'y' => 0],
                ['x' => 0, 'y' => 0]
            ];

            // 1. Assign to nearest centroid
            foreach ($dataset as &$point) {
                $minDist = PHP_FLOAT_MAX;
                $clusterIndex = 0;

                for ($i = 0; $i < $k; $i++) {
                    // Euclidean distance
                    $dist = sqrt(pow($point['x'] - $centroids[$i]['x'], 2) + pow($point['y'] - $centroids[$i]['y'], 2));
                    if ($dist < $minDist) {
                        $minDist = $dist;
                        $clusterIndex = $i;
                    }
                }

                if ($point['cluster'] !== $clusterIndex) {
                    $point['cluster'] = $clusterIndex;
                    $changed = true;
                }

                $clusterCounts[$clusterIndex]++;
                $clusterSums[$clusterIndex]['x'] += $point['x'];
                $clusterSums[$clusterIndex]['y'] += $point['y'];
            }
            unset($point); // FIX PHP Reference Bug

            // 2. Recalculate centroids
            for ($i = 0; $i < $k; $i++) {
                if ($clusterCounts[$i] > 0) {
                    $centroids[$i]['x'] = $clusterSums[$i]['x'] / $clusterCounts[$i];
                    $centroids[$i]['y'] = $clusterSums[$i]['y'] / $clusterCounts[$i];
                }
            }
        }

        // Tentukan Label Prioritas berdasarkan nilai Centroid X (kesejahteraan terendah terbanyak)
        // Sort centroids index based on X
        $cLabels = [];
        foreach ($centroids as $idx => $c) {
            $cLabels[] = ['idx' => $idx, 'x' => $c['x']];
        }
        usort($cLabels, function($a, $b) { return $b['x'] <=> $a['x']; }); // Descending X
        
        $priorityLabels = [];
        $priorityLabels[$cLabels[0]['idx']] = 'Prioritas Tinggi';
        $priorityLabels[$cLabels[1]['idx']] = 'Prioritas Menengah';
        $priorityLabels[$cLabels[2]['idx']] = 'Prioritas Rendah';

        $colors = [];
        $colors[$cLabels[0]['idx']] = '#cf2d56'; // Tinggi — error
        $colors[$cLabels[1]['idx']] = '#f54e00'; // Menengah — primary
        $colors[$cLabels[2]['idx']] = '#1f8a65'; // Rendah — success

        // Group dataset for view
        $clustersGrouped = [
            'Tinggi' => [],
            'Menengah' => [],
            'Rendah' => []
        ];

        // Format for Chart.js
        $chartDatasets = [
            ['label' => 'Prioritas Tinggi', 'data' => [], 'backgroundColor' => '#cf2d56'],
            ['label' => 'Prioritas Menengah', 'data' => [], 'backgroundColor' => '#f54e00'],
            ['label' => 'Prioritas Rendah', 'data' => [], 'backgroundColor' => '#1f8a65'],
        ];

        foreach ($dataset as $point) {
            $cIdx = $point['cluster'];
            
            if ($cIdx === $cLabels[0]['idx']) {
                $clustersGrouped['Tinggi'][] = $point;
                $chartDatasets[0]['data'][] = ['x' => $point['x'], 'y' => $point['y'], 'kecamatan' => $point['kecamatan']];
            } elseif ($cIdx === $cLabels[1]['idx']) {
                $clustersGrouped['Menengah'][] = $point;
                $chartDatasets[1]['data'][] = ['x' => $point['x'], 'y' => $point['y'], 'kecamatan' => $point['kecamatan']];
            } else {
                $clustersGrouped['Rendah'][] = $point;
                $chartDatasets[2]['data'][] = ['x' => $point['x'], 'y' => $point['y'], 'kecamatan' => $point['kecamatan']];
            }
        }

        return view('cluster', compact('clustersGrouped', 'chartDatasets', 'maxTahun', 'iterations'));
    }
}
