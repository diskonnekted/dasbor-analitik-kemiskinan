<?php

namespace App\Http\Controllers;

use App\Models\KesejahteraanKecamatan;
use App\Models\BantuanSosial;
use App\Models\IndikatorKecamatan;
use App\Models\KemiskinanData;

class RekomendasiController extends Controller
{
    public function index()
    {
        $data = $this->bangunData();
        if (isset($data['error'])) {
            return $this->responsiveView('rekomendasi', ['error' => $data['error']]);
        }

        return $this->responsiveView('rekomendasi', $data);
    }

    /**
     * Halaman detail cetak per kecamatan (kop JDN Jaga Data Nusantara).
     */
    public function cetak(string $kecamatan)
    {
        $data = $this->bangunData();
        if (isset($data['error'])) {
            abort(404, $data['error']);
        }

        $kec = strtoupper(trim($kecamatan));
        $row = collect($data['rows'])->firstWhere('kecamatan', $kec);
        if (!$row) {
            abort(404, 'Kecamatan tidak ditemukan.');
        }

        // Arahan intervensi khusus kecamatan ini (pakai faktor kunci yang sama).
        $rek = collect($data['rekomendasi'])->firstWhere('kecamatan', $kec);
        if (!$rek) {
            $rek = $this->susunRekomendasi([$row], $data['faktor'])[0] ?? null;
        }

        // Nilai indikator spesifik kecamatan ini (tahun terbaru per indikator).
        $indikatorKecamatan = [];
        foreach (IndikatorKecamatan::select('nama_indikator', 'kategori')->distinct()->get() as $ind) {
            $nama = $ind->nama_indikator;
            $tahunInd = IndikatorKecamatan::where('nama_indikator', $nama)->max('tahun');
            $baris = IndikatorKecamatan::where('nama_indikator', $nama)
                ->where('tahun', $tahunInd)
                ->get()
                ->first(fn ($i) => strtoupper(trim($i->kecamatan)) === $kec);

            if ($baris) {
                $f = collect($data['faktor'])->firstWhere('indikator', $nama);
                $indikatorKecamatan[] = [
                    'indikator' => $nama,
                    'kategori' => $ind->kategori,
                    'nilai' => (float) $baris->nilai,
                    'tahun' => $tahunInd,
                    'korelasi' => $f['korelasi'] ?? null,
                    'arah' => $f['arah'] ?? null,
                    'kekuatan' => $f['kekuatan'] ?? null,
                ];
            }
        }

        return view('rekomendasi_cetak', [
            'row' => $row,
            'rek' => $rek,
            'faktor' => $data['faktor'],
            'indikatorKecamatan' => $indikatorKecamatan,
            'ringkasan' => $data['ringkasan'],
            'maxTahun' => $data['maxTahun'],
            'dicetak' => now(),
        ]);
    }

