@extends('layouts.app')

@section('title', 'Dasbor Analitik Kemiskinan')

@section('content')
<div class="w-full mx-auto py-8 px-4 sm:px-8 lg:px-12 space-y-8 bg-[#f8fafc] min-h-screen">
    
    <!-- Hero Header Section -->
    <div class="relative overflow-hidden bg-slate-900 rounded-[2.5rem] p-8 md:p-12 shadow-2xl border border-slate-800 flex flex-col xl:flex-row xl:items-center justify-between gap-8">
        <!-- Decorative Glow -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-red-500/10 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500/10 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-slate-300 text-xs font-bold tracking-widest uppercase mb-4">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Live Spatial Analytics
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-2">Dasbor <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-orange-400">Kemiskinan</span></h1>
            <p class="text-slate-400 text-lg max-w-2xl">Pemetaan spasial distribusi kemiskinan ekstrem dan alokasi bantuan sosial di wilayah Kabupaten Banjarnegara.</p>
        </div>
        
        <div class="relative z-10 flex flex-col sm:flex-row items-center gap-4 bg-white/5 p-2 rounded-2xl border border-white/10 backdrop-blur-md">
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center w-full sm:w-auto">
                <div class="relative flex items-center w-full">
                    <div class="absolute left-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <select name="tahun" onchange="this.form.submit()" class="w-full sm:w-64 appearance-none bg-transparent border-none focus:ring-0 py-3 pl-12 pr-10 text-white font-bold text-lg cursor-pointer">
                        <option value="" class="text-slate-900">Semua Tahun (Terbaru)</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $tahunFilter == $year ? 'selected' : '' }} class="text-slate-900">{{ $year }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-4 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </div>
                <noscript>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl ml-2">Filter</button>
                </noscript>
            </form>
        </div>
    </div>

    <!-- Quick Stats Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Card 1: Total Penduduk Miskin (Besar) -->
        <div class="lg:col-span-2 bg-gradient-to-br from-red-500 to-red-700 p-8 rounded-3xl shadow-lg border border-red-400 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-48 w-48 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-red-100 font-bold mb-2 text-xs uppercase tracking-widest flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-white"></div>
                    Total Penduduk Miskin
                </h3>
                <p class="text-5xl font-black text-white mt-4">{{ number_format($totalMiskin, 0, ',', '.') }} <span class="text-xl font-medium text-red-200">Jiwa</span></p>
            </div>
        </div>

        <!-- Card 2 & 3: Indeks Kemiskinan -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden flex flex-col justify-center">
            <h3 class="text-slate-400 font-bold mb-1 text-[10px] uppercase tracking-widest">Indeks Kedalaman (P1)</h3>
            <p class="text-3xl font-black text-slate-800">{{ number_format($rataKedalaman, 2) }}</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-red-400 to-transparent"></div>
        </div>
        
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden flex flex-col justify-center">
            <h3 class="text-slate-400 font-bold mb-1 text-[10px] uppercase tracking-widest">Indeks Keparahan (P2)</h3>
            <p class="text-3xl font-black text-slate-800">{{ number_format($rataKeparahan, 2) }}</p>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-slate-800 to-transparent"></div>
        </div>

        <!-- Card 4: Bantuan Sosial -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 p-6 rounded-3xl shadow-lg border border-emerald-400 relative overflow-hidden flex flex-col justify-center group">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-emerald-100 font-bold mb-1 text-[10px] uppercase tracking-widest">Intervensi Bantuan</h3>
                <p class="text-2xl font-black text-white">{{ number_format($totalPenerimaBansos, 0, ',', '.') }} <span class="text-sm font-normal text-emerald-200">KK</span></p>
                <div class="mt-2 text-xs font-bold text-emerald-200 bg-black/10 inline-block px-2 py-1 rounded-lg">Rp {{ number_format($totalAnggaranBansos / 1000000000, 2, ',', '.') }} M</div>
            </div>
        </div>
    </div>

    <!-- Main Map Section -->
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200 border border-slate-100 overflow-hidden flex flex-col relative group">
        <!-- Floating Glass Panel -->
        <div class="absolute top-6 left-6 z-[1000] bg-white/80 backdrop-blur-xl px-6 py-5 rounded-2xl shadow-2xl border border-white pointer-events-none transition-transform group-hover:scale-105 origin-top-left">
            <div class="flex items-center gap-3 mb-1">
                <div class="w-3 h-3 rounded-full bg-red-500 animate-ping"></div>
                <h2 class="text-xl font-black text-slate-800 tracking-tight">Peta Spasial</h2>
            </div>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Tahun: <span class="text-red-500">{{ $maxTahunKesejahteraan ?? 'N/A' }}</span></p>
        </div>
        
        <!-- Legend Overlay -->
        <div class="absolute bottom-6 left-6 z-[1000] bg-white/90 backdrop-blur-md px-4 py-3 rounded-xl shadow-lg border border-slate-100 pointer-events-none">
            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Skala Kepadatan Kemiskinan</h4>
            <div class="flex items-center gap-1">
                <div class="w-8 h-3 rounded-l-full" style="background-color: #fee5d9"></div>
                <div class="w-8 h-3" style="background-color: #fcae91"></div>
                <div class="w-8 h-3" style="background-color: #fb6a4a"></div>
                <div class="w-8 h-3" style="background-color: #de2d26"></div>
                <div class="w-8 h-3 rounded-r-full" style="background-color: #a50f15"></div>
            </div>
            <div class="flex justify-between text-[9px] font-bold text-slate-400 mt-1">
                <span>Rendah</span>
                <span>Tinggi</span>
            </div>
        </div>

        <div id="map" class="w-full h-[650px] z-0"></div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Trend Chart (Takes 7 cols) -->
        <div class="lg:col-span-7 bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-slate-100 flex flex-col relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-32 bg-red-50 rounded-bl-full -z-0"></div>
            <h2 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-2 relative z-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                Tren Penduduk Miskin
            </h2>
            <div class="relative flex-1 w-full min-h-[300px] z-10">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Bar Chart: Perbandingan Kedalaman vs Keparahan (Takes 5 cols) -->
        <div class="lg:col-span-5 bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-slate-100 flex flex-col relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-32 bg-slate-50 rounded-bl-full -z-0"></div>
            <h2 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-2 relative z-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-800" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                Komparasi Indeks (P1 & P2)
            </h2>
            <div class="relative flex-1 w-full min-h-[300px] z-10">
                <canvas id="indexChart"></canvas>
            </div>
        </div>

    </div>
    
    <div class="flex justify-center md:justify-end mt-4">
        <a href="{{ route('export.csv') }}" class="group relative inline-flex items-center justify-center gap-3 px-8 py-4 bg-slate-900 text-white font-bold rounded-2xl overflow-hidden transition-all hover:bg-slate-800 shadow-xl hover:shadow-2xl hover:-translate-y-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
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

        // Add Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
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
                        
                        let color = '#fee5d9';
                        if (value > 20000) color = '#a50f15';
                        else if (value > 15000) color = '#de2d26';
                        else if (value > 10000) color = '#fb6a4a';
                        else if (value > 5000) color = '#fcae91';

                        return {
                            color: '#ffffff',
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
                            <div class="text-sm p-1">
                                <b class="text-lg text-slate-800">Kecamatan ${name}</b><br/>
                                <div class="mt-3">
                                    <span class="text-xs text-slate-400 uppercase tracking-wide">Individu Sangat Miskin:</span><br/>
                                    <span class="font-bold text-primary text-base">${new Intl.NumberFormat('id-ID').format(valueMiskin)} Jiwa</span>
                                </div>
                                <div class="mt-2 pt-2 border-t border-slate-200">
                                    <span class="text-xs text-slate-400 uppercase tracking-wide">Penerima Bantuan:</span><br/>
                                    <span class="font-bold text-emerald-600 text-base">${new Intl.NumberFormat('id-ID').format(jumlahPenerima)} KK</span>
                                </div>
                                <div class="mt-1">
                                    <span class="text-xs text-slate-400 uppercase tracking-wide">Bansos Pangan (Per Bulan):</span><br/>
                                    <span class="font-bold text-emerald-600 text-base">Rp ${new Intl.NumberFormat('id-ID').format(anggaranRupiah)}</span>
                                </div>
                            </div>
                        `;
                        layer.bindPopup(tooltipContent);
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
                    borderColor: 'rgba(220, 38, 38, 1)',
                    backgroundColor: 'rgba(220, 38, 38, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
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
                        backgroundColor: 'rgba(220, 38, 38, 0.8)',
                    },
                    {
                        label: 'Indeks Keparahan',
                        data: @json($chartDataKeparahan),
                        backgroundColor: 'rgba(15, 23, 42, 0.8)',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endpush
