<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KemiskinanData;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $selectedKecamatan = $request->query('kecamatan');
        $kecamatans = \App\Models\KesejahteraanKecamatan::distinct()->pluck('kecamatan');

        $historicalData = [];
        $baseYear = 2018;
        $lastYear = 2024;

        if (!$selectedKecamatan || $selectedKecamatan === 'Semua') {
            $kemiskinanAll = KemiskinanData::orderBy('tahun', 'asc')->get();
            if ($kemiskinanAll->isEmpty()) {
                return view('landing', ['timelineData' => '{}', 'tahunSekarang' => date('Y'), 'kecamatans' => $kecamatans, 'selectedKecamatan' => '']);
            }
            $baseYear = $kemiskinanAll->first()->tahun;
            $lastYear = $kemiskinanAll->last()->tahun;
            foreach ($kemiskinanAll as $data) {
                $historicalData[$data->tahun] = $data->jumlah_penduduk_miskin;
            }
        } else {
            // Data Per Kecamatan (hanya ada 2022-2024)
            $kesejahteraan = \App\Models\KesejahteraanKecamatan::where('kecamatan', $selectedKecamatan)->orderBy('tahun', 'asc')->get();
            if ($kesejahteraan->isEmpty()) {
                return view('landing', ['timelineData' => '{}', 'tahunSekarang' => date('Y'), 'kecamatans' => $kecamatans, 'selectedKecamatan' => $selectedKecamatan]);
            }
            $baseYear = $kesejahteraan->first()->tahun;
            $lastYear = $kesejahteraan->last()->tahun;
            foreach ($kesejahteraan as $data) {
                // desil_1 adalah penduduk sangat miskin
                $historicalData[$data->tahun] = $data->desil_1;
            }
        }

        // Kalkulasi Proyeksi (Least Squares)
        $n = count($historicalData);
        $sumX = 0; $sumY = 0; $sumXY = 0; $sumX2 = 0;
        
        foreach ($historicalData as $tahun => $jumlah) {
            $x = $tahun - $baseYear;
            $y = $jumlah;
            
            $sumX += $x;
            $sumY += $y;
            $sumXY += ($x * $y);
            $sumX2 += ($x * $x);
        }

        $b = 0;
        $a = $sumY / $n;
        if (($n * $sumX2 - $sumX * $sumX) != 0) {
            $b = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
            $a = ($sumY - $b * $sumX) / $n;
        }

        $timelineData = [];
        // Isi data historis
        foreach ($historicalData as $tahun => $jumlah) {
            $timelineData[$tahun] = $jumlah;
        }

        // Prediksi untuk 3 tahun ke depan
        for ($i = 1; $i <= 3; $i++) {
            $targetYear = $lastYear + $i;
            $targetX = $targetYear - $baseYear;
            $predictedValue = round($a + ($b * $targetX));
            $timelineData[$targetYear] = max(0, $predictedValue); // Tidak mungkin minus
        }

        $tahunSekarang = $lastYear;

        // Escape Rates (Laju Penurunan per tahun -> per detik)
        // Hitung selisih rata-rata tahunan (dari total penurunan / jumlah tahun)
        $totalPenurunan = $historicalData[$baseYear] - $historicalData[$lastYear];
        $rataTahunan = $totalPenurunan / max(1, ($lastYear - $baseYear));
        
        // Asumsi 1 tahun = 365 hari = 31.536.000 detik
        $escapeRateSec = $rataTahunan / 31536000;
        // Asumsi target escape rate agar 0 di 2030 (SDG 1)
        $targetRataTahunan = $historicalData[$lastYear] / max(1, (2030 - $lastYear));
        $targetEscapeRateSec = $targetRataTahunan / 31536000;
        
        $escapedToday = round($rataTahunan / 365);
        $fellToday = round($escapedToday * 0.15); // Asumsi fluktuasi 15% jatuh miskin lagi

        $metrics = [
            'currentRate' => round($escapeRateSec, 4),
            'targetRate' => round($targetEscapeRateSec, 4),
            'escapedToday' => max(0, $escapedToday),
            'fellToday' => max(0, $fellToday)
        ];

        return view('landing', compact('timelineData', 'tahunSekarang', 'metrics', 'kecamatans', 'selectedKecamatan'));
    }
}
