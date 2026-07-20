@extends('layouts.mobile')

@section('title', 'Dasbor Analitik Kemiskinan')
@section('pagelabel', 'Dasbor')

@section('content')
<div class="w-full px-4 py-5 space-y-5">

    <!-- Header -->
    <div class="bg-white border-2 border-[#171717] p-5 shadow-[4px_4px_0px_0px_#171717]">
        <div class="inline-flex items-center gap-2 px-2 py-1 bg-[#171717] text-white text-[9px] font-mono font-bold tracking-widest uppercase mb-3 border border-[#171717]">
            <span class="w-2 h-2 bg-white"></span> Spatial Analytics
        </div>
        <h1 class="text-2xl font-serif font-black uppercase tracking-tight mb-2">Dasbor Kemiskinan</h1>
        <p class="text-neutral-600 font-mono font-bold text-xs leading-relaxed">Pemetaan spasial distribusi kemiskinan ekstrem & bantuan sosial Kab. Banjarnegara.</p>

        <form method="GET" action="{{ route('dashboard') }}" class="mt-4">
            <label for="tahun-mobile" class="block text-[9px] font-mono font-black text-neutral-500 uppercase tracking-widest mb-1">Filter Tahun</label>
            <div class="relative border-2 border-[#171717] bg-white shadow-[2px_2px_0px_0px_#171717]">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"><i data-lucide="calendar" class="w-4 h-4" aria-hidden="true"></i></span>
                <select id="tahun-mobile" name="tahun" onchange="this.form.submit()" class="w-full appearance-none bg-transparent border-none focus:ring-0 py-3 pl-10 pr-8 font-mono font-black text-sm cursor-pointer">
                    <option value="">Semua Tahun (Terbaru)</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $tahunFilter == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Stats -->
    <div class="space-y-4">
        <div class="bg-[#171717] text-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
            <h3 class="font-mono text-neutral-300 font-black mb-2 text-[10px] uppercase tracking-widest flex items-center gap-2">
                <span class="w-2 h-2 bg-white"></span> Total Penduduk Miskin
            </h3>
            <p class="text-3xl font-serif font-black">{{ number_format($totalMiskin, 0, ',', '.') }} <span class="text-sm font-bold text-neutral-300">Jiwa</span></p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white p-4 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
                <h3 class="text-neutral-500 font-black mb-1 text-[9px] uppercase tracking-widest font-mono">Kedalaman (P1)</h3>
                <p class="text-2xl font-serif font-black">{{ number_format($rataKedalaman, 2) }}</p>
            </div>
            <div class="bg-white p-4 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
                <h3 class="text-neutral-500 font-black mb-1 text-[9px] uppercase tracking-widest font-mono">Keparahan (P2)</h3>
                <p class="text-2xl font-serif font-black">{{ number_format($rataKeparahan, 2) }}</p>
            </div>
        </div>

        <div class="bg-white p-4 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
            <h3 class="text-neutral-500 font-black mb-1 text-[9px] uppercase tracking-widest font-mono">Intervensi Bantuan</h3>
            <p class="text-2xl font-serif font-black">{{ number_format($totalPenerimaBansos, 0, ',', '.') }} <span class="text-xs font-bold text-neutral-500 font-sans">KK</span></p>
            <div class="mt-2 text-[11px] font-mono font-black text-white bg-[#171717] px-2 py-1 inline-block">Rp {{ number_format($totalAnggaranBansos / 1000000000, 2, ',', '.') }} M</div>
        </div>
    </div>

    <!-- Map -->
    <div class="bg-white border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b-2 border-[#171717]">
            <h2 class="text-base font-serif font-black uppercase tracking-tight flex items-center gap-2">
                <span class="w-3 h-3 bg-[#171717]"></span> Peta Spasial
            </h2>
            <span class="text-[9px] font-mono font-black uppercase text-neutral-500">Tahun {{ $maxTahunKesejahteraan ?? 'N/A' }}</span>
        </div>
        <div id="map" class="w-full h-[360px] z-0"></div>
        <div class="px-4 py-3 border-t-2 border-[#171717]">
            <h4 class="text-[9px] font-mono font-black uppercase tracking-widest mb-2">Kepadatan Penduduk Sangat Miskin</h4>
            <div class="flex items-center gap-1">
                <div class="flex-1 h-4 border border-[#171717]" style="background-color:#ffffff"></div>
                <div class="flex-1 h-4 border border-[#171717]" style="background-color:#e5e5d8"></div>
                <div class="flex-1 h-4 border border-[#171717]" style="background-color:#a3a398"></div>
                <div class="flex-1 h-4 border border-[#171717]" style="background-color:#52524a"></div>
                <div class="flex-1 h-4 border border-[#171717]" style="background-color:#171717"></div>
            </div>
            <div class="flex justify-between text-[9px] font-mono font-black mt-1 uppercase"><span>Rendah</span><span>Tinggi</span></div>
        </div>
    </div>

    <!-- Trend Chart -->
    <div class="bg-white border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] p-5">
        <h2 class="text-base font-serif font-black uppercase mb-4 flex items-center gap-2">
            <i data-lucide="trending-up" class="w-5 h-5" aria-hidden="true"></i> Tren Penduduk Miskin
        </h2>
        <div class="relative w-full h-[260px]"><canvas id="trendChart"></canvas></div>
    </div>

    <!-- Export -->
    <a href="{{ route('export.csv') }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-[#171717] text-white font-mono font-black text-sm border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <i data-lucide="download" class="w-5 h-5" aria-hidden="true"></i> Unduh Laporan (.csv)
    </a>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map').setView([-7.3946, 109.6965], 10);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            subdomains: 'abcd', maxZoom: 20
        }).addTo(map);

        const mapData = @json($kesejahteraanMap);
        const bansosData = @json($bansosMap);

        fetch('/geojson/peta_kecamatan.geojson')
            .then(r => r.json())
            .then(data => {
                const layer = L.geoJSON(data, {
                    style: function(feature) {
                        let name = feature.properties.Kecamatan ? feature.properties.Kecamatan.toUpperCase() : '';
                        let value = mapData[name] ? mapData[name].desil_1 : 0;
                        let color = '#ffffff';
                        if (value > 20000) color = '#171717';
                        else if (value > 15000) color = '#52524a';
                        else if (value > 10000) color = '#a3a398';
                        else if (value > 5000) color = '#e5e5d8';
                        return { color: '#171717', weight: 1.5, fillColor: color, fillOpacity: 0.8 };
                    },
                    onEachFeature: function(feature, lyr) {
                        let name = feature.properties.Kecamatan ? feature.properties.Kecamatan.toUpperCase() : '';
                        let vMiskin = mapData[name] ? mapData[name].desil_1 : 0;
                        let penerima = bansosData[name] ? bansosData[name].jumlah_penerima : 0;
                        lyr.bindPopup(`<div class="font-sans text-xs p-1"><b class="font-serif font-black uppercase block mb-1">Kec. ${name}</b>Sangat Miskin: <b>${new Intl.NumberFormat('id-ID').format(vMiskin)} Jiwa</b><br/>Penerima: <b>${new Intl.NumberFormat('id-ID').format(penerima)} KK</b></div>`, { className: 'brutal-popup' });
                    }
                }).addTo(map);
                map.fitBounds(layer.getBounds());
            })
            .catch(error => {
                console.error('Error loading GeoJSON:', error);
                const mapEl = document.getElementById('map');
                if (mapEl) {
                    const notice = document.createElement('div');
                    notice.setAttribute('role', 'alert');
                    notice.className = 'absolute inset-0 z-[1200] flex items-center justify-center bg-[#f4f4f0]/95 p-4';
                    notice.innerHTML = '<div class="bg-white border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] px-4 py-4 text-center"><p class="font-serif font-black uppercase mb-1">Peta Gagal Dimuat</p><p class="font-mono font-bold text-[10px] text-neutral-600">Coba muat ulang halaman.</p></div>';
                    mapEl.style.position = 'relative';
                    mapEl.appendChild(notice);
                }
            });

        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Penduduk Miskin',
                    data: @json($chartDataPenduduk),
                    borderColor: '#171717', backgroundColor: '#e5e5d8',
                    borderWidth: 3, fill: false, tension: 0,
                    pointBackgroundColor: '#171717', pointBorderColor: '#ffffff', pointBorderWidth: 2, pointRadius: 5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { labels: { font: { family: 'Inter', weight: 'bold' } } } },
                scales: {
                    x: { grid: { color: '#e5e5d8' }, ticks: { font: { family: 'Inter', weight: 'bold', size: 10 }, color: '#171717' } },
                    y: { beginAtZero: true, grid: { color: '#e5e5d8' }, ticks: { font: { family: 'Inter', size: 10 }, color: '#171717' } }
                }
            }
        });
    });
</script>
<style>
    .brutal-popup .leaflet-popup-content-wrapper { background:#fff!important; border:2px solid #171717!important; border-radius:0!important; box-shadow:4px 4px 0 0 #171717!important; }
    .brutal-popup .leaflet-popup-tip { border:1px solid #171717!important; background:#fff!important; }
</style>
@endpush
