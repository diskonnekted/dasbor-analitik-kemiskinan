@extends('layouts.app')

@section('title', 'Analisa Klaster Spasial')

@section('content')
<div class="flex-grow overflow-y-auto w-full py-8 px-6 sm:px-8 space-y-8 bg-[#f4f4f0] text-[#171717]">
    
    <!-- Hero Header Section -->
    <div class="relative bg-white border-2 border-[#171717] p-8 md:p-10 shadow-[4px_4px_0px_0px_#171717] flex flex-col xl:flex-row xl:items-center justify-between gap-8">
        
        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#171717] text-white text-xs font-mono font-bold tracking-widest uppercase mb-4 border border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <i data-lucide="brain" class="w-4 h-4"></i>
                Machine Learning Module
            </div>
            <h1 class="text-3xl md:text-4xl font-serif font-black text-[#171717] uppercase tracking-tight mb-2">Analisa Klaster Spasial</h1>
            <p class="text-neutral-600 font-mono font-bold text-sm">Pengelompokan cerdas (*Smart Grouping*) wilayah berdasarkan kedekatan karakter kemiskinan ekstrem menggunakan algoritma <b>K-Means Clustering</b> (Tahun {{ $maxTahun }}).</p>
        </div>
        
        <div class="relative z-10 flex flex-col items-center justify-center bg-white p-5 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] min-w-[200px]">
            <span class="text-[#171717] text-xs font-mono font-black uppercase tracking-widest mb-1">Iterasi Mesin</span>
            <div class="flex items-baseline gap-1">
                <span class="text-5xl font-mono font-black text-[#171717]">{{ $iterations }}</span>
                <span class="text-lg font-black text-neutral-500">x</span>
            </div>
            <div class="mt-2 text-[10px] text-white font-mono font-bold bg-[#171717] px-2 py-1 border border-[#171717] shadow-[2px_2px_0px_0px_#f4f4f0]">Optimal Converged</div>
        </div>
    </div>

    <!-- Overview Klaster -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Tinggi -->
        <div class="bg-[#171717] text-white p-8 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="font-mono text-neutral-300 font-black mb-1 text-xs uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-white"></span> Prioritas Tinggi
                </h3>
                <p class="text-neutral-400 text-xs font-mono font-bold mb-4 uppercase">Intervensi Segera</p>
                <p class="text-4xl font-serif font-black text-white">{{ count($clustersGrouped['Tinggi']) }} <span class="text-lg font-bold text-neutral-300">Kecamatan</span></p>
            </div>
        </div>

        <!-- Menengah -->
        <div class="bg-white text-[#171717] p-8 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="font-mono text-neutral-600 font-black mb-1 text-xs uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-[#a3a398] border border-[#171717]"></span> Prioritas Menengah
                </h3>
                <p class="text-neutral-500 text-xs font-mono font-bold mb-4 uppercase">Pengawasan Program</p>
                <p class="text-4xl font-serif font-black text-[#171717]">{{ count($clustersGrouped['Menengah']) }} <span class="text-lg font-bold text-neutral-500">Kecamatan</span></p>
            </div>
        </div>

        <!-- Rendah -->
        <div class="bg-[#e5e5d8] text-[#171717] p-8 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="font-mono text-[#171717] font-black mb-1 text-xs uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-white border border-[#171717]"></span> Prioritas Rendah
                </h3>
                <p class="text-neutral-600 text-xs font-mono font-bold mb-4 uppercase">Pembinaan Mandiri</p>
                <p class="text-4xl font-serif font-black text-[#171717]">{{ count($clustersGrouped['Rendah']) }} <span class="text-lg font-bold text-neutral-600 font-sans">Kecamatan</span></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Scatter Plot Chart -->
        <div class="lg:col-span-8 bg-white p-8 md:p-10 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col relative">
            <div class="relative z-10">
                <h2 class="text-xl font-serif font-black text-[#171717] mb-2 flex items-center gap-3 uppercase">
                    <i data-lucide="scatter-chart" class="w-6 h-6 text-[#171717]"></i>
                    Peta Sebaran K-Means
                </h2>
                <p class="text-neutral-600 text-xs font-mono font-black uppercase tracking-widest mb-8">Sumbu X: Kemiskinan Ekstrem | Sumbu Y: Penerima Bansos Pangan</p>
            </div>
            <div class="relative flex-1 w-full min-h-[400px] border border-[#171717] p-4 bg-white">
                <canvas id="clusterChart"></canvas>
            </div>
        </div>

        <!-- Tabel Prioritas -->
        <div class="lg:col-span-4 bg-white p-8 md:p-10 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col">
            <h2 class="text-lg font-serif font-black text-[#171717] mb-6 flex items-center gap-2 uppercase border-b-2 border-[#171717] pb-2">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-[#171717]"></i>
                Target Intervensi Tinggi
            </h2>
            <div class="overflow-y-auto max-h-[450px] pr-2 space-y-4">
                @if(count($clustersGrouped['Tinggi']) > 0)
                    @foreach($clustersGrouped['Tinggi'] as $point)
                    <div class="relative bg-white p-5 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">
                        <div class="absolute top-0 left-0 w-2 h-full bg-[#171717]"></div>
                        <h4 class="font-serif font-black text-[#171717] text-lg ml-2 uppercase">{{ $point['kecamatan'] }}</h4>
                        <div class="mt-3 ml-2 flex items-center gap-6">
                            <div>
                                <p class="text-neutral-500 text-[10px] font-mono font-bold uppercase tracking-widest">Sangat Miskin</p>
                                <p class="font-mono font-black text-[#171717] text-lg">{{ number_format($point['x'], 0, ',', '.') }} <span class="text-xs font-bold text-neutral-500">Jiwa</span></p>
                            </div>
                            <div>
                                <p class="text-neutral-500 text-[10px] font-mono font-bold uppercase tracking-widest">Bansos Pangan</p>
                                <p class="font-mono font-black text-neutral-700 text-lg">{{ number_format($point['y'], 0, ',', '.') }} <span class="text-xs font-bold text-neutral-500">KK</span></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-12 bg-neutral-100 border-2 border-[#171717] border-dashed">
                        <i data-lucide="check-circle" class="h-10 w-10 text-neutral-400 mx-auto mb-3"></i>
                        <p class="text-neutral-600 font-mono font-bold text-xs uppercase">Tidak ada kecamatan di klaster ini.</p>
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

        // Map cluster labels to monochrome colors
        const colors = {
            'Prioritas Tinggi': '#171717',     // Graphite
            'Prioritas Menengah': '#a3a398',   // Medium Gray
            'Prioritas Rendah': '#e5e5d8'       // Light Gray
        };

        new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: rawDatasets.map(ds => ({
                    label: ds.label,
                    data: ds.data,
                    backgroundColor: colors[ds.label] || '#171717',
                    borderColor: '#171717',
                    borderWidth: 1.5,
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
                            font: { family: 'Inter', weight: 'bold' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.raw.kecamatan || '';
                                return label + ': (Miskin: ' + context.parsed.x.toLocaleString('id-ID') + ', Bansos: ' + context.parsed.y.toLocaleString('id-ID') + ')';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Individu Sangat Miskin (Desil 1)',
                            font: { family: 'Inter', weight: 'bold' },
                            color: '#171717'
                        },
                        grid: { color: '#e5e5d8' },
                        ticks: { font: { family: 'Inter' }, color: '#171717' }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Penerima Bansos (KK)',
                            font: { family: 'Inter', weight: 'bold' },
                            color: '#171717'
                        },
                        grid: { color: '#e5e5d8' },
                        ticks: { font: { family: 'Inter' }, color: '#171717' }
                    }
                }
            }
        });
    });
</script>
@endpush
