<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KemiskinanData;

class PrediksiController extends Controller
{
    public function index()
    {
        // Ambil data historis
        $data = KemiskinanData::orderBy('tahun', 'asc')->get();
        
        if ($data->count() < 2) {
            return redirect()->back()->with('error', 'Data historis tidak cukup untuk membuat prediksi.');
        }

        $years = [];
        $actualData = [];
        
        foreach ($data as $row) {
            $years[] = (int) $row->tahun;
            $actualData[] = (float) $row->jumlah_penduduk_miskin;
        }

        // --- Algoritma Simple Linear Regression (Least Squares Method) ---
        $n = count($years);
        $minYear = min($years);
        
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumXX = 0;

        for ($i = 0; $i < $n; $i++) {
            $x = $years[$i] - $minYear; // Normalisasi X agar perhitungan tidak overflow
            $y = $actualData[$i];
            
            $sumX += $x;
            $sumY += $y;
            $sumXY += ($x * $y);
            $sumXX += ($x * $x);
        }

        $denominator = ($n * $sumXX) - ($sumX * $sumX);
        
        if ($denominator == 0) {
            $slope = 0;
            $intercept = $sumY / $n;
        } else {
            $slope = (($n * $sumXY) - ($sumX * $sumY)) / $denominator;
            $intercept = ($sumY - ($slope * $sumX)) / $n;
        }

        // --- Proyeksi Masa Depan (Linear Regression) ---
        $maxYear = max($years);
        $futureYears = [$maxYear + 1, $maxYear + 2, $maxYear + 3];
        $predictionDataLR = [];
        
        foreach ($futureYears as $fy) {
            $x = $fy - $minYear;
            $predictedY = $intercept + ($slope * $x);
            // Angka kemiskinan tidak mungkin negatif
            $predictedY = max(0, $predictedY); 
            $predictionDataLR[] = round($predictedY);
        }

        // --- Algoritma Double Exponential Smoothing (Holt's Method) ---
        // Parameter penghalusan (0 < alpha, beta < 1)
        $alpha = 0.6; // Pengaruh data terbaru terhadap Level
        $beta = 0.4;  // Pengaruh perubahan terbaru terhadap Trend
        
        $level = [];
        $trend = [];
        $desFitted = []; // Prediksi algoritma DES pada data historis

        // Inisialisasi (Tahun pertama)
        $level[0] = $actualData[0];
        $trend[0] = (count($actualData) > 1) ? ($actualData[1] - $actualData[0]) : 0;
        $desFitted[0] = $actualData[0]; // Prediksi pertama sama dengan aktual
        
        for ($i = 1; $i < $n; $i++) {
            $currentY = $actualData[$i];
            
            $prevLevel = $level[$i - 1];
            $prevTrend = $trend[$i - 1];
            
            $level[$i] = ($alpha * $currentY) + ((1 - $alpha) * ($prevLevel + $prevTrend));
            $trend[$i] = ($beta * ($level[$i] - $prevLevel)) + ((1 - $beta) * $prevTrend);
            
            // Prediksi untuk tahun berjalan
            $desFitted[$i] = $prevLevel + $prevTrend;
        }

        $lastLevel = end($level);
        $lastTrend = end($trend);
        
        // Proyeksi Masa Depan (DES)
        $predictionDataDES = [];
        for ($m = 1; $m <= count($futureYears); $m++) {
            $predictedY = $lastLevel + ($m * $lastTrend);
            $predictedY = max(0, $predictedY);
            $predictionDataDES[] = round($predictedY);
        }

        // Siapkan data untuk Chart.js
        // Label gabungan: [2018, 2019, ... , 2024, 2025, 2026, 2027]
        $allLabels = array_merge($years, $futureYears);
        
        // Dataset aktual: [val1, val2, ..., val7, null, null, null]
        $chartActual = array_merge($actualData, array_fill(0, count($futureYears), null));
        
        // Dataset Prediksi Linear Regression (LR)
        $lastActual = end($actualData);
        $chartPredictionLR = array_fill(0, $n - 1, null);
        $chartPredictionLR[] = $lastActual; // titik sambung
        $chartPredictionLR = array_merge($chartPredictionLR, $predictionDataLR);

        // Dataset Prediksi Double Exponential Smoothing (DES)
        $chartPredictionDES = array_fill(0, $n - 1, null);
        $chartPredictionDES[] = $lastActual; // titik sambung
        $chartPredictionDES = array_merge($chartPredictionDES, $predictionDataDES);

        return $this->responsiveView('prediksi', compact(
            'years', 'actualData', 
            'futureYears', 'predictionDataLR', 'predictionDataDES',
            'allLabels', 'chartActual', 'chartPredictionLR', 'chartPredictionDES',
            'slope', 'lastTrend'
        ));
    }
}
