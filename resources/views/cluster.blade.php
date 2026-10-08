@extends('layouts.app')

@section('title', 'Analisa Klaster Spasial')

@section('content')
<div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-14">

    <!-- ============ Page header ============ -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <div class="section-label mb-4">
                <span aria-hidden="true">[</span>Machine Learning<span aria-hidden="true">]</span>
            </div>
            <h1 class="display-lg text-ink">Klaster Spasial</h1>
            <p class="body-md text-body mt-3 max-w-2xl">
                Pengelompokan wilayah berdasarkan kedekatan karakter kemiskinan ekstrem menggunakan algoritma <strong class="text-ink font-medium">K-Means Clustering</strong> (Tahun {{ $maxTahun }}). @include('partials.p3ke-info')
            </p>
        </div>

        <div class="feature-card inline-flex items-center gap-4 py-4 px-6">
            <div class="caption-uppercase text-muted">Iterasi Mesin</div>
            <div class="flex items-baseline gap-1">
                <span class="display-sm text-ink tabular">{{ $iterations }}</span>
                <span class="code text-primary">x</span>
            </div>
            <span class="badge-pill bg-success text-white">Converged</span>
        </div>
    </div>

    <!-- ============ Ringkasan prioritas ============ -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <!-- Tinggi -->
        <div class="feature-card border-l-2 border-l-error">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-2 h-2 rounded-full bg-error"></span>
                <span class="caption-uppercase text-error">Prioritas Tinggi</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="display-md text-ink tabular">{{ count($clustersGrouped['Tinggi']) }}</span>
                <span class="body-sm text-muted">Kecamatan</span>
            </div>
            <p class="body-sm text-body mt-2">Intervensi segera.</p>
        </div>

        <!-- Menengah -->
        <div class="feature-card border-l-2 border-l-primary">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-2 h-2 rounded-full bg-primary"></span>
                <span class="caption-uppercase text-primary-active">Prioritas Menengah</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="display-md text-ink tabular">{{ count($clustersGrouped['Menengah']) }}</span>
                <span class="body-sm text-muted">Kecamatan</span>
            </div>
            <p class="body-sm text-body mt-2">Pengawasan program.</p>
        </div>

        <!-- Rendah -->
        <div class="feature-card border-l-2 border-l-success">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-2 h-2 rounded-full bg-success"></span>
                <span class="caption-uppercase text-success">Prioritas Rendah</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="display-md text-ink tabular">{{ count($clustersGrouped['Rendah']) }}</span>
                <span class="body-sm text-muted">Kecamatan</span>
            </div>
            <p class="body-sm text-body mt-2">Pembinaan mandiri.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- ============ Scatter plot ============ -->
        <div class="lg:col-span-8 feature-card flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h2 class="title-sm text-ink">Peta Sebaran K-Means</h2>
                <span class="code text-muted">scatter</span>
            </div>
            <p class="caption-uppercase text-muted mb-6">Sumbu X: Kesejahteraan Terendah (Desil 1) — Sumbu Y: Penerima Bansos Pangan</p>
            <div class="relative flex-1 w-full min-h-[420px] bg-canvas-soft rounded-[8px] p-4">
                <canvas id="clusterChart"></canvas>
            </div>
        </div>

        <!-- ============ Tabel prioritas tinggi ============ -->
        <div class="lg:col-span-4 feature-card flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h2 class="title-sm text-ink">Target Intervensi Tinggi</h2>
                <span class="badge-pill bg-error text-white">{{ count($clustersGrouped['Tinggi']) }}</span>
            </div>

            <div class="overflow-y-auto max-h-[500px] pr-1 thin-scroll space-y-3">
                @if(count($clustersGrouped['Tinggi']) > 0)
                    @foreach($clustersGrouped['Tinggi'] as $point)
                    <div class="border border-hairline panel p-4 border-l-2 border-l-error">
                        <h4 class="title-sm text-ink mb-3">{{ $point['kecamatan'] }}</h4>
                        <div class="flex items-center gap-6">
                            <div>
                                <p class="caption-uppercase text-muted">Kesejahteraan Terendah (Desil 1)</p>
                                <p class="code text-error text-sm tabular">{{ number_format($point['x'], 0, ',', '.') }} <span class="text-muted">Jiwa</span></p>
                            </div>
                            <div>
                                <p class="caption-uppercase text-muted">Bansos Pangan</p>
                                <p class="code text-ink text-sm tabular">{{ number_format($point['y'], 0, ',', '.') }} <span class="text-muted">KK</span></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-12 border border-dashed border-hairline-strong rounded-[12px]">
                        <p class="body-sm text-muted">Tidak ada kecamatan di klaster ini.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('clusterChart').getContext('2d');
        const rawDatasets = @json($chartDatasets);

        new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: rawDatasets.map(ds => ({
                    label: ds.label,
                    data: ds.data,
                    backgroundColor: ds.backgroundColor,
                    borderColor: ds.backgroundColor,
                    pointRadius: 8,
                    pointHoverRadius: 10,
                }))
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
                        callbacks: {
                            label: function(context) {
                                let label = context.raw.kecamatan || '';
                                return label + ': (Kesejahteraan terendah: ' + context.parsed.x.toLocaleString('id-ID') + ', Bansos: ' + context.parsed.y.toLocaleString('id-ID') + ')';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Individu Kesejahteraan Terendah (Desil 1)',
                            font: { family: "'Inter', sans-serif", size: 12 },
                            color: '#807d72'
                        },
                        grid: { color: '#efeee8' },
                        ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#a09c92' }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Penerima Bansos (KK)',
                            font: { family: "'Inter', sans-serif", size: 12 },
                            color: '#807d72'
                        },
                        grid: { color: '#efeee8' },
                        ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#a09c92' }
                    }
                }
            }
        });
    });
</script>
@endpush