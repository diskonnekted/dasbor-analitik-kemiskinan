@extends('layouts.app')

@section('title', 'Dasbor Analitik Kemiskinan')

@section('content')
<div class="flex-grow overflow-y-auto w-full py-8 px-6 sm:px-8 space-y-8 bg-[#f4f4f0] text-[#171717]">
    
    <!-- Hero Header Section -->
    <div class="relative bg-white border-2 border-[#171717] p-8 md:p-10 shadow-[4px_4px_0px_0px_#171717] flex flex-col xl:flex-row xl:items-center justify-between gap-8">
        
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#171717] text-white text-xs font-mono font-bold tracking-widest uppercase mb-4 border border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <span class="w-2.5 h-2.5 bg-white border border-[#171717]"></span>
                SPATIAL ANALYTICS
            </div>
            <h1 class="text-3xl md:text-4xl font-serif font-black text-[#171717] uppercase tracking-tight mb-2">Dasbor Kemiskinan</h1>
            <p class="text-neutral-600 font-mono font-bold text-sm max-w-2xl">Pemetaan spasial distribusi kemiskinan ekstrem dan alokasi bantuan sosial di wilayah Kabupaten Banjarnegara.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-4 bg-white p-2 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center w-full sm:w-auto">
                <div class="relative flex items-center w-full">
                    <div class="absolute left-4 pointer-events-none">
                        <i data-lucide="calendar" class="w-4 h-4 text-[#171717]"></i>
                    </div>
                    <select name="tahun" onchange="this.form.submit()" class="w-full sm:w-64 appearance-none bg-transparent border-none focus:ring-0 py-3 pl-12 pr-10 text-[#171717] font-mono font-black text-base cursor-pointer">
                        <option value="">Semua Tahun (Terbaru)</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $tahunFilter == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Stats Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Card 1: Total Penduduk Miskin (Besar) -->
        <div class="lg:col-span-2 bg-[#171717] text-white p-8 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="font-mono text-neutral-300 font-black mb-2 text-xs uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2 h-2 bg-white"></span>
                    Total Penduduk Miskin
                </h3>
                <p class="text-4xl font-serif font-black text-white mt-4">{{ number_format($totalMiskin, 0, ',', '.') }} <span class="text-lg font-bold text-neutral-300">Jiwa</span></p>
            </div>
        </div>

        <!-- Card 2 & 3: Indeks Kemiskinan -->
        <div class="bg-white p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col justify-center">
            <h3 class="text-neutral-500 font-black mb-1 text-[10px] uppercase tracking-widest font-mono">Indeks Kedalaman (P1)</h3>
            <p class="text-3xl font-serif font-black text-[#171717]">{{ number_format($rataKedalaman, 2) }}</p>
        </div>
        
        <div class="bg-white p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col justify-center">
            <h3 class="text-neutral-500 font-black mb-1 text-[10px] uppercase tracking-widest font-mono">Indeks Keparahan (P2)</h3>
            <p class="text-3xl font-serif font-black text-[#171717]">{{ number_format($rataKeparahan, 2) }}</p>
        </div>

        <!-- Card 4: Bantuan Sosial -->
        <div class="bg-white p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col justify-center">
            <div class="relative z-10">
                <h3 class="text-neutral-500 font-black mb-1 text-[10px] uppercase tracking-widest font-mono">Intervensi Bantuan</h3>
                <p class="text-2xl font-serif font-black text-[#171717]">{{ number_format($totalPenerimaBansos, 0, ',', '.') }} <span class="text-xs font-bold text-neutral-500 font-sans">KK</span></p>
                <div class="mt-2 text-xs font-mono font-black text-white bg-[#171717] px-2 py-1 inline-block border border-[#171717] shadow-[2px_2px_0px_0px_#f4f4f0]">Rp {{ number_format($totalAnggaranBansos / 1000000000, 2, ',', '.') }} M</div>
            </div>
        </div>
    </div>

    <!-- Main Map Section -->
    <div class="bg-white border-2 border-[#171717] overflow-hidden flex flex-col relative shadow-[4px_4px_0px_0px_#171717]">
        <!-- Floating Info Panel -->
        <div class="absolute top-6 left-6 z-[1000] bg-white border-2 border-[#171717] px-5 py-4 shadow-[2px_2px_0px_0px_#171717] pointer-events-none">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-3.5 h-3.5 bg-[#171717] border border-white"></div>
                <h2 class="text-lg font-serif font-black text-[#171717] uppercase tracking-tight">Peta Spasial</h2>
            </div>
            <p class="text-[10px] text-neutral-600 font-mono font-bold uppercase tracking-widest">Tahun: <span class="text-[#171717] font-black underline">{{ $maxTahunKesejahteraan ?? 'N/A' }}</span></p>
        </div>
        
        <!-- Legend Overlay -->
        <div class="absolute bottom-6 left-6 z-[1000] bg-white border-2 border-[#171717] px-4 py-3 shadow-[2px_2px_0px_0px_#171717] pointer-events-none">
            <h4 class="text-[10px] font-mono font-black text-[#171717] uppercase tracking-widest mb-2">Kepadatan Penduduk Sangat Miskin</h4>
            <div class="flex items-center gap-1">
                <div class="w-8 h-4 border border-[#171717]" style="background-color: #ffffff"></div>
                <div class="w-8 h-4 border border-[#171717]" style="background-color: #e5e5d8"></div>
                <div class="w-8 h-4 border border-[#171717]" style="background-color: #a3a398"></div>
                <div class="w-8 h-4 border border-[#171717]" style="background-color: #52524a"></div>
                <div class="w-8 h-4 border border-[#171717]" style="background-color: #171717"></div>
            </div>
            <div class="flex justify-between text-[9px] font-mono font-black text-[#171717] mt-1 uppercase">
                <span>Rendah</span>
                <span>Tinggi</span>
            </div>
        </div>

        <div id="map" class="w-full h-[650px] z-0"></div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Trend Chart (Takes 7 cols) -->
        <div class="lg:col-span-7 bg-white p-8 md:p-10 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col relative">
            <h2 class="text-lg font-serif font-black text-[#171717] mb-6 flex items-center gap-2 uppercase">
                <i data-lucide="trending-up" class="w-5 h-5 text-[#171717]"></i>
                Tren Penduduk Miskin
            </h2>
            <div class="relative flex-1 w-full min-h-[300px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Bar Chart: Perbandingan Kedalaman vs Keparahan (Takes 5 cols) -->
        <div class="lg:col-span-5 bg-white p-8 md:p-10 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col relative">
            <h2 class="text-lg font-serif font-black text-[#171717] mb-6 flex items-center gap-2 uppercase">
                <i data-lucide="bar-chart" class="w-5 h-5 text-[#171717]"></i>
                Komparasi Indeks (P1 & P2)
            </h2>
            <div class="relative flex-1 w-full min-h-[300px]">
                <canvas id="indexChart"></canvas>
            </div>
        </div>

    </div>
    
    <div class="flex justify-center md:justify-end mt-4">
        <a href="{{ route('export.csv') }}" class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-[#171717] text-white font-mono font-black border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] hover:shadow-[6px_6px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">
            <i data-lucide="download" class="w-5 h-5"></i>
            Unduh Laporan Lengkap (.csv)
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Map
        var map = L.map('map').setView([-7.3946, 109.6965], 11);

        // Add Tile Layer (Monochrome/Grayscale style)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        const mapData = @json($kesejahteraanMap);
        const bansosData = @json($bansosMap);

        // Load GeoJSON for Kecamatan
        fetch('/geojson/peta_kecamatan.geojson')
            .then(response => response.json())
            .then(data => {
                L.geoJSON(data, {
                    style: function(feature) {
                        let name = feature.properties.Kecamatan ? feature.properties.Kecamatan.toUpperCase() : '';
                        let value = mapData[name] ? mapData[name].desil_1 : 0;
                        
                        let color = '#ffffff';
                        if (value > 20000) color = '#171717';
                        else if (value > 15000) color = '#52524a';
                        else if (value > 10000) color = '#a3a398';
                        else if (value > 5000) color = '#e5e5d8';

                        return {
                            color: '#171717',
                            weight: 2,
                            fillColor: color,
                            fillOpacity: 0.8,
                            interactive: true
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        let name = feature.properties.Kecamatan ? feature.properties.Kecamatan.toUpperCase() : '';
                        let valueMiskin = mapData[name] ? mapData[name].desil_1 : 0;
                        
                        let jumlahPenerima = bansosData[name] ? bansosData[name].jumlah_penerima : 0;
                        let anggaranRupiah = bansosData[name] ? bansosData[name].anggaran_ribu_rupiah : 0;
                        
                        let tooltipContent = `
                            <div class="font-sans text-xs p-2 bg-white border-2 border-[#171717] text-[#171717]">
                                <b class="text-sm font-serif font-black uppercase border-b border-[#171717] pb-1 mb-2 block">Kec. ${name}</b>
                                <div class="mt-2">
                                    <span class="text-[9px] uppercase font-mono font-bold text-neutral-500">Sangat Miskin:</span><br/>
                                    <span class="font-bold">${new Intl.NumberFormat('id-ID').format(valueMiskin)} Jiwa</span>
                                </div>
                                <div class="mt-2 pt-2 border-t border-dashed border-[#171717]">
                                    <span class="text-[9px] uppercase font-mono font-bold text-neutral-500">Penerima Bantuan:</span><br/>
                                    <span class="font-bold">${new Intl.NumberFormat('id-ID').format(jumlahPenerima)} KK</span>
                                </div>
                                <div class="mt-1">
                                    <span class="text-[9px] uppercase font-mono font-bold text-neutral-500">Anggaran Bansos/Bulan:</span><br/>
                                    <span class="font-bold">Rp ${new Intl.NumberFormat('id-ID').format(anggaranRupiah)}</span>
                                </div>
                            </div>
                        `;
                        layer.bindPopup(tooltipContent, {
                            className: 'brutal-popup'
                        });
                    }
                }).addTo(map);
            })
            .catch(error => console.error('Error loading GeoJSON:', error));

        // Line Chart: Tren Kemiskinan
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Jumlah Penduduk Miskin',
                    data: @json($chartDataPenduduk),
                    borderColor: '#171717',
                    backgroundColor: '#e5e5d8',
                    borderWidth: 3,
                    fill: false,
                    tension: 0,
                    pointBackgroundColor: '#171717',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            font: { family: 'Inter', weight: 'bold' }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#171717', width: 1 },
                        ticks: { font: { family: 'Inter', weight: 'bold' }, color: '#171717' }
                    },
                    y: {
                        grid: { color: '#e5e5d8' },
                        ticks: { font: { family: 'Inter', weight: 'bold' }, color: '#171717' }
                    }
                }
            }
        });

        // Bar Chart: Indeks Kedalaman vs Keparahan
        const ctxIndex = document.getElementById('indexChart').getContext('2d');
        new Chart(ctxIndex, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Indeks Kedalaman',
                        data: @json($chartDataKedalaman),
                        backgroundColor: '#171717',
                        borderColor: '#171717',
                        borderWidth: 1
                    },
                    {
                        label: 'Indeks Keparahan',
                        data: @json($chartDataKeparahan),
                        backgroundColor: '#a3a398',
                        borderColor: '#171717',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            font: { family: 'Inter', weight: 'bold' }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#171717', width: 1 },
                        ticks: { font: { family: 'Inter', weight: 'bold' }, color: '#171717' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e5e5d8' },
                        ticks: { font: { family: 'Inter', weight: 'bold' }, color: '#171717' }
                    }
                }
            }
        });
    });
</script>
<style>
    /* Popup styling overrides */
    .brutal-popup .leaflet-popup-content-wrapper {
        background: #ffffff !important;
        border: 2px solid #171717 !important;
        border-radius: 0px !important;
        box-shadow: 4px 4px 0px 0px #171717 !important;
    }
    .brutal-popup .leaflet-popup-tip {
        border: 1px solid #171717 !important;
        background: #ffffff !important;
    }
</style>
@endpush
