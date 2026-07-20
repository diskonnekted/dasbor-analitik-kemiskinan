@extends('layouts.mobile')

@section('title', 'Analisa Klaster Spasial')
@section('pagelabel', 'Klaster')

@section('content')
<div class="w-full px-4 py-5 space-y-5">

    <!-- Header -->
    <div class="bg-white border-2 border-[#171717] p-5 shadow-[4px_4px_0px_0px_#171717]">
        <div class="inline-flex items-center gap-2 px-2 py-1 bg-[#171717] text-white text-[9px] font-mono font-bold tracking-widest uppercase mb-3 border border-[#171717]">
            <i data-lucide="brain" class="w-3 h-3" aria-hidden="true"></i> Machine Learning
        </div>
        <h1 class="text-2xl font-serif font-black uppercase tracking-tight mb-2">Analisa Klaster</h1>
        <p class="text-neutral-600 font-mono font-bold text-xs leading-relaxed">Pengelompokan wilayah dengan <b>K-Means Clustering</b> (Tahun {{ $maxTahun }}). Konvergen dalam {{ $iterations }}x iterasi.</p>
    </div>

    <!-- Overview -->
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-[#171717] text-white p-3 border-2 border-[#171717] shadow-[3px_3px_0px_0px_#171717]">
            <span class="w-2 h-2 bg-white block mb-2"></span>
            <p class="text-[9px] font-mono font-black uppercase tracking-widest text-neutral-300 mb-1">Tinggi</p>
            <p class="text-2xl font-serif font-black leading-none">{{ count($clustersGrouped['Tinggi']) }}</p>
        </div>
        <div class="bg-white p-3 border-2 border-[#171717] shadow-[3px_3px_0px_0px_#171717]">
            <span class="w-2 h-2 bg-[#a3a398] border border-[#171717] block mb-2"></span>
            <p class="text-[9px] font-mono font-black uppercase tracking-widest text-neutral-600 mb-1">Menengah</p>
            <p class="text-2xl font-serif font-black leading-none">{{ count($clustersGrouped['Menengah']) }}</p>
        </div>
        <div class="bg-[#e5e5d8] p-3 border-2 border-[#171717] shadow-[3px_3px_0px_0px_#171717]">
            <span class="w-2 h-2 bg-white border border-[#171717] block mb-2"></span>
            <p class="text-[9px] font-mono font-black uppercase tracking-widest text-neutral-600 mb-1">Rendah</p>
            <p class="text-2xl font-serif font-black leading-none">{{ count($clustersGrouped['Rendah']) }}</p>
        </div>
    </div>

    <!-- Scatter -->
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <h2 class="text-base font-serif font-black uppercase mb-1 flex items-center gap-2">
            <i data-lucide="scatter-chart" class="w-5 h-5" aria-hidden="true"></i> Peta Sebaran
        </h2>
        <p class="text-neutral-600 text-[9px] font-mono font-black uppercase tracking-widest mb-4">X: Kemiskinan | Y: Bansos</p>
        <div class="relative w-full h-[300px] border border-[#171717] p-2"><canvas id="clusterChart"></canvas></div>
    </div>

    <!-- Target Intervensi -->
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <h2 class="text-base font-serif font-black uppercase mb-4 flex items-center gap-2 border-b-2 border-[#171717] pb-2">
            <i data-lucide="alert-triangle" class="w-5 h-5" aria-hidden="true"></i> Target Intervensi Tinggi
        </h2>
        <div class="space-y-3">
            @forelse($clustersGrouped['Tinggi'] as $point)
            <div class="relative p-4 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-[#171717]"></div>
                <h4 class="font-serif font-black text-base ml-2 uppercase">{{ $point['kecamatan'] }}</h4>
                <div class="mt-2 ml-2 flex items-center gap-6">
                    <div>
                        <p class="text-neutral-500 text-[9px] font-mono font-bold uppercase tracking-widest">Sangat Miskin</p>
                        <p class="font-mono font-black text-base">{{ number_format($point['x'], 0, ',', '.') }} <span class="text-[10px] text-neutral-500">Jiwa</span></p>
                    </div>
                    <div>
                        <p class="text-neutral-500 text-[9px] font-mono font-bold uppercase tracking-widest">Bansos</p>
                        <p class="font-mono font-black text-base">{{ number_format($point['y'], 0, ',', '.') }} <span class="text-[10px] text-neutral-500">KK</span></p>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10 bg-neutral-100 border-2 border-[#171717] border-dashed">
                <i data-lucide="check-circle" class="h-8 w-8 text-neutral-400 mx-auto mb-2" aria-hidden="true"></i>
                <p class="text-neutral-600 font-mono font-bold text-[10px] uppercase">Tidak ada kecamatan di klaster ini.</p>
            </div>
            @endforelse
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
        const colors = {
            'Prioritas Tinggi': '#171717',
            'Prioritas Menengah': '#a3a398',
            'Prioritas Rendah': '#e5e5d8'
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
                    pointRadius: 6,
                    pointHoverRadius: 8
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, padding: 12, font: { family: 'Inter', weight: 'bold', size: 10 } } },
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
                    x: { title: { display: true, text: 'Sangat Miskin', font: { family: 'Inter', weight: 'bold', size: 10 }, color: '#171717' }, grid: { color: '#e5e5d8' }, ticks: { font: { family: 'Inter', size: 9 }, color: '#171717' } },
                    y: { title: { display: true, text: 'Bansos (KK)', font: { family: 'Inter', weight: 'bold', size: 10 }, color: '#171717' }, grid: { color: '#e5e5d8' }, ticks: { font: { family: 'Inter', size: 9 }, color: '#171717' } }
                }
            }
        });
    });
</script>
@endpush
