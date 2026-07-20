<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KemiskinanData;
use App\Models\KesejahteraanKecamatan;
use App\Models\BantuanSosial;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahunFilter = $request->query('tahun');
        
        $kemiskinanAll = KemiskinanData::orderBy('tahun', 'asc')->get();

        $chartLabels = $kemiskinanAll->pluck('tahun');
        $chartDataPenduduk = $kemiskinanAll->pluck('jumlah_penduduk_miskin');
        $chartDataKedalaman = $kemiskinanAll->pluck('indeks_kedalaman');
        $chartDataKeparahan = $kemiskinanAll->pluck('indeks_keparahan');

        // Tentukan data mana yang akan ditampilkan di kotak stat utama
        $latest = $tahunFilter ? $kemiskinanAll->where('tahun', $tahunFilter)->first() : $kemiskinanAll->last();
        if (!$latest) {
            $latest = $kemiskinanAll->last();
            $tahunFilter = $latest ? $latest->tahun : null;
        } else {
            $tahunFilter = $latest->tahun;
        }

        $totalMiskin = $latest ? $latest->jumlah_penduduk_miskin : 0;
        $rataKedalaman = $latest ? $latest->indeks_kedalaman : 0;
        $rataKeparahan = $latest ? $latest->indeks_keparahan : 0;

        // Data Kesejahteraan (Peta)
        $maxTahunKesejahteraan = $tahunFilter;
        $kesejahteraan = KesejahteraanKecamatan::where('tahun', $tahunFilter)->get();
        
        if ($kesejahteraan->isEmpty()) {
            $maxTahunKesejahteraan = KesejahteraanKecamatan::max('tahun');
            $kesejahteraan = KesejahteraanKecamatan::where('tahun', $maxTahunKesejahteraan)->get();
        }

        $kesejahteraanMap = [];
        foreach ($kesejahteraan as $k) {
            $kecName = strtoupper($k->kecamatan);
            if ($kecName === 'PURWOREJA KLAMPOK' || $kecName === 'PURWAREJA KLAMPOK') {
                $kecName = 'PURWAREJAKLAMPOK';
            }
            $kesejahteraanMap[$kecName] = $k;
        }
        
        // Data Bantuan Sosial
        $bansos = BantuanSosial::where('tahun', $tahunFilter)->get();
        if ($bansos->isEmpty()) {
            $maxTahunBansos = BantuanSosial::max('tahun');
            $bansos = BantuanSosial::where('tahun', $maxTahunBansos)->get();
        }

        $tahunBansos = $bansos->first() ? $bansos->first()->tahun : null;
        $totalPenerimaBansos = BantuanSosial::where('tahun', $tahunBansos)->sum('jumlah_penerima');
        $totalAnggaranBansos = BantuanSosial::where('tahun', $tahunBansos)->sum('anggaran_ribu_rupiah'); // to Rupiah
        
        $bansosMap = [];
        foreach ($bansos as $b) {
            $kecName = strtoupper($b->kecamatan);
            if ($kecName === 'PURWOREJA KLAMPOK' || $kecName === 'PURWAREJA KLAMPOK') {
                $kecName = 'PURWAREJAKLAMPOK';
            }
            $bansosMap[$kecName] = $b;
        }

        $availableYears = KemiskinanData::orderBy('tahun', 'desc')->pluck('tahun');

        return view('dasbor', compact(
            'totalMiskin', 'rataKedalaman', 'rataKeparahan',
            'chartLabels', 'chartDataPenduduk', 'chartDataKedalaman', 'chartDataKeparahan',
            'kesejahteraanMap', 'maxTahunKesejahteraan', 'tahunFilter', 'availableYears',
            'totalPenerimaBansos', 'totalAnggaranBansos', 'bansosMap'
        ));
    }

    public function exportCsv()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=laporan_kemiskinan.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $data = KemiskinanData::orderBy('tahun', 'desc')->get();
        $columns = ['Tahun', 'Jumlah Penduduk Miskin (Jiwa)', 'Persentase (%)', 'Indeks Kedalaman', 'Indeks Keparahan'];

        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->tahun,
                    $row->jumlah_penduduk_miskin,
                    $row->persentase,
                    $row->indeks_kedalaman,
                    $row->indeks_keparahan
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
