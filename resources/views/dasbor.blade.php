@extends('layouts.app')

@section('title', 'Dasbor Spasial')

@section('content')
<div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-14">

    <!-- ============ Page header ============ -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <div class="section-label mb-4">
                <span aria-hidden="true">[</span>Spatial Analytics<span aria-hidden="true">]</span>
            </div>
            <h1 class="display-lg text-ink">Dasbor Kemiskinan</h1>
            <p class="body-md text-body mt-3 max-w-2xl">
                Pemetaan spasial distribusi kemiskinan ekstrem dan alokasi bantuan sosial per kecamatan di Kabupaten Banjarnegara.
            </p>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="relative">
                <select name="tahun" onchange="this.form.submit()" class="select h-11 w-60 pr-10">
                    <option value="">Semua Tahun (Terbaru)</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $tahunFilter == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <noscript><button type="submit" class="btn btn-secondary h-11">Filter</button></noscript>
        </form>
    </div>

    <!-- ============ Stat cards ============ -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

        <!-- Total penduduk miskin -->
        <div class="feature-card lg:col-span-2 border-t-2 border-t-primary">
            <div class="caption-uppercase text-muted mb-2">Total Penduduk Miskin</div>
            <div class="flex items-baseline gap-2">
                <span class="display-md text-ink tabular">{{ number_format($totalMiskin, 0, ',', '.') }}</span>
                <span class="body-sm text-muted">Jiwa</span>
            </div>
            <div class="body-sm text-muted-soft mt-1">Tahun {{ $tahunFilter ?? '—' }}</div>
        </div>

        <!-- Indeks kedalaman -->
        <div class="feature-card">
            <div class="caption-uppercase text-muted mb-2">Indeks Kedalaman (P1)</div>
            <span class="display-md text-ink tabular">{{ number_format($rataKedalaman, 2) }}</span>
        </div>

        <!-- Indeks keparahan -->
        <div class="feature-card">
            <div class="caption-uppercase text-muted mb-2">Indeks Keparahan (P2)</div>
            <span class="display-md text-ink tabular">{{ number_format($rataKeparahan, 2) }}</span>
        </div>

        <!-- Bantuan sosial -->
        <div class="feature-card">
            <div class="caption-uppercase text-muted mb-2">Intervensi Bantuan</div>
            <div class="flex items-baseline gap-2">
                <span class="display-md text-ink tabular">{{ number_format($totalPenerimaBansos, 0, ',', '.') }}</span>
                <span class="body-sm text-muted">KK</span>
            </div>
            <div class="body-sm text-muted-soft mt-1">Rp {{ number_format($totalAnggaranBansos / 1000000000, 2, ',', '.') }} M</div>
        </div>
    </div>

    <!-- ============ Peta spasial ============ -->
    <section class="mb-6">
        <div class="ide-mockup-card overflow-hidden relative">
            <!-- Panel header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-hairline">
                <div class="flex items-center gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-primary"></div>
                    <h2 class="title-sm text-ink">Peta Spasial Kemiskinan@include('partials.p3ke-info')</h2>
                </div>
                <span class="caption-uppercase text-muted">Tahun: <span class="text-primary">{{ $maxTahunKesejahteraan ?? 'N/A' }}</span></span>
            </div>

            <!-- Legend -->
            <div class="absolute left-5 bottom-5 z-[1000] bg-surface-card/95 border border-hairline px-4 py-3 panel pointer-events-none">
                <div class="caption-uppercase text-muted mb-2">Skala Kepadatan</div>
                <div class="flex items-center gap-1">
                    <div class="w-8 h-3 rounded-l-full" style="background-color: #fbe4d8"></div>
                    <div class="w-8 h-3" style="background-color: #f8bfa5"></div>
                    <div class="w-8 h-3" style="background-color: #f58c68"></div>
                    <div class="w-8 h-3" style="background-color: #f54e00"></div>
                    <div class="w-8 h-3 rounded-r-full" style="background-color: #b83a00"></div>
                </div>
                <div class="flex justify-between text-[9px] font-medium text-muted mt-1">
                    <span>Rendah</span>
                    <span>Tinggi</span>
                </div>
            </div>

            <div id="map" class="w-full h-[560px] z-0"></div>
        </div>
    </section>

    <!-- ============ Grafik ============ -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Tren -->
        <div class="lg:col-span-7 feature-card flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h2 class="title-sm text-ink">Tren Penduduk Miskin</h2>
                <span class="code text-muted">line</span>
            </div>
            <div class="relative flex-1 w-full min-h-[300px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Komparasi indeks -->
        <div class="lg:col-span-5 feature-card flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h2 class="title-sm text-ink">Komparasi Indeks (P1 &amp; P2)</h2>
                <span class="code text-muted">bar</span>
            </div>
            <div class="relative flex-1 w-full min-h-[300px]">
                <canvas id="indexChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Unduh -->
    <div class="flex justify-center md:justify-end mt-6">
        <a href="{{ route('export.csv') }}" class="btn btn-download">
            <span aria-hidden="true">↓</span><span>Unduh Laporan Lengkap (.csv)</span>
        </a>
    </div>

</div>
@endsection