    /**
     * Susun seluruh data analitik rekomendasi (dipakai index & cetak).
     */
    private function bangunData(): array
    {
        $maxTahun = KesejahteraanKecamatan::max('tahun');

        $kesejahteraan = KesejahteraanKecamatan::where('tahun', $maxTahun)->get();
        if ($kesejahteraan->isEmpty()) {
            return ['error' => 'Data kesejahteraan belum tersedia.'];
        }

        $bansos = BantuanSosial::where('tahun', $maxTahun)->get()->keyBy(function ($b) {
            return strtoupper(trim($b->kecamatan));
        });

        // --- 1. Susun data per kecamatan (sangat miskin + cakupan bansos) ---
        $rows = [];
        foreach ($kesejahteraan as $k) {
            $kec = strtoupper(trim($k->kecamatan));
            if (isset($rows[$kec])) {
                continue;
            }
            $sangatMiskin = (int) $k->desil_1;
            $penerima = isset($bansos[$kec]) ? (int) $bansos[$kec]->jumlah_penerima : 0;

            $rows[$kec] = [
                'kecamatan' => $kec,
                'sangat_miskin' => $sangatMiskin,
                'penerima_bansos' => $penerima,
                // Rasio cakupan: penerima (KK) dibanding jiwa sangat miskin. Makin kecil = makin kurang tersentuh.
                'rasio_cakupan' => $sangatMiskin > 0 ? round($penerima / $sangatMiskin, 3) : 0,
            ];
        }
        $rows = array_values($rows);

        // --- 2. Skoring prioritas: gabungan beban kemiskinan (70%) + kesenjangan cakupan (30%) ---
        $maxMiskin = max(array_map(fn ($r) => $r['sangat_miskin'], $rows)) ?: 1;
        $maxRasio = max(array_map(fn ($r) => $r['rasio_cakupan'], $rows)) ?: 1;

        foreach ($rows as &$r) {
            $skorBeban = $r['sangat_miskin'] / $maxMiskin;              // 0..1 (makin miskin makin tinggi)
            $skorKesenjangan = 1 - ($r['rasio_cakupan'] / $maxRasio);   // 0..1 (cakupan rendah makin tinggi)
            $r['skor'] = round(($skorBeban * 0.7 + $skorKesenjangan * 0.3) * 100, 1);
        }
        unset($r);

        usort($rows, fn ($a, $b) => $b['skor'] <=> $a['skor']);

        // Bagi tiga tingkat prioritas berdasarkan sepertiga peringkat
        $n = count($rows);
        $batasTinggi = (int) ceil($n / 3);
        $batasMenengah = (int) ceil(2 * $n / 3);
        foreach ($rows as $i => &$r) {
            if ($i < $batasTinggi) {
                $r['prioritas'] = 'Tinggi';
            } elseif ($i < $batasMenengah) {
                $r['prioritas'] = 'Menengah';
            } else {
                $r['prioritas'] = 'Rendah';
            }
        }
        unset($r);

        // --- 3. Faktor pendorong: korelasi Pearson tiap indikator vs sangat miskin ---
        $faktor = $this->hitungFaktorPendorong($kesejahteraan);

        // --- 4. Rekomendasi otomatis untuk kecamatan prioritas tinggi ---
        $prioritasTinggi = array_values(array_filter($rows, fn ($r) => $r['prioritas'] === 'Tinggi'));
        $rekomendasi = $this->susunRekomendasi($prioritasTinggi, $faktor);

        // --- 5. Ringkasan tingkat kabupaten ---
        $totalSangatMiskin = array_sum(array_map(fn ($r) => $r['sangat_miskin'], $rows));
        $totalPenerima = array_sum(array_map(fn ($r) => $r['penerima_bansos'], $rows));
        $kabupaten = KemiskinanData::where('tahun', $maxTahun)->first() ?? KemiskinanData::orderBy('tahun', 'desc')->first();

        $ringkasan = [
            'tahun' => $maxTahun,
            'total_sangat_miskin' => $totalSangatMiskin,
            'total_penerima' => $totalPenerima,
            'jml_prioritas_tinggi' => count($prioritasTinggi),
            'jml_kecamatan' => $n,
            'persentase_kab' => $kabupaten ? $kabupaten->persentase : null,
        ];

        return compact('ringkasan', 'rows', 'faktor', 'rekomendasi', 'maxTahun');
    }

    /**
     * Korelasi Pearson tiap indikator terhadap jumlah penduduk sangat miskin (desil_1).
     */
    private function hitungFaktorPendorong($kesejahteraan): array
    {
        $miskinByKec = [];
        foreach ($kesejahteraan as $k) {
            $miskinByKec[strtoupper(trim($k->kecamatan))] = (int) $k->desil_1;
        }

        $indikatorList = IndikatorKecamatan::select('nama_indikator', 'kategori')->distinct()->get();
        $faktor = [];

        foreach ($indikatorList as $ind) {
            $nama = $ind->nama_indikator;
            $tahunInd = IndikatorKecamatan::where('nama_indikator', $nama)->max('tahun');
            $dataInd = IndikatorKecamatan::where('nama_indikator', $nama)
                ->where('tahun', $tahunInd)
                ->get()->keyBy(fn ($i) => strtoupper(trim($i->kecamatan)));

            $x = [];
            $y = [];
            foreach ($miskinByKec as $kec => $miskin) {
                if (isset($dataInd[$kec])) {
                    $x[] = (float) $dataInd[$kec]->nilai;
                    $y[] = $miskin;
                }
            }

            if (count($x) < 3) {
                continue;
            }

            $r = $this->pearson($x, $y);
            $abs = abs($r);
            $kekuatan = 'Sangat Lemah';
            if ($abs > 0.8) $kekuatan = 'Sangat Kuat';
            elseif ($abs > 0.6) $kekuatan = 'Kuat';
            elseif ($abs > 0.4) $kekuatan = 'Sedang';
            elseif ($abs > 0.2) $kekuatan = 'Lemah';

            $faktor[] = [
                'indikator' => $nama,
                'kategori' => $ind->kategori,
                'korelasi' => round($r, 3),
                'abs' => round($abs, 3),
                'arah' => $r >= 0 ? 'Positif' : 'Negatif',
                'kekuatan' => $kekuatan,
            ];
        }

        usort($faktor, fn ($a, $b) => $b['abs'] <=> $a['abs']);
        return $faktor;
    }

