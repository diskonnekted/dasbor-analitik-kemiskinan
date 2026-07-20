<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekomendasi Intervensi — {{ ucwords(strtolower($row['kecamatan'])) }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #171717;
            background: #e5e5e0;
            line-height: 1.5;
            font-size: 12px;
        }
        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 16px auto;
            padding: 18mm 16mm;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
        }
        .toolbar {
            width: 210mm;
            margin: 16px auto 0;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }
        .btn {
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 18px;
            border: 2px solid #171717;
            background: #171717;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn.secondary { background: #fff; color: #171717; }

        /* Kop surat */
        .kop {
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 3px solid #171717;
            padding-bottom: 14px;
        }
        .kop-logo {
            width: 64px; height: 64px;
            flex-shrink: 0;
            border: 2px solid #171717;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 22px;
            background: #171717; color: #fff;
            letter-spacing: -1px;
        }
        .kop-text h1 { font-size: 18px; text-transform: uppercase; letter-spacing: 0.02em; }
        .kop-text .sub { font-size: 11px; font-weight: 700; color: #525252; text-transform: uppercase; letter-spacing: 0.12em; }
        .kop-text .tag { font-size: 10px; color: #737373; margin-top: 2px; }

        .doc-title {
            text-align: center;
            margin: 18px 0 4px;
        }
        .doc-title h2 { font-size: 15px; text-transform: uppercase; letter-spacing: 0.04em; }
        .doc-title .meta { font-size: 10px; color: #737373; margin-top: 3px; }

        .section { margin-top: 18px; }
        .section-title {
            font-size: 12px; font-weight: 800; text-transform: uppercase;
            letter-spacing: 0.08em; border-bottom: 2px solid #171717;
            padding-bottom: 4px; margin-bottom: 10px;
            display: flex; align-items: center; gap: 6px;
        }
        .grid-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .stat {
            border: 2px solid #171717; padding: 10px 12px;
        }
        .stat .label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.1em; color: #525252; font-weight: 700; }
        .stat .value { font-size: 20px; font-weight: 800; margin-top: 2px; }
        .stat .unit { font-size: 10px; font-weight: 700; color: #737373; }
        .stat.dark { background: #171717; color: #fff; }
        .stat.dark .label { color: #a3a3a3; }
        .stat.dark .unit { color: #a3a3a3; }

        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        th, td { text-align: left; padding: 6px 8px; border-bottom: 1px solid #d4d4d4; font-size: 11px; }
        th { text-transform: uppercase; font-size: 9px; letter-spacing: 0.08em; border-bottom: 2px solid #171717; }
        td.num, th.num { text-align: right; }
        .badge {
            display: inline-block; font-size: 9px; font-weight: 800; text-transform: uppercase;
            letter-spacing: 0.05em; padding: 2px 7px; border: 1.5px solid #171717;
        }
        .badge.pos { background: #171717; color: #fff; }
        .badge.neg { background: #e5e5d8; }

        .aksi { border: 2px solid #171717; padding: 12px 14px; margin-bottom: 10px; }
        .aksi .dinas { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.03em; }
        .aksi .isi { font-size: 12px; margin-top: 2px; }
        .aksi .dasar { font-size: 10px; color: #737373; font-style: italic; margin-top: 4px; }

        .note {
            font-size: 10px; color: #525252; line-height: 1.6;
            border-top: 2px solid #171717; padding-top: 10px; margin-top: 18px;
        }
        .ttd { margin-top: 28px; display: flex; justify-content: flex-end; }
        .ttd .box { text-align: center; font-size: 11px; }
        .ttd .space { height: 56px; }
        .ttd .name { font-weight: 800; text-transform: uppercase; border-top: 1px solid #171717; padding-top: 4px; }

        .footer-doc {
            margin-top: 20px; border-top: 1px solid #d4d4d4; padding-top: 8px;
            font-size: 9px; color: #a3a3a3; text-align: center; letter-spacing: 0.05em;
        }

        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { width: auto; min-height: auto; margin: 0; padding: 0; box-shadow: none; }
            @page { size: A4; margin: 14mm; }
        }
    </style>
</head>
<body>
    @php
        $namaKec = ucwords(strtolower($row['kecamatan']));
        $prioBadge = $row['prioritas'] ?? '-';
        $rasio = $row['rasio_cakupan'] ?? 0;
    @endphp

    <div class="toolbar">
        <a href="{{ route('rekomendasi') }}" class="btn secondary">&larr; Kembali</a>
        <button type="button" class="btn" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>

    <div class="sheet">
        <!-- KOP JDN -->
        <div class="kop">
            <div class="kop-logo">JDN</div>
            <div class="kop-text">
                <h1>Jaga Data Nusantara</h1>
                <div class="sub">Komunitas Data untuk Kebijakan Publik</div>
                <div class="tag">Sistem Pendukung Keputusan Penanggulangan Kemiskinan &mdash; Kabupaten Banjarnegara</div>
            </div>
        </div>

        <div class="doc-title">
            <h2>Lembar Rekomendasi Intervensi Kemiskinan</h2>
            <div class="meta">Kecamatan {{ $namaKec }} &middot; Tahun Data {{ $maxTahun }} &middot; Dicetak {{ $dicetak->format('d/m/Y H:i') }} WIB</div>
        </div>

        <!-- Ringkasan Angka -->
        <div class="section">
            <div class="section-title">Profil Kemiskinan Kecamatan</div>
            <div class="grid-stats">
                <div class="stat dark">
                    <div class="label">Penduduk Sangat Miskin (Desil 1)</div>
                    <div class="value">{{ number_format($row['sangat_miskin'], 0, ',', '.') }} <span class="unit">Jiwa</span></div>
                </div>
                <div class="stat">
                    <div class="label">Penerima Bantuan Sosial</div>
                    <div class="value">{{ number_format($row['penerima_bansos'], 0, ',', '.') }} <span class="unit">KK</span></div>
                </div>
                <div class="stat">
                    <div class="label">Skor Prioritas</div>
                    <div class="value">{{ $row['skor'] }} <span class="unit">/ 100</span></div>
                </div>
            </div>
            <table>
                <tbody>
                    <tr>
                        <th>Tingkat Prioritas</th>
                        <td><span class="badge pos">{{ $prioBadge }}</span></td>
                        <th>Rasio Cakupan Bantuan</th>
                        <td>{{ number_format($rasio, 3, ',', '.') }} (penerima KK / jiwa sangat miskin)</td>
                    </tr>
                </tbody>
            </table>
            @if(!empty($rek['catatan_cakupan']))
            <p style="margin-top:8px; font-size:11px; font-weight:700; border:2px solid #171717; padding:8px 10px;">Catatan: {{ $rek['catatan_cakupan'] }}</p>
            @endif
        </div>

        <!-- Arahan Intervensi -->
        <div class="section">
            <div class="section-title">Arahan Intervensi Lintas Dinas</div>
            @forelse($rek['aksi'] ?? [] as $i => $a)
            <div class="aksi">
                <div class="dinas">{{ $i + 1 }}. {{ $a['dinas'] }}</div>
                <div class="isi">{{ ucfirst($a['aksi']) }}.</div>
                @if(!empty($a['narasi']))
                <p style="font-size:11px; line-height:1.6; margin-top:8px; text-align:justify;">{{ $a['narasi'] }}</p>
                @endif
                @if(!empty($a['penjelasan_korelasi']))
                <p style="font-size:10px; line-height:1.55; margin-top:6px; background:#f4f4f0; border-left:3px solid #171717; padding:6px 8px;">{{ $a['penjelasan_korelasi'] }}</p>
                @endif
                <div class="dasar">Dasar analisa: {{ $a['dasar'] }}</div>
            </div>
            @empty
            <p style="font-size:11px; color:#737373;">Belum ada arahan intervensi otomatis untuk kecamatan ini.</p>
            @endforelse
        </div>

        <!-- Indikator per kecamatan -->
        <div class="section">
            <div class="section-title">Indikator Pendukung (Data Kecamatan Ini)</div>
            <table>
                <thead>
                    <tr>
                        <th>Indikator</th>
                        <th>Kategori</th>
                        <th class="num">Nilai</th>
                        <th class="num">Tahun</th>
                        <th class="num">Korelasi (r)</th>
                        <th>Hubungan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($indikatorKecamatan as $ik)
                    <tr>
                        <td>{{ $ik['indikator'] }}</td>
                        <td>{{ $ik['kategori'] }}</td>
                        <td class="num">{{ number_format($ik['nilai'], 0, ',', '.') }}</td>
                        <td class="num">{{ $ik['tahun'] }}</td>
                        <td class="num">{{ $ik['korelasi'] !== null ? number_format($ik['korelasi'], 2, ',', '.') : '–' }}</td>
                        <td>
                            @if($ik['korelasi'] !== null)
                            <span class="badge {{ $ik['arah'] === 'Positif' ? 'pos' : 'neg' }}">{{ $ik['arah'] }} · {{ $ik['kekuatan'] }}</span>
                            @else
                            –
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="color:#737373;">Data indikator kecamatan belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <p style="font-size:10px; color:#737373; margin-top:6px;">Kolom korelasi (r) adalah keterkaitan indikator terhadap jumlah penduduk sangat miskin tingkat kabupaten. Positif = searah (risiko), Negatif = berlawanan (pelindung). Nilai indikator di atas adalah angka spesifik Kecamatan {{ $namaKec }}.</p>
        </div>

        <!-- Konteks Kabupaten -->
        <div class="section">
            <div class="section-title">Konteks Kabupaten ({{ $ringkasan['tahun'] }})</div>
            <table>
                <tbody>
                    <tr>
                        <th>Total Penduduk Sangat Miskin</th>
                        <td class="num">{{ number_format($ringkasan['total_sangat_miskin'], 0, ',', '.') }} Jiwa</td>
                        <th>Kecamatan Prioritas Tinggi</th>
                        <td class="num">{{ $ringkasan['jml_prioritas_tinggi'] }} dari {{ $ringkasan['jml_kecamatan'] }}</td>
                    </tr>
                    <tr>
                        <th>Total Penerima Bantuan</th>
                        <td class="num">{{ number_format($ringkasan['total_penerima'], 0, ',', '.') }} KK</td>
                        <th>Persentase Kemiskinan Kab.</th>
                        <td class="num">{{ $ringkasan['persentase_kab'] !== null ? number_format($ringkasan['persentase_kab'], 2, ',', '.') . '%' : '–' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="note">
            Dokumen ini dihasilkan otomatis oleh sistem analitik <strong>Jaga Data Nusantara (JDN)</strong> berdasarkan data desil kesejahteraan, cakupan bantuan sosial, dan korelasi indikator antar-OPD. Skor prioritas = 70% beban kemiskinan ekstrem (desil 1) + 30% kesenjangan cakupan bantuan. Rekomendasi bersifat pendukung keputusan; verifikasi lapangan tetap diperlukan sebelum penetapan kebijakan.
        </div>

        <div class="ttd">
            <div class="box">
                <div>Banjarnegara, {{ $dicetak->translatedFormat('d F Y') }}</div>
                <div>Disusun oleh,</div>
                <div class="space"></div>
                <div class="name">Tim Analis Data JDN</div>
            </div>
        </div>

        <div class="footer-doc">
            Jaga Data Nusantara &mdash; Dokumen dibuat pada {{ $dicetak->format('d/m/Y H:i') }} WIB &middot; Sumber: BPS, DTKS, & data OPD Kabupaten Banjarnegara
        </div>
    </div>

    <script>
        // Auto-focus untuk print bila diakses dengan ?print=1
        if (new URLSearchParams(window.location.search).get('print') === '1') {
            window.addEventListener('load', () => window.print());
        }
    </script>
</body>
</html>
