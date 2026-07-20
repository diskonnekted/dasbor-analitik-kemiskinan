@extends('layouts.mobile')

@section('title', 'Prediksi Kemiskinan Masa Depan')
@section('pagelabel', 'Proyeksi')

@section('content')
<div class="w-full px-4 py-5 space-y-5">

    <!-- Header -->
    <div class="bg-white border-2 border-[#171717] p-5 shadow-[4px_4px_0px_0px_#171717]">
        <div class="inline-flex items-center gap-2 px-2 py-1 bg-[#171717] text-white text-[9px] font-mono font-bold tracking-widest uppercase mb-3 border border-[#171717]">
            <i data-lucide="trending-up" class="w-3 h-3" aria-hidden="true"></i> Statistical Forecasting
        </div>
        <h1 class="text-2xl font-serif font-black uppercase tracking-tight mb-2">Proyeksi Masa Depan</h1>
        <p class="text-neutral-600 font-mono font-bold text-xs leading-relaxed">Peramalan jumlah penduduk miskin 3 tahun ke depan berdasarkan tren {{ min($years) }} - {{ max($years) }}.</p>
    </div>

    <!-- Alert -->
    @if($slope > 0)
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex items-start gap-4" role="status">
        <div class="bg-[#171717] text-white p-2.5 border-2 border-[#171717] flex-shrink-0"><i data-lucide="alert-triangle" class="h-6 w-6" aria-hidden="true"></i></div>
        <div>
            <h3 class="font-serif font-black text-base uppercase mb-1">Tren Meningkat</h3>
            <p class="text-neutral-600 font-mono font-bold text-[11px] leading-relaxed">Model mendeteksi kenaikan linier. Kemiskinan diprediksi bertambah bila tanpa intervensi baru.</p>
        </div>
    </div>
    @else
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex items-start gap-4" role="status">
        <div class="bg-[#171717] text-white p-2.5 border-2 border-[#171717] flex-shrink-0"><i data-lucide="check" class="h-6 w-6" aria-hidden="true"></i></div>
        <div>
            <h3 class="font-serif font-black text-base uppercase mb-1">Tren Menurun</h3>
            <p class="text-neutral-600 font-mono font-bold text-[11px] leading-relaxed">Model mendeteksi penurunan linier stabil. Program pengentasan berjalan efektif.</p>
        </div>
    </div>
    @endif

    <!-- Tabel Proyeksi -->
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <h2 class="text-base font-serif font-black uppercase mb-4 flex items-center gap-2 border-b-2 border-[#171717] pb-2">
            <i data-lucide="table" class="w-5 h-5" aria-hidden="true"></i> Angka Proyeksi
        </h2>
        <div class="grid grid-cols-3 gap-2 mb-2 pb-2 border-b-2 border-[#171717]">
            <div class="text-[9px] font-mono font-black uppercase tracking-widest">Tahun</div>
            <div class="text-[9px] font-mono font-black uppercase tracking-widest text-center">Linear</div>
            <div class="text-[9px] font-mono font-black uppercase tracking-widest text-right">Exp. Smooth</div>
        </div>
        <div class="space-y-3">
            @foreach($futureYears as $idx => $fy)
            <div class="grid grid-cols-3 gap-2 items-center p-3 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <div class="bg-[#f4f4f0] px-2 py-1 border-2 border-[#171717] font-mono font-black text-xs text-center">{{ $fy }}</div>
                <div class="text-center font-mono font-black text-base">{{ number_format($predictionDataLR[$idx], 0, ',', '.') }}</div>
                <div class="text-right font-mono font-black text-base">{{ number_format($predictionDataDES[$idx], 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Chart -->
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <h2 class="text-base font-serif font-black uppercase mb-1 flex items-center gap-2">
            <i data-lucide="line-chart" class="w-5 h-5" aria-hidden="true"></i> Grafik Peramalan
        </h2>
        <p class="text-neutral-500 font-mono font-bold text-[9px] uppercase tracking-widest mb-4">Regresi vs Exp. Smoothing</p>
        <div class="relative w-full h-[300px] border border-[#171717] p-2"><canvas id="forecastChart"></canvas></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('forecastChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($allLabels),
                datasets: [
                    { label: 'Aktual', data: @json($chartActual), borderColor: '#171717', backgroundColor: '#171717', borderWidth: 3, pointRadius: 4, pointBackgroundColor: '#171717', fill: false, tension: 0, order: 1 },
                    { label: 'Linear Reg.', data: @json($chartPredictionLR), borderColor: '#52524a', backgroundColor: '#52524a', borderWidth: 2, borderDash: [5, 5], pointRadius: 4, pointBackgroundColor: '#fff', pointBorderColor: '#52524a', pointBorderWidth: 2, fill: false, tension: 0, order: 2 },
                    { label: 'Exp. Smooth', data: @json($chartPredictionDES), borderColor: '#a3a398', backgroundColor: '#a3a398', borderWidth: 2, borderDash: [2, 3], pointRadius: 4, pointBackgroundColor: '#fff', pointBorderColor: '#a3a398', pointBorderWidth: 2, fill: false, tension: 0, order: 3 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, padding: 10, font: { family: 'Inter', weight: 'bold', size: 9 } } },
                    tooltip: {
                        mode: 'index', intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) label += new Intl.NumberFormat('id-ID').format(context.parsed.y) + ' Jiwa';
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: false, grid: { color: '#e5e5d8' }, ticks: { font: { family: 'Inter', size: 9 }, color: '#171717' } },
                    x: { grid: { color: '#e5e5d8' }, ticks: { font: { family: 'Inter', size: 9 }, color: '#171717' } }
                }
            }
        });
    });
</script>
@endpush
