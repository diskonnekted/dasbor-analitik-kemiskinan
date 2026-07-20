@extends('layouts.app')

@section('title', 'Konteks Makro & Benchmarking Regional')

@section('content')
<div class="flex-grow overflow-y-auto w-full py-8 px-6 sm:px-8 space-y-8 bg-[#f4f4f0] text-[#171717]">

    <!-- Hero Header -->
    <div class="bg-white border-2 border-[#171717] p-8 md:p-10 shadow-[4px_4px_0px_0px_#171717] flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#171717] text-white text-[10px] font-mono font-bold tracking-widest uppercase mb-4 border border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <i data-lucide="globe-2" class="w-3.5 h-3.5" aria-hidden="true"></i>
                Regional Benchmarking
            </div>
            <h1 class="text-3xl md:text-4xl font-serif font-black text-[#171717] uppercase tracking-tight mb-2">Konteks Makro</h1>
            <p class="text-neutral-600 font-mono font-bold text-xs md:text-sm leading-relaxed">Posisi Banjarnegara dibanding kabupaten tetangga, Jawa Tengah, dan Nasional berdasarkan indikator makro BPS (Tahun {{ $ringkasan['rentang'] ?? '-' }}).</p>
        </div>

        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-[#171717] font-mono font-black uppercase text-xs border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] hover:shadow-[4px_4px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">
            <span>Dasbor Utama</span>
            <i data-lucide="arrow-right" class="w-4 h-4" aria-hidden="true"></i>
        </a>
    </div>

    @if(isset($error))
    <div class="bg-white text-[#171717] p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] font-mono font-bold" role="alert">
        {{ $error }}
    </div>
    @else

    <!-- Ringkasan Posisi -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-[#171717] text-white p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
            <h3 class="font-mono text-neutral-300 font-black mb-2 text-[10px] uppercase tracking-widest">Indikator Dibandingkan</h3>
            <p class="text-3xl font-mono font-black">{{ $ringkasan['jml_indikator'] }} <span class="text-xs font-bold text-neutral-400">Indikator</span></p>
        </div>
        <div class="bg-white p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
            <h3 class="text-neutral-500 font-black mb-2 text-[10px] uppercase tracking-widest font-mono">Unggul dari Nasional</h3>
            <p class="text-3xl font-mono font-black text-[#171717]">{{ $ringkasan['unggul'] }} <span class="text-xs font-bold text-neutral-500">Indikator</span></p>
        </div>
        <div class="bg-white p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
            <h3 class="text-neutral-500 font-black mb-2 text-[10px] uppercase tracking-widest font-mono">Tertinggal dari Nasional</h3>
            <p class="text-3xl font-mono font-black text-[#171717]">{{ $ringkasan['tertinggal'] }} <span class="text-xs font-bold text-neutral-500">Indikator</span></p>
        </div>
    </div>

    <!-- Kartu Benchmarking per Indikator -->
    <div class="bg-white p-6 md:p-8 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <h2 class="text-lg font-serif font-black text-[#171717] mb-6 flex items-center gap-2 uppercase border-b-2 border-[#171717] pb-2">
            <i data-lucide="layout-grid" class="w-5 h-5" aria-hidden="true"></i>
            Posisi per Indikator (Tahun Terbaru)
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($kartu as $k)
            @php
                $statusBadge = $k['vs_nasional'] === 'unggul'
                    ? 'bg-[#171717] text-white'
                    : ($k['vs_nasional'] === 'tertinggal' ? 'bg-[#e5e5d8] text-[#171717]' : 'bg-[#a3a398] text-[#171717]');
                $statusLabel = ['unggul' => 'Unggul', 'tertinggal' => 'Tertinggal', 'setara' => 'Setara', 'na' => 'N/A'][$k['vs_nasional']] ?? 'N/A';
            @endphp
            <div class="bg-[#f4f4f0] p-5 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] flex flex-col">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div>
                        <span class="block text-[9px] font-mono font-bold uppercase tracking-widest text-neutral-500">{{ $k['kategori'] }}</span>
                        <h3 class="font-mono font-black text-[11px] uppercase tracking-tight leading-tight mt-0.5">{{ $k['indikator'] }}</h3>
                    </div>
                    <span class="flex-shrink-0 text-[9px] font-mono font-black uppercase tracking-wider px-2 py-1 border-2 border-[#171717] {{ $statusBadge }}">{{ $statusLabel }}</span>
                </div>

                <div class="flex items-baseline gap-2 mb-3">
                    <span class="text-3xl font-mono font-black text-[#171717]">{{ number_format($k['nilai_fokus'], 2, ',', '.') }}</span>
                    <span class="text-[10px] font-mono font-bold text-neutral-500">{{ $k['satuan'] }}</span>
                </div>

                <div class="mt-auto pt-3 border-t-2 border-[#171717]/20 space-y-1.5">
                    <div class="flex items-center justify-between text-[10px] font-mono font-bold">
                        <span class="text-neutral-500 uppercase tracking-wider">Peringkat Kab.</span>
                        <span class="font-black text-[#171717]">#{{ $k['rank'] }} / {{ $k['total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] font-mono font-bold">
                        <span class="text-neutral-500 uppercase tracking-wider">Jawa Tengah</span>
                        <span class="font-black text-[#171717]">{{ $k['nilai_jateng'] !== null ? number_format($k['nilai_jateng'], 2, ',', '.') : '–' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] font-mono font-bold">
                        <span class="text-neutral-500 uppercase tracking-wider">Nasional</span>
                        <span class="font-black text-[#171717]">{{ $k['nilai_nasional'] !== null ? number_format($k['nilai_nasional'], 2, ',', '.') : '–' }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-[10px] font-mono font-bold text-neutral-500 mt-5 leading-relaxed">Status "Unggul/Tertinggal" dihitung relatif terhadap Nasional dengan arah indikator diperhitungkan (mis. kemiskinan & Gini makin rendah makin baik). Peringkat kabupaten hanya membandingkan 5 kabupaten (tanpa Jateng & Nasional).</p>
    </div>

    <!-- Analisis Tren & Perbandingan Indikator Terpilih -->
    <div class="bg-white p-6 md:p-8 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 border-b-2 border-[#171717] pb-4">
            <h2 class="text-lg font-serif font-black text-[#171717] flex items-center gap-2 uppercase">
                <i data-lucide="line-chart" class="w-5 h-5" aria-hidden="true"></i>
                Tren & Perbandingan
            </h2>
            <form method="GET" action="{{ route('makro') }}" class="flex items-center gap-2">
                <label for="indikator" class="text-[10px] font-mono font-black uppercase tracking-widest text-neutral-500">Indikator</label>
                <select name="indikator" id="indikator" onchange="this.form.submit()" class="font-mono font-bold text-xs border-2 border-[#171717] bg-white px-3 py-2 shadow-[2px_2px_0px_0px_#171717] focus-visible:outline-none">
                    @foreach($indikatorList as $ind)
                    <option value="{{ $ind }}" @selected($ind === $terpilih)>{{ $ind }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div>
                <h3 class="text-[10px] font-mono font-black uppercase tracking-widest text-neutral-500 mb-3">Tren {{ $chart['indikator'] }} ({{ $ringkasan['rentang'] }})</h3>
                <div class="h-72">
                    <canvas id="trenChart"></canvas>
                </div>
            </div>
            <div>
                <h3 class="text-[10px] font-mono font-black uppercase tracking-widest text-neutral-500 mb-3">Perbandingan Antar-Wilayah ({{ $chart['bandingTahun'] }})</h3>
                <div class="h-72">
                    <canvas id="bandingChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @endif
</div>

@if(!isset($error))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const makroChart = @json($chart);

    const gridColor = 'rgba(23,23,23,0.08)';
    const fontFamily = "'JetBrains Mono', monospace";

    // --- Tren Chart (Banjarnegara vs Jateng vs Nasional) ---
    const trenCtx = document.getElementById('trenChart');
    if (trenCtx) {
        new Chart(trenCtx, {
            type: 'line',
            data: {
                labels: makroChart.labels,
                datasets: [
                    {
                        label: 'Banjarnegara',
                        data: makroChart.trenFokus,
                        borderColor: '#171717',
                        backgroundColor: 'rgba(23,23,23,0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        spanGaps: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#171717',
                    },
                    {
                        label: 'Jawa Tengah',
                        data: makroChart.trenJateng,
                        borderColor: '#737373',
                        borderWidth: 2,
                        borderDash: [6, 4],
                        tension: 0.3,
                        spanGaps: true,
                        pointRadius: 2,
                    },
                    {
                        label: 'Nasional',
                        data: makroChart.trenNasional,
                        borderColor: '#a3a398',
                        borderWidth: 2,
                        borderDash: [2, 3],
                        tension: 0.3,
                        spanGaps: true,
                        pointRadius: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { font: { family: fontFamily, weight: 'bold' }, boxWidth: 12 } },
                    tooltip: { callbacks: { label: (c) => `${c.dataset.label}: ${c.parsed.y ?? '–'} ${makroChart.satuan}` } },
                },
                scales: {
                    y: { grid: { color: gridColor }, ticks: { font: { family: fontFamily } } },
                    x: { grid: { display: false }, ticks: { font: { family: fontFamily } } },
                },
            },
        });
    }

    // --- Perbandingan Antar-Wilayah (bar) ---
    const bandingCtx = document.getElementById('bandingChart');
    if (bandingCtx) {
        const colors = makroChart.bandingLabels.map((w) => w === 'Banjarnegara' ? '#171717' : '#c4c4b8');
        new Chart(bandingCtx, {
            type: 'bar',
            data: {
                labels: makroChart.bandingLabels,
                datasets: [{
                    label: makroChart.indikator,
                    data: makroChart.bandingNilai,
                    backgroundColor: colors,
                    borderColor: '#171717',
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (c) => `${c.parsed.x} ${makroChart.satuan}` } },
                },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { font: { family: fontFamily } } },
                    y: { grid: { display: false }, ticks: { font: { family: fontFamily, weight: 'bold' } } },
                },
            },
        });
    }
</script>
@endpush
@endif
@endsection
