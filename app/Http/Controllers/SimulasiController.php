<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KesejahteraanKecamatan;
use App\Models\BantuanSosial;

class SimulasiController extends Controller
{
    public function index()
    {
        // Ambil data terbaru per kecamatan (tahun yang lengkap di kesejahteraan DAN bansos).
        $maxTahun = (int) min(KesejahteraanKecamatan::max('tahun'), BantuanSosial::max('tahun'));
        $kesejahteraan = KesejahteraanKecamatan::where('tahun', $maxTahun)->get();
        $bansos = BantuanSosial::where('tahun', $maxTahun)->get()->keyBy('kecamatan');

        if ($kesejahteraan->isEmpty()) {
            return redirect()->back()->with('error', 'Data tidak tersedia untuk simulasi.');
        }

        // Elastisitas berbasis data (regresi panel first-difference Δdesil_1 ~ Δanggaran).
        $elastisitasInfo = $this->estimasiElastisitas();
        $elastisitas = $elastisitasInfo['elastisitas'];

        $kecamatanList = [];
        $dataKecamatan = [];

        foreach ($kesejahteraan as $k) {
            $kec = strtoupper($k->kecamatan);
            $pendudukMiskin = $k->desil_1; // Asumsi Desil 1 sebagai patokan utama sasaran
            
            // Dapatkan anggaran existing
            $anggaranBansosRibu = isset($bansos[$kec]) ? $bansos[$kec]->anggaran_ribu_rupiah : 0;
            $anggaranBansosJuta = $anggaranBansosRibu / 1000;

            $dataKecamatan[$kec] = [
                'nama' => $kec,
                'kemiskinan_awal' => $pendudukMiskin,
                'anggaran_awal_juta' => $anggaranBansosJuta,
                'elastisitas' => $elastisitas
            ];
            
            $kecamatanList[] = $kec;
        }

        sort($kecamatanList);

        return view('simulasi', compact('kecamatanList', 'dataKecamatan', 'maxTahun', 'elastisitasInfo'));
    }

    /**
     * Estimasi elastisitas kemiskinan terhadap anggaran bansos.
     *
     * Regresi first-difference panel per kecamatan:
     *   ΔY = desil_1(t+1) - desil_1(t)
     *   ΔX = (anggaran(t+1) - anggaran(t)) / 1000  (juta rupiah)
     *
     * Elastisitas = -β  (jiwa terangkat per Rp 1 juta).
     * Menggantikan asumsi 0.1 yang sebelumnya hardcoded.
     */
    private function estimasiElastisitas(): array
    {
        $kesejahteraan = KesejahteraanKecamatan::orderBy('tahun')->get()->groupBy('kecamatan');
        $bansos = BantuanSosial::orderBy('tahun')->get()->groupBy('kecamatan');

        $xs = []; // Δanggaran (juta)
        $ys = []; // Δdesil_1 (jiwa)

        foreach ($kesejahteraan as $kec => $rows) {
            $byDesil = $rows->keyBy('tahun');
            $byBansos = ($bansos[$kec] ?? collect())->keyBy('tahun');
            $tahuns = array_values(array_intersect($byDesil->keys()->all(), $byBansos->keys()->all()));
            sort($tahuns);

            for ($i = 0; $i < count($tahuns) - 1; $i++) {
                $t = $tahuns[$i];
                $t1 = $tahuns[$i + 1];
                if ($t1 - $t !== 1) {
                    continue; // hanya transisi tahun berurutan
                }
                $ys[] = $byDesil[$t1]->desil_1 - $byDesil[$t]->desil_1;
                $xs[] = ($byBansos[$t1]->anggaran_ribu_rupiah - $byBansos[$t]->anggaran_ribu_rupiah) / 1000;
            }
        }

        $n = count($xs);
        if ($n < 5) {
            return ['elastisitas' => 0.1, 'n' => $n, 'slope' => null, 'r2' => null, 'sumber' => 'fallback (data kurang)'];
        }

        $mx = array_sum($xs) / $n;
        $my = array_sum($ys) / $n;

        $num = 0.0;
        $den = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $num += ($xs[$i] - $mx) * ($ys[$i] - $my);
            $den += ($xs[$i] - $mx) ** 2;
        }
        $slope = $den == 0 ? 0.0 : $num / $den;
        $intercept = $my - $slope * $mx;

        $ssRes = 0.0;
        $ssTot = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $pred = $intercept + $slope * $xs[$i];
            $ssRes += ($ys[$i] - $pred) ** 2;
            $ssTot += ($ys[$i] - $my) ** 2;
        }
        $r2 = $ssTot == 0 ? 0 : 1 - $ssRes / $ssTot;

        $elastisitas = -$slope; // jiwa terangkat per Rp 1 juta
        if ($elastisitas <= 0) {
            // tanda slope tidak sesuai (indikasi endogenitas) -> baseline
            $elastisitas = 0.1;
            $sumber = 'fallback (tanda slope tidak sesuai — indikasi endogenitas)';
        } else {
            $elastisitas = min(max($elastisitas, 0.001), 0.5); // clamp (hanya cegah nilai degenerate)
            $sumber = 'empiris (panel first-difference)';
        }

        return [
            'elastisitas' => round($elastisitas, 4),
            'n' => $n,
            'slope' => round($slope, 4),
            'r2' => round($r2, 3),
            'sumber' => $sumber,
        ];
    }
}
