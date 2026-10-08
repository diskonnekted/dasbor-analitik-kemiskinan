@extends('layouts.app')

@section('title', 'Prediksi Kemiskinan Masa Depan')

@section('content')
<div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-14">

    <!-- ============ Page header ============ -->
    <div class="mb-12">
        <div class="section-label mb-4">
            <span aria-hidden="true">[</span>Statistical Forecasting<span aria-hidden="true">]</span>
        </div>
        <h1 class="display-lg text-ink">Proyeksi Masa Depan</h1>
        <p class="body-md text-body mt-3 max-w-2xl">
            Peramalan kuantitatif jumlah penduduk miskin menggunakan <strong class="text-ink font-medium">Regresi Linear Sederhana</strong> dan <strong class="text-ink font-medium">Exponential Smoothing</strong>, berdasarkan tren data aktual tahun {{ min($years) }}–{{ max($years) }}.
        </p>
    </div>

    <!-- ============ Status tren ============ -->
    @if($slope > 0)
    <div class="feature-card mb-6 border-l-2 border-l-error">
        <div class="flex items-start gap-4">
            <span class="badge-pill bg-error text-white shrink-0 mt-0.5">Peringatan</span>
            <div>
                <h3 class="title-md text-ink">Tren Kemiskinan Meningkat</h3>
                <p class="body-sm text-body mt-1 max-w-4xl">Model matematis mendeteksi <strong class="text-ink font-medium">kenaikan linier</strong> dari waktu ke waktu. Proyeksi algoritma memprediksi jumlah penduduk miskin akan terus bertambah dalam 3 tahun ke depan tanpa intervensi kebijakan baru.</p>
            </div>
        </div>
    </div>
    @else
    <div class="feature-card mb-6 border-l-2 border-l-success">
        <div class="flex items-start gap-4">
            <span class="badge-pill bg-success text-white shrink-0 mt-0.5">Kabar Baik</span>
            <div>
                <h3 class="title-md text-ink">Tren Kemiskinan Menurun</h3>
                <p class="body-sm text-body mt-1 max-w-4xl">Model matematis mendeteksi <strong class="text-ink font-medium">penurunan linier</strong> yang stabil. Program pengentasan kemiskinan saat ini berada pada jalur yang efektif untuk 3 tahun ke depan.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- ============ Tabel proyeksi ============ -->
        <div class="lg:col-span-4 feature-card flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h2 class="title-sm text-ink">Angka Proyeksi</h2>
                <span class="code text-muted">dual</span>
            </div>

            <div class="flex-1 space-y-2">
                <!-- Header -->
                <div class="grid grid-cols-3 gap-2 pb-2 border-b border-hairline">
                    <div class="caption-uppercase text-muted pl-1">Tahun</div>
                    <div class="caption-uppercase text-error text-center">Lin. Reg.</div>
                    <div class="caption-uppercase text-primary-active text-right pr-1">Exp. Smooth</div>
                </div>

                @foreach($futureYears as $idx => $fy)
                <div class="list-row items-center">
                    <div class="w-auto shrink-0 pl-1">
                        <span class="code text-ink">{{ $fy }}</span>
                    </div>
                    <div class="flex-grow text-center">
                        <span class="code text-error tabular">{{ number_format($predictionDataLR[$idx], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex-grow text-right pr-1">
                        <span class="code text-primary-active tabular">{{ number_format($predictionDataDES[$idx], 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8 pt-6 border-t border-hairline">
                <h3 class="caption-uppercase text-muted mb-3">Metodologi (Duel Algoritma)</h3>
                <div class="space-y-3 body-sm text-body">
                    <p><span class="code text-error">Linear Regression</span> — menarik garis tren konstan dari historis tahun {{ min($years) }}–{{ max($years) }}.</p>
                    <p><span class="code text-primary-active">Exponential Smoothing</span> — model dinamis (Holt) yang memberi bobot lebih tinggi pada data terbaru.</p>
                </div>
            </div>
        </div>

        <!-- ============ Grafik ============ -->
        <div class="lg:col-span-8 feature-card flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="title-sm text-ink">Grafik Peramalan</h2>
                <span class="code text-muted">line</span>
            </div>
            <p class="caption-uppercase text-muted mb-6">Komparasi model regresi vs penghalusan eksponensial.</p>

            <div class="relative flex-1 w-full min-h-[420px] bg-canvas-soft rounded-[8px] p-4">
                <canvas id="forecastChart"></canvas>
            </div>
        </div>

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
                    {
                        label: 'Data Historis (Aktual)',
                        data: @json($chartActual),
                        borderColor: '#26251e',
                        backgroundColor: '#26251e',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: '#26251e',
                        fill: false,
                        tension: 0.1,
                        order: 1
                    },
                    {
                        label: 'Proyeksi (Linear Regression)',
                        data: @json($chartPredictionLR),
                        borderColor: '#cf2d56',
                        backgroundColor: '#cf2d56',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#cf2d56',
                        pointBorderWidth: 2,
                        fill: false,
                        tension: 0.1,
                        order: 2
                    },
                    {
                        label: 'Proyeksi (Exponential Smoothing)',
                        data: @json($chartPredictionDES),
                        borderColor: '#f54e00',
                        backgroundColor: '#f54e00',
                        borderWidth: 2,
                        borderDash: [2, 4],
                        pointRadius: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#f54e00',
                        pointBorderWidth: 2,
                        fill: false,
                        tension: 0.3,
                        order: 0
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
                            padding: 20,
                            font: { family: "'Inter', sans-serif", size: 12 }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID').format(context.parsed.y) + ' Jiwa';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: '#efeee8' },
                        ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#a09c92' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#a09c92' }
                    }
                }
            }
        });
    });
</script>
@endpush