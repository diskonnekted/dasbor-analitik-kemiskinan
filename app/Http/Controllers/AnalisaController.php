<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KesejahteraanKecamatan;
use App\Models\IndikatorKecamatan;

class AnalisaController extends Controller
{
    public function index(Request $request)
    {
        $tahunKemiskinan = KesejahteraanKecamatan::max('tahun');
        
        // Data Kemiskinan (Y) per kecamatan
        $kemiskinan = KesejahteraanKecamatan::where('tahun', $tahunKemiskinan)->get()->keyBy('kecamatan');
        
        if ($kemiskinan->isEmpty()) {
            return $this->responsiveView('analisa', ['error' => 'Data kemiskinan belum tersedia.']);
        }

        // Ambil daftar indikator unik
        $indikatorList = IndikatorKecamatan::select('nama_indikator', 'kategori')
                            ->distinct()
                            ->get();

        $hasilKorelasi = [];
        $scatterData = [];

        foreach ($indikatorList as $ind) {
            $nama = $ind->nama_indikator;
            $kategori = $ind->kategori;
            
            // Ambil data indikator per kecamatan untuk tahun terbaru yg ada datanya
            $tahunInd = IndikatorKecamatan::where('nama_indikator', $nama)->max('tahun');
            $dataIndikator = IndikatorKecamatan::where('nama_indikator', $nama)
                                               ->where('tahun', $tahunInd)
                                               ->get()->keyBy('kecamatan');
                                               
            $arrayX = [];
            $arrayY = [];
            $scatterPoints = [];

            foreach ($kemiskinan as $kecamatan => $kem) {
                // Desil 1 (Sangat Miskin)
                $yVal = $kem->desil_1;
                
                // Cari nilai indikator
                $xVal = isset($dataIndikator[$kecamatan]) ? $dataIndikator[$kecamatan]->nilai : null;
                
                if ($xVal !== null) {
                    $arrayX[] = $xVal;
                    $arrayY[] = $yVal;
                    $scatterPoints[] = [
                        'x' => $xVal,
                        'y' => $yVal,
                        'kecamatan' => $kecamatan
                    ];
                }
            }

            // Hitung Korelasi Pearson
            $r = $this->pearsonCorrelation($arrayX, $arrayY);
            
            // Evaluasi Kekuatan Hubungan
            $kekuatan = 'Sangat Lemah';
            $absR = abs($r);
            if ($absR > 0.8) $kekuatan = 'Sangat Kuat';
            elseif ($absR > 0.6) $kekuatan = 'Kuat';
            elseif ($absR > 0.4) $kekuatan = 'Sedang';
            elseif ($absR > 0.2) $kekuatan = 'Lemah';

            $arah = $r > 0 ? 'Searah (Positif)' : 'Berlawanan (Negatif)';

            $hasilKorelasi[] = [
                'indikator' => $nama,
                'kategori' => $kategori,
                'korelasi' => round($r, 3),
                'kekuatan' => $kekuatan,
                'arah' => $arah,
                'poin' => count($arrayX)
            ];

            $scatterData[$nama] = $scatterPoints;
        }

        // Urutkan berdasarkan kekuatan korelasi (absolut) tertinggi
        usort($hasilKorelasi, function($a, $b) {
            return abs($b['korelasi']) <=> abs($a['korelasi']);
        });

        return $this->responsiveView('analisa', compact('hasilKorelasi', 'scatterData', 'tahunKemiskinan'));
    }

    private function pearsonCorrelation($x, $y)
    {
        if(count($x) !== count($y) || count($x) === 0) return 0;
        
        $n = count($x);
        $mx = array_sum($x) / $n;
        $my = array_sum($y) / $n;
        
        $cov = 0;
        $varX = 0;
        $varY = 0;
        
        for($i = 0; $i < $n; $i++) {
            $dx = $x[$i] - $mx;
            $dy = $y[$i] - $my;
            
            $cov += $dx * $dy;
            $varX += pow($dx, 2);
            $varY += pow($dy, 2);
        }
        
        if ($varX * $varY == 0) return 0;
        return $cov / sqrt($varX * $varY);
    }
}