    /**
     * Susun narasi rekomendasi otomatis berbasis faktor korelasi terkuat.
     */
    private function susunRekomendasi(array $prioritasTinggi, array $faktor): array
    {
        // Ambil hingga 3 faktor terkuat yang bermakna (>0.4) sebagai dasar arahan.
        $faktorKunci = array_values(array_filter($faktor, fn ($f) => $f['abs'] > 0.4));
        $faktorKunci = array_slice($faktorKunci, 0, 3);

        $peta = [
            'Penerima Bantuan Sosial' => [
                'dinas' => 'Dinas Sosial',
                'aksi' => 'perluasan cakupan bantuan sosial pangan dan validasi ulang DTKS',
                'narasi' => 'Jumlah penerima bantuan sosial bergerak sangat erat dengan jumlah penduduk sangat miskin — makin banyak warga miskin, makin besar pula kebutuhan bantuan. Ini menandakan bantuan sosial memang menyasar wilayah yang tepat, namun tekanan kemiskinan di kecamatan ini masih tinggi sehingga cakupannya perlu diperluas. Langkah konkretnya: memutakhirkan Data Terpadu Kesejahteraan Sosial (DTKS) agar penerima benar-benar sesuai kondisi terkini, menutup kebocoran (penerima yang sudah mampu) sekaligus menjangkau keluarga miskin yang belum terdaftar, lalu memastikan penyaluran bantuan pangan tepat sasaran, tepat jumlah, dan tepat waktu.',
            ],
            'Penyandang Masalah Sosial' => [
                'dinas' => 'Dinas Sosial',
                'aksi' => 'pendampingan PMKS dan program rehabilitasi sosial',
                'narasi' => 'Tingginya jumlah Penyandang Masalah Kesejahteraan Sosial (PMKS) berjalan seiring dengan tingkat kemiskinan ekstrem. Kelompok ini — seperti lansia terlantar, anak jalanan, dan warga rentan lainnya — umumnya sulit keluar dari kemiskinan tanpa pendampingan khusus. Karena itu diperlukan pendataan ulang PMKS per desa, pendampingan sosial yang berkelanjutan, dan program rehabilitasi sosial agar mereka bisa kembali produktif dan tidak menjadi beban jangka panjang.',
            ],
            'Penyandang Disabilitas Usia Produktif' => [
                'dinas' => 'Dinas Sosial & Disnaker',
                'aksi' => 'pelatihan vokasi inklusif dan bantuan alat bantu produktif',
                'narasi' => 'Banyaknya penyandang disabilitas usia produktif berkaitan kuat dengan kemiskinan karena keterbatasan akses mereka ke lapangan kerja membuat potensi ekonominya belum tergarap. Padahal pada usia produktif mereka bisa menjadi tenaga kerja bila diberi dukungan yang tepat. Intervensinya: pelatihan vokasi yang inklusif dan sesuai jenis disabilitas, pemberian alat bantu produktif (misalnya alat kerja atau mobilitas), serta kemitraan dengan dunia usaha agar lulusan pelatihan terserap kerja. Ini mengubah kelompok rentan menjadi penggerak ekonomi keluarga.',
            ],
            'Akseptor KB Aktif' => [
                'dinas' => 'Dispermades & Dinkes',
                'aksi' => 'penguatan program KB untuk menekan beban tanggungan keluarga',
                'narasi' => 'Korelasi yang kuat antara jumlah akseptor KB aktif dan kemiskinan mengindikasikan besarnya beban tanggungan keluarga di wilayah miskin. Keluarga dengan banyak anggota namun pendapatan terbatas cenderung sulit lepas dari kemiskinan karena pengeluaran dasar (pangan, pendidikan, kesehatan) membengkak. Penguatan program Keluarga Berencana — melalui edukasi, kemudahan akses alat kontrasepsi, dan penyuluhan kesehatan reproduksi — membantu keluarga merencanakan jumlah anak sesuai kemampuan ekonomi, sehingga beban tanggungan lebih terkendali dan investasi per anak (gizi & pendidikan) bisa lebih baik.',
            ],
            'Alokasi Dana Desa' => [
                'dinas' => 'Dispermades',
                'aksi' => 'realokasi Dana Desa ke program padat karya dan infrastruktur dasar',
                'narasi' => 'Hubungan antara Alokasi Dana Desa dan kemiskinan menunjukkan bahwa besaran dana yang mengalir ke desa belum tentu berdampak langsung menurunkan kemiskinan bila belum diarahkan ke program yang menyentuh warga miskin. Karena itu perlu realokasi Dana Desa ke kegiatan padat karya tunai (menyerap tenaga kerja lokal), pembangunan infrastruktur dasar (jalan usaha tani, air bersih, sanitasi), dan pemberdayaan ekonomi produktif — bukan sekadar belanja rutin. Dengan begitu Dana Desa menjadi alat pengungkit ekonomi warga miskin, bukan sekadar transfer anggaran.',
            ],
            'Jumlah Tenaga Kesehatan' => [
                'dinas' => 'Dinas Kesehatan',
                'aksi' => 'penambahan tenaga kesehatan dan penguatan Posyandu',
                'narasi' => 'Keterkaitan jumlah tenaga kesehatan dengan kemiskinan mencerminkan pentingnya akses layanan kesehatan bagi warga rentan. Wilayah miskin dengan tenaga kesehatan terbatas berisiko pada gizi buruk, stunting, dan penyakit yang menggerus produktivitas serta menambah pengeluaran keluarga. Penambahan tenaga kesehatan dan penguatan Posyandu di kecamatan ini akan meningkatkan deteksi dini, layanan ibu dan anak, serta promosi kesehatan — memutus rantai antara sakit dan miskin.',
            ],
            'Kejadian Bencana' => [
                'dinas' => 'BPBD',
                'aksi' => 'mitigasi bencana dan penguatan ketahanan wilayah rawan',
                'narasi' => 'Kaitan antara kejadian bencana dan kemiskinan menegaskan bahwa warga miskin paling rentan terdampak dan paling lambat pulih ketika bencana terjadi — aset dan tabungan mereka terbatas. Wilayah rawan bencana perlu prioritas mitigasi: pemetaan risiko, sistem peringatan dini, penguatan rumah dan infrastruktur, serta kesiapsiagaan masyarakat. Mengurangi kerugian akibat bencana berarti melindungi warga miskin agar tidak jatuh makin dalam ke kemiskinan.',
            ],
        ];

        $petaDefault = [
            'dinas' => 'Bappeda/Baperlitbang',
            'aksi' => 'intervensi terpadu berbasis data',
            'narasi' => 'Indikator ini terhubung dengan tingkat kemiskinan namun belum memiliki pemetaan intervensi baku. Disarankan koordinasi lintas OPD di bawah Bappeda untuk merancang program terpadu berbasis data dan memantau dampaknya secara berkala.',
        ];

        $rekomendasi = [];
        foreach ($prioritasTinggi as $r) {
            $aksiList = [];
            foreach ($faktorKunci as $f) {
                $info = $peta[$f['indikator']] ?? $petaDefault;
                $arahTeks = $f['arah'] === 'Positif'
                    ? 'searah (nilainya tinggi di wilayah dengan kemiskinan tinggi)'
                    : 'berlawanan arah (nilainya rendah di wilayah dengan kemiskinan tinggi)';
                $aksiList[] = [
                    'dinas' => $info['dinas'],
                    'aksi' => $info['aksi'],
                    'narasi' => $info['narasi'],
                    'dasar' => $f['indikator'] . ' (korelasi ' . $f['korelasi'] . ', ' . $f['kekuatan'] . ')',
                    'penjelasan_korelasi' => 'Indikator "' . $f['indikator'] . '" memiliki korelasi ' . number_format($f['korelasi'], 3, ',', '.') . ' (' . strtolower($f['kekuatan']) . ', ' . $arahTeks . ') terhadap jumlah penduduk sangat miskin antar-kecamatan.',
                ];
            }

            $rekomendasi[] = [
                'kecamatan' => $r['kecamatan'],
                'sangat_miskin' => $r['sangat_miskin'],
                'skor' => $r['skor'],
                'catatan_cakupan' => $r['rasio_cakupan'] < 0.5
                    ? 'Cakupan bantuan tergolong rendah — perlu diprioritaskan.'
                    : 'Cakupan bantuan cukup, fokus pada kualitas intervensi.',
                'aksi' => $aksiList,
            ];
        }

        return $rekomendasi;
    }

    private function pearson(array $x, array $y): float
    {
        $n = count($x);
        if ($n === 0 || $n !== count($y)) {
            return 0.0;
        }
        $mx = array_sum($x) / $n;
        $my = array_sum($y) / $n;
        $cov = 0;
        $vx = 0;
        $vy = 0;
        for ($i = 0; $i < $n; $i++) {
            $dx = $x[$i] - $mx;
            $dy = $y[$i] - $my;
            $cov += $dx * $dy;
            $vx += $dx * $dx;
            $vy += $dy * $dy;
        }
        if ($vx * $vy == 0) {
            return 0.0;
        }
        return $cov / sqrt($vx * $vy);
    }
}
