@extends('layouts.mobile')

@section('title', 'Konteks Makro & Benchmarking Regional')
@section('pagelabel', 'Makro')

@section('content')
<div class="w-full px-4 py-5 space-y-5">

    <!-- Header -->
    <div class="bg-white border-2 border-[#171717] p-5 shadow-[4px_4px_0px_0px_#171717]">
        <div class="inline-flex items-center gap-2 px-2 py-1 bg-[#171717] text-white text-[9px] font-mono font-bold tracking-widest uppercase mb-3 border border-[#171717]">
            <i data-lucide="globe-2" class="w-3 h-3" aria-hidden="true"></i> Regional Benchmarking
        </div>
        <h1 class="text-2xl font-serif font-black uppercase tracking-tight mb-2">Konteks Makro</h1>
        <p class="text-neutral-600 font-mono font-bold text-xs leading-relaxed">Posisi Banjarnegara dibanding kabupaten tetangga, Jawa Tengah, dan Nasional berdasarkan indikator makro BPS ({{ $ringkasan['rentang'] ?? '-' }}).</p>
    </div>

    @if(isset($error))
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] font-mono font-bold text-sm" role="alert">
        {{ $error }}
    </div>
    @else

    <!-- Ringkasan Posisi -->
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-[#171717] text-white p-3 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
            <h3 class="font-mono text-neutral-300 font-black mb-1 text-[8px] uppercase tracking-widest leading-tight">Indikator</h3>
            <p class="text-xl font-mono font-black">{{ $ringkasan['jml_indikator'] }}</p>
        </div>
        <div class="bg-white p-3 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
            <h3 class="text-neutral-500 font-black mb-1 text-[8px] uppercase tracking-widest font-mono leading-tight">Unggul</h3>
            <p class="text-xl font-mono font-black text-[#171717]">{{ $ringkasan['unggul'] }}</p>
        </div>
        <div class="bg-white p-3 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
            <h3 class="text-neutral-500 font-black mb-1 text-[8px] uppercase tracking-widest font-mono leading-tight">Tertinggal</h3>
            <p class="text-xl font-mono font-black text-[#171717]">{{ $ringkasan['tertinggal'] }}</p>
        </div>
    </div>
    <p class="text-[9px] font-mono font-bold text-neutral-500 leading-relaxed -mt-2">Unggul/tertinggal dihitung relatif terhadap Nasional (arah indikator diperhitungkan).</p>

    <!-- Kartu Benchmarking per Indikator -->
    <div class="space-y-3">
        <h2 class="text-base font-serif font-black text-[#171717] flex items-center gap-2 uppercase border-b-2 border-[#171717] pb-2">
            <i data-lucide="layout-grid" class="w-4 h-4" aria-hidden="true"></i>
            Posisi per Indikator
        </h2>

        @foreach($kartu as $k)
        @php
            $statusBadge = $k['vs_nasional'] === 'unggul'
                ? 'bg-[#171717] text-white'
                : ($k['vs_nasional'] === 'tertinggal' ? 'bg-[#e5e5d8] text-[#171717]' : 'bg-[#a3a398] text-[#171717]');
            $statusLabel = ['unggul' => 'Unggul', 'tertinggal' => 'Tertinggal', 'setara' => 'Setara', 'na' => 'N/A'][$k['vs_nasional']] ?? 'N/A';
        @endphp
        <div class="bg-white p-4 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
            <div class="flex items-start justify-between gap-2 mb-2">
                <div>
                    <span class="block text-[8px] font-mono font-bold uppercase tracking-widest text-neutral-500">{{ $k['kategori'] }}</span>
                    <h3 class="font-mono font-black text-[11px] uppercase tracking-tight leading-tight mt-0.5">{{ $k['indikator'] }}</h3>
                </div>
                <span class="flex-shrink-0 text-[8px] font-mono font-black uppercase tracking-wider px-2 py-1 border-2 border-[#171717] {{ $statusBadge }}">{{ $statusLabel }}</span>
            </div>

            <div class="flex items-baseline gap-2 mb-2">
                <span class="text-2xl font-mono font-black text-[#171717]">{{ number_format($k['nilai_fokus'], 2, ',', '.') }}</span>
                <span class="text-[9px] font-mono font-bold text-neutral-500">{{ $k['satuan'] }}</span>
            </div>

            <div class="grid grid-cols-3 gap-2 pt-2 border-t-2 border-[#171717]/20">
                <div>
                    <span class="block text-[8px] font-mono font-bold text-neutral-500 uppercase tracking-wider">Rank Kab.</span>
                    <span class="font-mono font-black text-[11px] text-[#171717]">#{{ $k['rank'] }}/{{ $k['total'] }}</span>
                </div>
                <div>
                    <span class="block text-[8px] font-mono font-bold text-neutral-500 uppercase tracking-wider">Jateng</span>
                    <span class="font-mono font-black text-[11px] text-[#171717]">{{ $k['nilai_jateng'] !== null ? number_format($k['nilai_jateng'], 2, ',', '.') : '–' }}</span>
                </div>
                <div>
                    <span class="block text-[8px] font-mono font-bold text-neutral-500 uppercase tracking-wider">Nasional</span>
                    <span class="font-mono font-black text-[11px] text-[#171717]">{{ $k['nilai_nasional'] !== null ? number_format($k['nilai_nasional'], 2, ',', '.') : '–' }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Analisis Tren & Perbandingan -->
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] space-y-4">
        <h2 class="text-base font-serif font-black text-[#171717] flex items-center gap-2 uppercase border-b-2 border-[#171717] pb-2">
            <i data-lucide="line-chart" class="w-4 h-4" aria-hidden="true"></i>
            Tren & Perbandingan
        </h2>

        <form method="GET" action="{{ route('makro') }}">
            <label for="indikator" class="block text-[9px] font-mono font-black uppercase tracking-widest text-neutral-500 mb-1">Pilih Indikator</label>
            <select name="indikator" id="indikator" onchange="this.form.submit()" class="w-full font-mono font-bold text-xs border-2 border-[#171717] bg-white px-3 py-2 shadow-[2px_2px_0px_0px_#171717] focus-visible:outline-none">
                @foreach($indikatorList as $ind)
                <option value="{{ $ind }}" @selected($ind === $terpilih)>{{ $ind }}</option>
                @endforeach
            </select>
        </form>

        <div>
            <h3 class="text-[9px] font-mono font-black uppercase tracking-widest text-neutral-500 mb-2">Tren {{ $chart['indikator'] }} ({{ $ringkasan['rentang'] }})</h3>
            <div class="h-56">
                <canvas id="trenChart"></canvas>
            </div>
        </div>
        <div>
            <h3 class="text-[9px] font-mono font-black uppercase tracking-widest text-neutral-500 mb-2">Perbandingan Antar-Wilayah ({{ $chart['bandingTahun'] }})</h3>
            <div class="h-64">
                <canvas id="bandingChart"></canvas>
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
                        pointRadius: 3,
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
                    legend: { labels: { font: { family: fontFamily, weight: 'bold', size: 10 }, boxWidth: 10 } },
                    tooltip: { callbacks: { label: (c) => `${c.dataset.label}: ${c.parsed.y ?? '–'} ${makroChart.satuan}` } },
                },
                scales: {
                    y: { grid: { color: gridColor }, ticks: { font: { family: fontFamily, size: 9 } } },
                    x: { grid: { display: false }, ticks: { font: { family: fontFamily, size: 9 } } },
                },
            },
        });
    }

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
                    x: { grid: { color: gridColor }, ticks: { font: { family: fontFamily, size: 9 } } },
                    y: { grid: { display: false }, ticks: { font: { family: fontFamily, weight: 'bold', size: 9 } } },
                },
            },
        });
    }
</script>
@endpush
@endif
@endsection
