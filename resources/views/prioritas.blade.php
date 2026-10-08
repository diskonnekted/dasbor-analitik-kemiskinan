@extends('layouts.app')

@section('title', 'Prioritas Kecamatan')

@section('content')
<div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-14">

    <!-- ============ Header ============ -->
    <div class="mb-12">
        <div class="section-label mb-4">
            <span aria-hidden="true">[</span>Prioritas<span aria-hidden="true">]</span>
        </div>
        <h1 class="display-lg text-ink">Prioritas &amp; Ketepatan Sasaran</h1>
        <p class="body-md text-body mt-3 max-w-2xl">
            Memadukan kemiskinan, kerentanan sosial (PMKS), akses air, tenaga kesehatan, dan bencana menjadi satu prioritas intervensi per kecamatan — tahun {{ $tahun }}.@include('partials.p3ke-info')
        </p>
    </div>

    <!-- ============ Temuan kunci ============ -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="feature-card flex flex-col gap-2">
            <span class="caption-uppercase text-muted">Autokorelasi Spasial</span>
            <span class="display-sm text-ink tabular">I = {{ number_format($moransI['I'], 3, ',', '.') }}</span>
            <span class="body-sm text-body">{{ $moransI['interpretation'] }}</span>
            <span class="body-xs text-muted">p-value (permutasi 999) = {{ number_format($moransI['p_value'], 3, ',', '.') }}</span>
        </div>
        <div class="feature-card flex flex-col gap-2">
            <span class="caption-uppercase text-muted">Hotspot Kemiskinan (LISA)</span>
            <span class="display-sm text-ink tabular">{{ count(array_filter($lisa, fn($l) => str_starts_with($l['type'], 'HH'))) }} kecamatan</span>
            <span class="body-sm text-body">Klaster tinggi-tinggi yang signifikan spasial.</span>
            <span class="body-xs text-muted">HH = kecamatan miskin dikelilingi kecamatan miskin.</span>
        </div>
        <div class="feature-card flex flex-col gap-2">
            <span class="caption-uppercase text-muted">Prioritas Utama</span>
            <span class="display-sm text-ink tabular">{{ count(array_filter($prioritas, fn($p) => $p['kuadran'] === 'Prioritas Utama')) }} kecamatan</span>
            <span class="body-sm text-body">Kemiskinan tinggi <em>dan</em> tren memburuk.</span>
            <span class="body-xs text-muted">Fokus alokasi anggaran &amp; program.</span>
        </div>
    </div>

    <!-- ============ 1. Ketepatan Sasaran ============ -->
    <section id="targeting" class="mb-14">
        <div class="flex items-end justify-between mb-6">
            <div>
                <h2 class="display-sm text-ink">Ketepatan Sasaran Bansos</h2>
                <p class="body-sm text-muted mt-1">Indeks cakupan = pangsa penerima ÷ pangsa penduduk kesejahteraan terendah.</p>
            </div>
            <span class="code text-muted">tahun {{ $tahun }}</span>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-7 feature-card flex flex-col">
                <div class="relative w-full flex-1 min-h-[380px] bg-canvas-soft rounded-[8px] p-2">
                    <canvas id="cakupanChart"></canvas>
                </div>
            </div>
            <div class="xl:col-span-5 feature-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-hairline">
                                <th class="py-3 pr-3 caption-uppercase text-muted font-normal">Kecamatan</th>
                                <th class="py-3 pr-3 caption-uppercase text-muted font-normal text-right">Indeks</th>
                                <th class="py-3 caption-uppercase text-muted font-normal text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($targeting as $row)
                            @php
                                $badge = match($row['status']) {
                                    'Kurang Terjangkau' => 'bg-warning text-ink',
                                    'Proporsional' => 'bg-success text-white',
                                    default => 'bg-error text-white',
                                };
                                $dot = match($row['status']) {
                                    'Kurang Terjangkau' => 'bg-warning',
                                    'Proporsional' => 'bg-success',
                                    default => 'bg-error',
                                };
                            @endphp
                            <tr class="border-b border-hairline last:border-0">
                                <td class="py-2.5 pr-3 body-sm text-ink">{{ $row['kecamatan'] }}</td>
                                <td class="py-2.5 pr-3 body-sm tabular text-right text-ink">{{ number_format($row['indeks_cakupan'], 2, ',', '.') }}</td>
                                <td class="py-2.5 text-right">
                                    <span class="badge-pill {{ $badge }}">{{ $row['status'] }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pt-4 mt-2 flex flex-wrap gap-4 body-xs text-muted">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-warning"></span> &lt; 0,85 — kurang terjangkau (eksklusi)</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-success"></span> 0,85–1,15 — proporsional</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-error"></span> &gt; 1,15 — lebih terjangkau (leakage)</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ 2. IKK ============ -->
    <section id="ikk" class="mb-14">
        <div class="flex items-end justify-between mb-6">
            <div>
                <h2 class="display-sm text-ink">Indeks Komposit Kemiskinan Kecamatan (IKK)</h2>
                <p class="body-sm text-muted mt-1">Skor 0–100 (semakin tinggi = semakin butuh intervensi). Bobot: kemiskinan 40%, PMKS 20%, bencana 20%, air 10%, nakes 10%.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-7 feature-card flex flex-col">
                <div class="relative w-full flex-1 min-h-[400px] bg-canvas-soft rounded-[8px] p-2">
                    <canvas id="ikkChart"></canvas>
                </div>
            </div>
            <div class="xl:col-span-5 feature-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-hairline">
                                <th class="py-3 pr-3 caption-uppercase text-muted font-normal">#</th>
                                <th class="py-3 pr-3 caption-uppercase text-muted font-normal">Kecamatan</th>
                                <th class="py-3 caption-uppercase text-muted font-normal text-right">Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ikk as $i => $row)
                            <tr class="border-b border-hairline last:border-0">
                                <td class="py-2.5 pr-3 code text-xs {{ $i < 5 ? 'text-error' : 'text-muted-soft' }}">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-2.5 pr-3 body-sm text-ink">{{ $row['kecamatan'] }}</td>
                                <td class="py-2.5 body-sm tabular text-right {{ $row['skor'] >= 60 ? 'text-error font-medium' : 'text-ink' }}">{{ number_format($row['skor'], 1, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ 3. Matriks Prioritas 2x2 ============ -->
    <section id="prioritas-2x2" class="mb-14">
        <div class="flex items-end justify-between mb-6">
            <div>
                <h2 class="display-sm text-ink">Matriks Prioritas 2×2</h2>
                <p class="body-sm text-muted mt-1">Tingkat kemiskinan (% kesejahteraan terendah) vs tren Desil 1 ({{ $tahun - 2 }}→{{ $tahun }}).</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-7 feature-card flex flex-col">
                <div class="relative w-full flex-1 min-h-[420px] bg-canvas-soft rounded-[8px] p-2">
                    <canvas id="prioritasChart"></canvas>
                </div>
            </div>
            <div class="xl:col-span-5 flex flex-col gap-4">
                @php
                    $kuadranMeta = [
                        'Prioritas Utama' => ['bg-error', 'Kritis: kemiskinan tinggi &amp; memburuk. Intervensi segera.'],
                        'Membaik (Tetap Diawasi)' => ['bg-primary', 'Masih tinggi tapi membaik. Jaga momentum.'],
                        'Waspada (Mulai Naik)' => ['bg-warning', 'Rendah tapi mulai naik. Cegah dini.'],
                        'Pantau (Stabil)' => ['bg-success', 'Rendah &amp; stabil. Pertahankan.'],
                    ];
                @endphp
                @foreach($kuadranMeta as $label => $meta)
                @php $anggota = array_values(array_filter($prioritas, fn($p) => $p['kuadran'] === $label)); @endphp
                <div class="feature-card">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-3 h-3 rounded-sm {{ $meta[0] }}"></span>
                        <h3 class="body-sm text-ink font-medium">{!! $label !!}</h3>
                        <span class="code text-xs text-muted ml-auto">{{ count($anggota) }}</span>
                    </div>
                    <p class="body-xs text-muted mb-2">{!! $meta[1] !!}</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($anggota as $a)
                        <span class="badge-pill bg-surface-strong text-ink">{{ $a['kecamatan'] }}</span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ============ 4. Kemiskinan x Bencana ============ -->
    <section id="bencana" class="mb-14">
        <div class="flex items-end justify-between mb-6">
            <div>
                <h2 class="display-sm text-ink">Kemiskinan × Bencana</h2>
                <p class="body-sm text-muted mt-1">Kecamatan miskin dan rawan bencana (kuadran kanan-atas) butuh proteksi ganda.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-7 feature-card flex flex-col">
                <div class="relative w-full flex-1 min-h-[420px] bg-canvas-soft rounded-[8px] p-2">
                    <canvas id="bencanaChart"></canvas>
                </div>
            </div>
            <div class="xl:col-span-5 feature-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-hairline">
                                <th class="py-3 pr-3 caption-uppercase text-muted font-normal">Kecamatan</th>
                                <th class="py-3 pr-3 caption-uppercase text-muted font-normal text-right">% Kesejahteraan Terendah</th>
                                <th class="py-3 caption-uppercase text-muted font-normal text-right">Bencana</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(collect($bencanaMatrix)->sortByDesc('bencana') as $row)
                            <tr class="border-b border-hairline last:border-0">
                                <td class="py-2.5 pr-3 body-sm text-ink">{{ $row['kecamatan'] }}</td>
                                <td class="py-2.5 pr-3 body-sm tabular text-right text-ink">{{ number_format($row['kemiskinan'], 1, ',', '.') }}%</td>
                                <td class="py-2.5 body-sm tabular text-right {{ $row['bencana'] >= 4 ? 'text-error font-medium' : 'text-ink' }}">{{ $row['bencana'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ 5. Hotspot Spasial ============ -->
    <section id="hotspot" class="mb-14">
        <div class="flex items-end justify-between mb-6">
            <div>
                <h2 class="display-sm text-ink">Klasifikasi Hotspot Spasial (LISA)</h2>
                <p class="body-sm text-muted mt-1">Local Moran's I dengan 3 tetangga terdekat dari centroid GeoJSON.</p>
            </div>
        </div>

        {{-- Peta choropleth LISA --}}
        <div class="ide-mockup-card overflow-hidden relative mb-6">
            <div class="absolute left-5 bottom-5 z-[1000] bg-surface-card/95 border border-hairline px-4 py-3 panel pointer-events-none">
                <div class="caption-uppercase text-muted mb-2">Klasifikasi LISA</div>
                <div class="flex flex-col gap-1.5 body-xs">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background-color:#cf2d56"></span> HH — hotspot (tinggi-tinggi)</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background-color:#1f8a65"></span> LL — coldspot (rendah-rendah)</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background-color:#f54e00"></span> HL — outlier tinggi</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background-color:#d97706"></span> LH — outlier rendah</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background-color:#c9c4b8"></span> Tidak signifikan</span>
                </div>
            </div>
            <div id="lisaMap" class="w-full h-[560px] z-0"></div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @php
                $typeColor = [
                    'HH (Hotspot)' => 'bg-error text-white',
                    'LL (Coldspot)' => 'bg-success text-white',
                    'HL (Outlier Tinggi)' => 'bg-primary text-white',
                    'LH (Outlier Rendah)' => 'bg-warning text-ink',
                    'Tidak Signifikan' => 'bg-surface-strong text-muted',
                ];
            @endphp
            @foreach($lisa as $row)
            @php $c = $typeColor[$row['type']] ?? 'bg-surface-strong text-muted'; @endphp
            <div class="border border-hairline panel p-4 flex flex-col gap-1">
                <h4 class="body-sm text-ink leading-tight">{{ $row['kecamatan'] }}</h4>
                <span class="badge-pill self-start {{ $c }}">{{ $row['type'] }}</span>
            </div>
            @endforeach
        </div>

        <div class="flex flex-wrap gap-4 body-xs text-muted mt-4">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-error"></span> HH — hotspot (tinggi-tinggi)</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-success"></span> LL — coldspot (rendah-rendah)</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-primary"></span> HL — outlier tinggi</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-warning"></span> LH — outlier rendah</span>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- ArcGIS Maps SDK (muat SETELAH Chart.js agar loader AMD tidak bentrok) --}}
<script src="https://js.arcgis.com/4.31/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const CHART_FONT = { family: "'Inter', sans-serif", size: 11 };

    // ---- 1. Ketepatan sasaran (horizontal bar indeks cakupan) ----
    const targeting = @json($targeting);
    const cakupanCtx = document.getElementById('cakupanChart').getContext('2d');
    const cakupanSorted = [...targeting].sort((a, b) => a.indeks_cakupan - b.indeks_cakupan);
    const statusColor = s => s === 'Kurang Terjangkau' ? '#d97706' : (s === 'Proporsional' ? '#1f8a65' : '#cf2d56');
    new Chart(cakupanCtx, {
        type: 'bar',
        data: {
            labels: cakupanSorted.map(r => r.kecamatan),
            datasets: [{
                data: cakupanSorted.map(r => r.indeks_cakupan),
                backgroundColor: cakupanSorted.map(r => statusColor(r.status)),
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: c => ' Indeks ' + c.parsed.x.toFixed(2) } }
            },
            scales: {
                x: { title: { display: true, text: 'Indeks cakupan (1 = proporsional)', font: CHART_FONT }, grid: { color: 'rgba(38,37,30,0.06)' } },
                y: { ticks: { font: CHART_FONT } }
            }
        }
    });

    // ---- 2. IKK ranking (horizontal bar) ----
    const ikk = @json($ikk);
    const ikkCtx = document.getElementById('ikkChart').getContext('2d');
    const ikkSorted = [...ikk].sort((a, b) => a.skor - b.skor);
    new Chart(ikkCtx, {
        type: 'bar',
        data: {
            labels: ikkSorted.map(r => r.kecamatan),
            datasets: [{
                data: ikkSorted.map(r => r.skor),
                backgroundColor: ikkSorted.map(r => r.skor >= 60 ? 'rgba(207,45,86,0.85)' : 'rgba(245,78,0,0.75)'),
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { min: 0, max: 100, title: { display: true, text: 'Skor IKK (0–100)', font: CHART_FONT }, grid: { color: 'rgba(38,37,30,0.06)' } },
                y: { ticks: { font: CHART_FONT } }
            }
        }
    });

    // ---- 3. Matriks Prioritas 2x2 ----
    const prioritas = @json($prioritas);
    const medTingkat = median(prioritas.map(p => p.tingkat));
    const medTren = median(prioritas.map(p => p.tren));
    const kuadranColor = k => k === 'Prioritas Utama' ? '#cf2d56' : (k === 'Membaik (Tetap Diawasi)' ? '#f54e00' : (k === 'Waspada (Mulai Naik)' ? '#d97706' : '#1f8a65'));
    const prioritasCtx = document.getElementById('prioritasChart').getContext('2d');
    new Chart(prioritasCtx, {
        type: 'scatter',
        data: {
            datasets: [{
                data: prioritas.map(p => ({ x: p.tingkat, y: p.tren, kecamatan: p.kecamatan, kuadran: p.kuadran })),
                backgroundColor: prioritas.map(p => kuadranColor(p.kuadran)),
                pointRadius: 6, pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: c => ' ' + c.raw.kecamatan + ' (' + c.raw.kuadran + ')' } }
            },
            scales: {
                x: { title: { display: true, text: 'Tingkat kemiskinan (% kesejahteraan terendah)', font: CHART_FONT }, grid: { color: 'rgba(38,37,30,0.06)' } },
                y: { title: { display: true, text: 'Tren Desil 1 (%)', font: CHART_FONT }, grid: { color: 'rgba(38,37,30,0.06)' } }
            },
            plugins: [{
                id: 'quadrantLines',
                afterDraw(chart) {
                    const { ctx, chartArea: { left, right, top, bottom }, scales: { x, y } } = chart;
                    const xm = x.getPixelForValue(medTingkat);
                    const ym = y.getPixelForValue(medTren);
                    ctx.save();
                    ctx.strokeStyle = 'rgba(38,37,30,0.25)';
                    ctx.setLineDash([4, 4]);
                    ctx.beginPath();
                    ctx.moveTo(left, ym); ctx.lineTo(right, ym);
                    ctx.moveTo(xm, top); ctx.lineTo(xm, bottom);
                    ctx.stroke();
                    ctx.restore();
                }
            }]
        }
    });

    // ---- 4. Kemiskinan x Bencana ----
    const bencanaMatrix = @json($bencanaMatrix);
    const bencanaCtx = document.getElementById('bencanaChart').getContext('2d');
    new Chart(bencanaCtx, {
        type: 'scatter',
        data: {
            datasets: [{
                data: bencanaMatrix.map(r => ({ x: r.kemiskinan, y: r.bencana, kecamatan: r.kecamatan })),
                backgroundColor: 'rgba(207,45,86,0.7)',
                pointRadius: 6, pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: c => ' ' + c.raw.kecamatan + ' (' + c.raw.y + ' bencana)' } }
            },
            scales: {
                x: { title: { display: true, text: '% kesejahteraan terendah', font: CHART_FONT }, grid: { color: 'rgba(38,37,30,0.06)' } },
                y: { title: { display: true, text: 'Kejadian bencana', font: CHART_FONT }, grid: { color: 'rgba(38,37,30,0.06)' }, beginAtZero: true }
            }
        }
    });

    function median(arr) {
        const s = [...arr].sort((a, b) => a - b);
        const mid = Math.floor(s.length / 2);
        return s.length % 2 ? s[mid] : (s[mid - 1] + s[mid]) / 2;
    }

    // ---- 5. Peta LISA (choropleth klasifikasi hotspot) ----
    const lisaData = @json($lisa);
    // kanonikalisasi nama kecamatan (UPPERCASE + tanpa spasi) agar cocok dgn properti GeoJSON
    const lisaTypeByName = {};
    const lisaZiByName = {};
    lisaData.forEach(function (row) {
        const key = String(row.kecamatan).toUpperCase().replace(/\s+/g, '');
        lisaTypeByName[key] = row.type;
        lisaZiByName[key] = row.zi;
    });

    const LISA_COLORS = {
        'HH (Hotspot)': '#cf2d56',
        'LL (Coldspot)': '#1f8a65',
        'HL (Outlier Tinggi)': '#f54e00',
        'LH (Outlier Rendah)': '#d97706',
        'Tidak Signifikan': '#c9c4b8'
    };

    require([
        'esri/Map',
        'esri/views/MapView',
        'esri/layers/GeoJSONLayer',
        'esri/renderers/UniqueValueRenderer',
        'esri/symbols/SimpleFillSymbol'
    ], function (Map, MapView, GeoJSONLayer, UniqueValueRenderer, SimpleFillSymbol) {

        function lisaFill(color) {
            return new SimpleFillSymbol({
                color: color,
                opacity: 0.8,
                outline: { color: '#ffffff', width: 1.5 }
            });
        }

        const renderer = new UniqueValueRenderer({
            field: 'LISA_TYPE',
            uniqueValueInfos: [
                { value: 'HH (Hotspot)', label: 'HH — hotspot (tinggi-tinggi)', symbol: lisaFill(LISA_COLORS['HH (Hotspot)']) },
                { value: 'LL (Coldspot)', label: 'LL — coldspot (rendah-rendah)', symbol: lisaFill(LISA_COLORS['LL (Coldspot)']) },
                { value: 'HL (Outlier Tinggi)', label: 'HL — outlier tinggi', symbol: lisaFill(LISA_COLORS['HL (Outlier Tinggi)']) },
                { value: 'LH (Outlier Rendah)', label: 'LH — outlier rendah', symbol: lisaFill(LISA_COLORS['LH (Outlier Rendah)']) },
                { value: 'Tidak Signifikan', label: 'Tidak signifikan', symbol: lisaFill(LISA_COLORS['Tidak Signifikan']) }
            ],
            defaultSymbol: lisaFill('#e6e5e0')
        });

        fetch('/geojson/peta_kecamatan.geojson')
            .then(response => response.json())
            .then(geojson => {
                // Tandai tiap kecamatan dengan klasifikasi LISA
                geojson.features.forEach(function (feature) {
                    const name = feature.properties.Kecamatan ? feature.properties.Kecamatan.toUpperCase() : '';
                    feature.properties.LISA_TYPE = lisaTypeByName[name] || 'Tidak Signifikan';
                    feature.properties.LISA_ZI = lisaZiByName[name] != null ? lisaZiByName[name] : '—';
                });

                const blob = new Blob([JSON.stringify(geojson)], { type: 'application/json' });
                const url = URL.createObjectURL(blob);

                const geojsonLayer = new GeoJSONLayer({
                    url: url,
                    renderer: renderer,
                    popupTemplate: {
                        title: 'Kecamatan {Kecamatan}',
                        content: `
                            <div style="font-family: Inter, sans-serif; min-width: 220px">
                                <div style="margin-top:8px">
                                    <span style="color:#807d72;font-size:10px;text-transform:uppercase;letter-spacing:.08em">Klasifikasi LISA:</span><br/>
                                    <span style="color:#26251e;font-weight:600;font-size:14px">{LISA_TYPE}</span>
                                </div>
                                <div style="margin-top:6px;padding-top:6px;border-top:1px solid #e6e5e0">
                                    <span style="color:#807d72;font-size:10px;text-transform:uppercase;letter-spacing:.08em">Local Moran's I (zi):</span><br/>
                                    <span style="color:#f54e00;font-weight:600;font-size:14px">{LISA_ZI}</span>
                                </div>
                            </div>
                        `
                    }
                });

                const map = new Map({
                    basemap: null,
                    layers: [geojsonLayer]
                });

                const view = new MapView({
                    container: 'lisaMap',
                    map: map,
                    background: { color: '#f7f7f4' },
                    center: [109.6965, -7.3946],
                    zoom: 10
                });

                // Pastikan seluruh batas kecamatan muat dalam frame peta
                view.whenLayerView(geojsonLayer).then(function () {
                    if (geojsonLayer.fullExtent) {
                        view.goTo(geojsonLayer.fullExtent.expand(1.15));
                    }
                }).catch(function (err) {
                    console.error('Gagal menyesuaikan batas peta LISA:', err);
                });
            })
            .catch(error => console.error('Error loading GeoJSON LISA:', error));
    });
});
</script>
@endpush