<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KesejahteraanKecamatan;
use App\Models\BantuanSosial;

class SimulasiController extends Controller
{
    public function index()
    {
        // Ambil data terbaru per kecamatan
        $maxTahun = KesejahteraanKecamatan::max('tahun');
        $kesejahteraan = KesejahteraanKecamatan::where('tahun', $maxTahun)->get();
        $bansos = BantuanSosial::where('tahun', $maxTahun)->get()->keyBy('kecamatan');

        if ($kesejahteraan->isEmpty()) {
            return redirect()->back()->with('error', 'Data tidak tersedia untuk simulasi.');
        }

        $kecamatanList = [];
        $dataKecamatan = [];

        foreach ($kesejahteraan as $k) {
            $kec = strtoupper($k->kecamatan);
            $pendudukMiskin = $k->desil_1; // Asumsi Desil 1 sebagai patokan utama sasaran
            
            // Dapatkan anggaran existing
            $anggaranBansosRibu = isset($bansos[$kec]) ? $bansos[$kec]->anggaran_ribu_rupiah : 0;
            $anggaranBansosJuta = $anggaranBansosRibu / 1000;

            // Kita asumsikan rasio elastisitas konstan untuk tujuan simulasi:
            // Setiap Rp 10 Juta injeksi bantuan langsung akan mengangkat 1 jiwa dari garis kemiskinan ekstrem.
            // (Elastisitas = 0.1 jiwa / 1 juta)
            $elastisitas = 0.1;

            $dataKecamatan[$kec] = [
                'nama' => $kec,
                'kemiskinan_awal' => $pendudukMiskin,
                'anggaran_awal_juta' => $anggaranBansosJuta,
                'elastisitas' => $elastisitas
            ];
            
            $kecamatanList[] = $kec;
        }

        sort($kecamatanList);

        return view('simulasi', compact('kecamatanList', 'dataKecamatan', 'maxTahun'));
    }
}
