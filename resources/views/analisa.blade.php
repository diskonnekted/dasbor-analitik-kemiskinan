@extends('layouts.app')

@section('title', 'Diagnostik & Korelasi')

@section('content')
<div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-14">

    <!-- ============ Page header ============ -->
    <div class="mb-12">
        <div class="section-label mb-4">
            <span aria-hidden="true">[</span>Diagnostik<span aria-hidden="true">]</span>
        </div>
        <h1 class="display-lg text-ink">Korelasi Faktor–Kemiskinan</h1>
        <p class="body-md text-body mt-3 max-w-2xl">
            Membongkar pola tersembunyi: faktor infrastruktur, kesehatan, dan pendidikan yang paling berpengaruh terhadap tingkat kemiskinan ekstrem di Banjarnegara.
        </p>
    </div>

    @if(isset($error))
    <div class="feature-card border-l-2 border-l-error">
        <p class="body-sm text-error">{{ $error }}</p>
    </div>
    @else

    <!-- ============ Temuan kunci ============ -->
    @php $topFactor = $hasilKorelasi[0] ?? null; @endphp
    @if($topFactor)
    <div class="feature-card mb-6 flex flex-col md:flex-row items-start md:items-center gap-6 {{ $topFactor['korelasi'] < 0 ? 'border-l-2 border-l-success' : 'border-l-2 border-l-error' }}">
        <div class="flex-shrink-0 w-16 h-16 rounded-full border border-hairline flex items-center justify-center bg-canvas-soft">
            <span class="display-sm tabular {{ $topFactor['korelasi'] < 0 ? 'text-success' : 'text-error' }}">{{ number_format(abs($topFactor['korelasi']) * 100, 0) }}<span class="text-lg">%</span></span>
        </div>
        <div class="flex-grow">
            <div class="caption-uppercase text-muted mb-2">Temuan Kunci Paling Signifikan</div>
            <p class="body-md text-ink leading-snug">
                <span class="font-medium {{ $topFactor['korelasi'] < 0 ? 'text-success' : 'text-error' }}">“{{ $topFactor['indikator'] }}”</span>
                memiliki korelasi <strong class="font-medium">{{ strtolower($topFactor['kekuatan']) }}</strong> dengan kemiskinan ekstrem
                ({{ $topFactor['arah'] }}).
            </p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        <!-- ============ Ranking korelasi ============ -->
        <div class="xl:col-span-5 flex flex-col gap-6">
            <div class="feature-card flex-grow">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="title-sm text-ink">Peringkat Korelasi</h2>
                    <span class="caption-uppercase text-muted">Tahun {{ $tahunKemiskinan }}</span>
                </div>

                <div class="space-y-2">
                    @foreach($hasilKorelasi as $index => $row)
                    @php
                        $width = abs($row['korelasi']) * 100;
                        $isPos = $row['korelasi'] >= 0;
                    @endphp
                    <div class="list-row">
                        <span class="w-7 shrink-0 code {{ $index === 0 ? 'text-primary' : 'text-muted-soft' }}">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center justify-between gap-3 mb-1.5">
                                <h4 class="body-sm text-ink truncate">{{ $row['indikator'] }}</h4>
                                <span class="code text-xs tabular shrink-0 {{ $isPos ? 'text-error' : 'text-success' }}">{{ number_format($row['korelasi'], 2) }}</span>
                            </div>
                            <div class="w-full bg-surface-strong h-1 overflow-hidden">
                                <div class="h-full {{ $isPos ? 'bg-error' : 'bg-success' }}" style="width: {{ $width }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="body-xs text-muted-soft">{{ $row['kategori'] }}</span>
                                <span class="body-xs text-muted">{{ $row['kekuatan'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="feature-card bg-surface-strong">
                <h4 class="title-sm text-ink mb-4">Cara Membaca Matriks</h4>
                <ul class="space-y-3 body-sm text-body">
                    <li class="flex items-start gap-2"><span class="w-2.5 h-2.5 rounded-full bg-success mt-1.5 shrink-0"></span><span><strong class="text-success font-medium">Hijau (−)</strong> berarti solusi. Jika nilai ini naik, kemiskinan turun.</span></li>
                    <li class="flex items-start gap-2"><span class="w-2.5 h-2.5 rounded-full bg-error mt-1.5 shrink-0"></span><span><strong class="text-error font-medium">Merah (+)</strong> berarti risiko. Jika nilai ini naik, kemiskinan ikut naik.</span></li>
                    <li class="flex items-start gap-2"><span class="w-2.5 h-2.5 rounded-full bg-primary mt-1.5 shrink-0"></span><span><strong class="text-ink font-medium">Skor mendekati ±1</strong> berarti korelasi sangat kuat.</span></li>
                </ul>
            </div>
        </div>

        <!-- ============ Peta sebaran ============ -->
        <div class="xl:col-span-7 feature-card">
            <div class="flex items-center justify-between mb-6">
                <h2 class="title-sm text-ink">Peta Sebaran per Kecamatan</h2>
                <span class="code text-muted">scatter</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($hasilKorelasi as $index => $row)
                @php $isPos = $row['korelasi'] >= 0; @endphp
                <div class="border border-hairline panel flex flex-col">
                    <div class="flex justify-between items-start gap-3 mb-3 px-1">
                        <div class="min-w-0">
                            <h3 class="body-sm text-ink leading-tight">{{ $row['indikator'] }}</h3>
                            <span class="body-xs text-muted-soft">{{ $row['kategori'] }}</span>
                        </div>
                        <span class="badge-pill shrink-0 {{ $isPos ? 'bg-error text-white' : 'bg-success text-white' }}">r = {{ number_format($row['korelasi'], 2) }}</span>
                    </div>
                    <div class="relative w-full h-[220px] bg-canvas-soft rounded-[8px] p-2">
                        <canvas id="scatterChart_{{ $index }}"></canvas>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scatterData = @json($scatterData);
        const hasilKorelasi = @json($hasilKorelasi);

        hasilKorelasi.forEach((row, index) => {
            const dataPoints = scatterData[row.indikator].map(pt => ({
                x: pt.x,
                y: pt.y,
                kecamatan: pt.kecamatan
            }));

            const ctx = document.getElementById('scatterChart_' + index).getContext('2d');
            const isPos = row.korelasi >= 0;
            const color = isPos ? '#cf2d56' : '#1f8a65';

            new Chart(ctx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: row.indikator,
                        data: dataPoints,
                        backgroundColor: isPos ? 'rgba(207, 45, 86, 0.5)' : 'rgba(31, 138, 101, 0.5)',
                        borderColor: color,
                        pointRadius: 5,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.raw.kecamatan + ' (X: ' + context.raw.x.toLocaleString('id-ID') + ', Y: ' + context.raw.y.toLocaleString('id-ID') + ')';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: { display: true, text: row.indikator, font: { size: 10, family: "'Inter', sans-serif" }, color: '#807d72' },
                            grid: { color: '#efeee8' },
                            ticks: { font: { size: 10, family: "'Inter', sans-serif" }, color: '#a09c92' }
                        },
                        y: {
                            title: { display: true, text: 'Penduduk Miskin', font: { size: 10, family: "'Inter', sans-serif" }, color: '#807d72' },
                            grid: { color: '#efeee8' },
                            ticks: { font: { size: 10, family: "'Inter', sans-serif" }, color: '#a09c92' }
                        }
                    }
                }
            });
        });
    });
</script>
@endpush