@push('scripts')
{{-- Muat Chart.js terlebih dahulu (sebelum ArcGIS SDK) agar tidak bentrok dengan loader AMD Dojo --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
{{-- ArcGIS Maps SDK for JavaScript (Esri) — hanya di halaman dasbor --}}
<script src="https://js.arcgis.com/4.31/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Map (ArcGIS Maps SDK for JavaScript — Esri)
        const mapData = @json($kesejahteraanMap);
        const bansosData = @json($bansosMap);
        const nf = new Intl.NumberFormat('id-ID');

        require([
            'esri/Map',
            'esri/views/MapView',
            'esri/layers/GeoJSONLayer',
            'esri/renderers/ClassBreaksRenderer',
            'esri/symbols/SimpleFillSymbol'
        ], function(Map, MapView, GeoJSONLayer, ClassBreaksRenderer, SimpleFillSymbol) {

            function fillSymbol(color) {
                return new SimpleFillSymbol({
                    color: color,
                    opacity: 0.8,
                    outline: {
                        color: '#ffffff',
                        width: 2
                    }
                });
            }

            fetch('/geojson/peta_kecamatan.geojson')
                .then(response => response.json())
                .then(geojson => {
                    // Enrich features with desil + bansos values, plus formatted strings
                    geojson.features.forEach(function(feature) {
                        const name = feature.properties.Kecamatan ? feature.properties.Kecamatan.toUpperCase() : '';
                        const desil = mapData[name] ? mapData[name].desil_1 : 0;
                        const jumlah = bansosData[name] ? bansosData[name].jumlah_penerima : 0;
                        const anggaran = bansosData[name] ? bansosData[name].anggaran_ribu_rupiah : 0;

                        feature.properties.DESIL = desil;
                        feature.properties.DESIL_FMT = nf.format(desil);
                        feature.properties.JUMLAH_FMT = nf.format(jumlah);
                        feature.properties.ANGGARAN_FMT = nf.format(anggaran);
                    });

                    const blob = new Blob([JSON.stringify(geojson)], { type: 'application/json' });
                    const url = URL.createObjectURL(blob);

                    const renderer = new ClassBreaksRenderer({
                        field: 'DESIL',
                        classBreakInfos: [
                            { minValue: 0, maxValue: 5000, label: 'Rendah', symbol: fillSymbol('#fbe4d8') },
                            { minValue: 5000, maxValue: 10000, label: 'Sedikit', symbol: fillSymbol('#f8bfa5') },
                            { minValue: 10000, maxValue: 15000, label: 'Sedang', symbol: fillSymbol('#f58c68') },
                            { minValue: 15000, maxValue: 20000, label: 'Tinggi', symbol: fillSymbol('#f54e00') },
                            { minValue: 20000, maxValue: Number.MAX_VALUE, label: 'Sangat Tinggi', symbol: fillSymbol('#b83a00') }
                        ]
                    });

                    const geojsonLayer = new GeoJSONLayer({
                        url: url,
                        renderer: renderer,
                        popupTemplate: {
                            title: 'Kecamatan {Kecamatan}',
                            content: `
                                <div style="font-family: Inter, sans-serif; min-width: 220px">
                                    <div style="margin-top:8px">
                                        <span style="color:#807d72;font-size:10px;text-transform:uppercase;letter-spacing:.08em">Individu Kesejahteraan Terendah (Desil 1):</span><br/>
                                        <span style="color:#f54e00;font-weight:600;font-size:14px">{DESIL_FMT} Jiwa</span>
                                    </div>
                                    <div style="margin-top:6px;padding-top:6px;border-top:1px solid #e6e5e0">
                                        <span style="color:#807d72;font-size:10px;text-transform:uppercase;letter-spacing:.08em">Penerima Bantuan:</span><br/>
                                        <span style="color:#1f8a65;font-weight:600;font-size:14px">{JUMLAH_FMT} KK</span>
                                    </div>
                                    <div style="margin-top:4px">
                                        <span style="color:#807d72;font-size:10px;text-transform:uppercase;letter-spacing:.08em">Bansos Pangan (Per Bulan):</span><br/>
                                        <span style="color:#1f8a65;font-weight:600;font-size:14px">Rp {ANGGARAN_FMT}</span>
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
                        container: 'map',
                        map: map,
                        background: { color: '#f7f7f4' },
                        center: [109.6965, -7.3946],
                        zoom: 10
                    });

                    // Pastikan seluruh batas kecamatan (GeoJSON) muat dalam frame peta
                    view.whenLayerView(geojsonLayer).then(function () {
                        if (geojsonLayer.fullExtent) {
                            view.goTo(geojsonLayer.fullExtent.expand(1.15));
                        }
                    }).catch(function (err) {
                        console.error('Gagal menyesuaikan batas peta:', err);
                    });
                })
                .catch(error => console.error('Error loading GeoJSON:', error));
        });

        // Line chart: tren kemiskinan
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Jumlah Penduduk Miskin',
                    data: @json($chartDataPenduduk),
                    borderColor: '#f54e00',
                    backgroundColor: 'rgba(245, 78, 0, 0.12)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: '#f54e00'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 16,
                            font: { family: "'Inter', sans-serif", size: 12 }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: '#efeee8' },
                        ticks: { font: { family: "'Inter', sans-serif", size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Inter', sans-serif", size: 11 } }
                    }
                }
            }
        });

        // Bar chart: indeks kedalaman vs keparahan
        const ctxIndex = document.getElementById('indexChart').getContext('2d');
        new Chart(ctxIndex, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Indeks Kedalaman',
                        data: @json($chartDataKedalaman),
                        backgroundColor: '#f54e00',
                        borderRadius: 4,
                    },
                    {
                        label: 'Indeks Keparahan',
                        data: @json($chartDataKeparahan),
                        backgroundColor: '#26251e',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 16,
                            font: { family: "'Inter', sans-serif", size: 12 }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#efeee8' },
                        ticks: { font: { family: "'Inter', sans-serif", size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Inter', sans-serif", size: 11 } }
                    }
                }
            }
        });
    });
</script>
@endpush