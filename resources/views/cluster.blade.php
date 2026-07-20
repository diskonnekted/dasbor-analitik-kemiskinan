@extends('layouts.app')

@section('title', 'Analisa Klaster Spasial')

@section('content')
<div class="w-full mx-auto py-8 px-4 sm:px-8 lg:px-12 space-y-8 bg-[#f8fafc] min-h-screen">
    
    <!-- Hero Header Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-[2.5rem] p-8 md:p-12 shadow-2xl border border-slate-800 flex flex-col xl:flex-row xl:items-center justify-between gap-8">
        <!-- Decorative Glow -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-500/20 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-20 w-80 h-80 bg-blue-500/20 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-slate-300 text-xs font-bold tracking-widest uppercase mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                Machine Learning Module
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-2">Analisa <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-indigo-400">Klaster Spasial</span></h1>
            <p class="text-slate-400 text-lg">Pengelompokan cerdas (*Smart Grouping*) wilayah berdasarkan kedekatan karakter kemiskinan ekstrem menggunakan algoritma <b>K-Means Clustering</b> (Tahun {{ $maxTahun }}).</p>
        </div>
        
        <div class="relative z-10 flex flex-col items-center justify-center bg-white/5 p-6 rounded-3xl border border-white/10 backdrop-blur-md min-w-[200px]">
            <span class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Iterasi Mesin</span>
            <div class="flex items-baseline gap-1">
                <span class="text-5xl font-black text-white">{{ $iterations }}</span>
                <span class="text-lg font-bold text-purple-400">x</span>
            </div>
            <div class="mt-2 text-[10px] text-emerald-400 font-mono bg-emerald-400/10 px-2 py-1 rounded">Optimal Converged</div>
        </div>
    </div>

    <!-- Overview Klaster -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Tinggi -->
        <div class="bg-gradient-to-br from-red-500 to-red-700 p-8 rounded-3xl shadow-lg border border-red-400 relative overflow-hidden group">
            <div class="absolute -right-8 -top-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-48 w-48 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-red-100 font-bold mb-1 text-xs uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> Prioritas Tinggi
                </h3>
                <p class="text-red-200 text-xs mb-4">Intervensi Segera</p>
                <p class="text-5xl font-black text-white">{{ count($clustersGrouped['Tinggi']) }} <span class="text-lg font-medium text-red-200">Kecamatan</span></p>
            </div>
        </div>

        <!-- Menengah -->
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 p-8 rounded-3xl shadow-lg border border-amber-400 relative overflow-hidden group">
            <div class="absolute -right-8 -top-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-48 w-48 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-amber-100 font-bold mb-1 text-xs uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-white"></span> Prioritas Menengah
                </h3>
                <p class="text-amber-200 text-xs mb-4">Pengawasan Program</p>
                <p class="text-5xl font-black text-white">{{ count($clustersGrouped['Menengah']) }} <span class="text-lg font-medium text-amber-200">Kecamatan</span></p>
            </div>
        </div>

        <!-- Rendah -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 p-8 rounded-3xl shadow-lg border border-emerald-400 relative overflow-hidden group">
            <div class="absolute -right-8 -top-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-48 w-48 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-emerald-100 font-bold mb-1 text-xs uppercase tracking-widest flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-white"></span> Prioritas Rendah
                </h3>
                <p class="text-emerald-200 text-xs mb-4">Pembinaan Mandiri</p>
                <p class="text-5xl font-black text-white">{{ count($clustersGrouped['Rendah']) }} <span class="text-lg font-medium text-emerald-200">Kecamatan</span></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Scatter Plot Chart -->
        <div class="lg:col-span-8 bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-slate-100 flex flex-col relative overflow-hidden">
            <div class="absolute left-0 top-0 w-32 h-32 bg-indigo-50 rounded-br-full -z-0"></div>
            <div class="relative z-10">
                <h2 class="text-2xl font-black text-slate-800 mb-2 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" /></svg>
                    Peta Sebaran K-Means
                </h2>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-8">Sumbu X: Kemiskinan Ekstrem | Sumbu Y: Penerima Bansos Pangan</p>
            </div>
            <div class="relative flex-1 w-full min-h-[400px] z-10 bg-slate-50/50 rounded-2xl p-4 border border-slate-50">
                <canvas id="clusterChart"></canvas>
            </div>
        </div>

        <!-- Tabel Prioritas -->
        <div class="lg:col-span-4 bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
            <h2 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                Target Intervensi Tinggi
            </h2>
            <div class="overflow-y-auto max-h-[450px] pr-2 space-y-4 custom-scrollbar">
                @if(count($clustersGrouped['Tinggi']) > 0)
                    @foreach($clustersGrouped['Tinggi'] as $point)
                    <div class="group relative bg-white p-5 rounded-2xl border border-red-100 shadow-sm hover:shadow-md hover:border-red-300 transition-all">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-red-500 rounded-l-2xl"></div>
                        <h4 class="font-black text-slate-800 text-lg ml-2">{{ $point['kecamatan'] }}</h4>
                        <div class="mt-3 ml-2 flex items-center gap-6">
                            <div>
                                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Sangat Miskin</p>
                                <p class="font-black text-red-600 text-lg">{{ number_format($point['x'], 0, ',', '.') }} <span class="text-xs font-medium text-red-400">Jiwa</span></p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Bansos Pangan</p>
                                <p class="font-black text-slate-700 text-lg">{{ number_format($point['y'], 0, ',', '.') }} <span class="text-xs font-medium text-slate-400">KK</span></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-12 bg-slate-50 rounded-2xl border border-slate-100 border-dashed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-slate-500 font-medium">Tidak ada kecamatan di klaster ini.</p>
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
                            font: { family: "'Inter', sans-serif", weight: 'bold' }
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
                            font: { weight: 'bold' }
                        },
                        grid: { color: '#F1F5F9' }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Penerima Bansos (KK)',
                            font: { weight: 'bold' }
                        },
                        grid: { color: '#F1F5F9' }
                    }
                }
            }
        });
    });
</script>
@endpush
