@extends('layouts.app')

@section('title', 'Analisa Diagnostik & Korelasi')

@section('content')
<div class="w-full mx-auto py-8 px-4 sm:px-8 lg:px-12 space-y-8 bg-slate-50 min-h-screen">
    
    <!-- Hero Header -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 rounded-[2.5rem] p-10 md:p-14 shadow-2xl">
        <!-- Decorative blobs -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-blue-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-20 w-72 h-72 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white/90 text-xs font-bold tracking-widest uppercase mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    Advanced Analytics
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-4 leading-tight">Analisa <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">Diagnostik & Korelasi</span></h1>
                <p class="text-blue-100/80 text-lg md:text-xl font-light leading-relaxed">Membongkar pola tersembunyi: Temukan faktor infrastruktur, kesehatan, dan pendidikan yang memiliki dampak paling mematikan terhadap tingkat kemiskinan di Banjarnegara.</p>
            </div>
            
            <a href="{{ route('dashboard') }}" class="group relative inline-flex items-center justify-center gap-3 px-8 py-4 bg-white text-slate-900 font-bold rounded-full overflow-hidden transition-transform hover:scale-105 hover:shadow-[0_0_40px_rgba(255,255,255,0.3)]">
                <span class="relative z-10">Dasbor Utama</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 relative z-10 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </a>
        </div>
    </div>

    @if(isset($error))
    <div class="bg-red-50 text-red-700 p-6 rounded-2xl border border-red-200">
        {{ $error }}
    </div>
    @else
    
    <!-- Top Insight Highlight -->
    @php
        $topFactor = $hasilKorelasi[0] ?? null;
    @endphp
    @if($topFactor)
    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm flex flex-col md:flex-row items-center gap-8 relative overflow-hidden">
        <div class="absolute right-0 top-0 w-1/3 h-full bg-gradient-to-l from-{{ $topFactor['korelasi'] < 0 ? 'emerald' : 'red' }}-50 to-transparent pointer-events-none"></div>
        
        <div class="flex-shrink-0 w-24 h-24 rounded-full bg-{{ $topFactor['korelasi'] < 0 ? 'emerald' : 'red' }}-100 flex items-center justify-center border-8 border-white shadow-lg z-10">
            <span class="text-3xl font-black text-{{ $topFactor['korelasi'] < 0 ? 'emerald' : 'red' }}-600">{{ number_format(abs($topFactor['korelasi']) * 100, 0) }}%</span>
        </div>
        
        <div class="z-10 flex-grow">
            <h3 class="text-sm font-bold tracking-widest text-slate-400 uppercase mb-1">Temuan Kunci Paling Signifikan</h3>
            <p class="text-2xl md:text-3xl font-bold text-slate-800 leading-snug">
                "<span class="text-{{ $topFactor['korelasi'] < 0 ? 'emerald' : 'red' }}-600">{{ $topFactor['indikator'] }}</span>" 
                memiliki korelasi <strong class="text-slate-900">{{ strtolower($topFactor['kekuatan']) }}</strong> dengan Kemiskinan Ekstrem.
            </p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        
        <!-- Tabel Ranking Korelasi -->
        <div class="xl:col-span-5 flex flex-col gap-6">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex-grow relative overflow-hidden">
                <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    Peringkat Korelasi
                </h2>
                
                <div class="space-y-5">
                    @foreach($hasilKorelasi as $index => $row)
                    <div class="group flex items-center gap-4 p-4 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-all cursor-default">
                        <!-- Rank Badge -->
                        <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-sm {{ $index === 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }}">
                            #{{ $index + 1 }}
                        </div>
                        
                        <!-- Indicator Name -->
                        <div class="flex-grow">
                            <h4 class="font-bold text-slate-800 text-sm mb-1 group-hover:text-indigo-600 transition-colors">{{ $row['indikator'] }}</h4>
                            
                            <!-- Visual Bar -->
                            <div class="w-full bg-slate-100 rounded-full h-1.5 mb-1 overflow-hidden flex">
                                <!-- Draw bar starting from center -->
                                @php
                                    $width = abs($row['korelasi']) * 100;
                                    $color = $row['korelasi'] < 0 ? 'bg-emerald-500' : 'bg-red-500';
                                @endphp
                                <div class="h-full {{ $color }} rounded-full" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                        
                        <!-- Score -->
                        <div class="flex flex-col items-end flex-shrink-0">
                            <span class="text-lg font-black {{ $row['korelasi'] < 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ number_format($row['korelasi'], 2) }}
                            </span>
                            <span class="text-[0.65rem] uppercase tracking-wider font-bold text-slate-400">{{ $row['kekuatan'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-slate-800 text-slate-300 p-6 rounded-3xl shadow-lg relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h4 class="text-white font-bold mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                    Cara Membaca Matriks
                </h4>
                <ul class="text-sm space-y-2 font-light">
                    <li class="flex items-start gap-2"><strong class="text-emerald-400 min-w-max">Hijau (-)</strong> Berarti Solusi. Jika nilai ini naik, kemiskinan turun.</li>
                    <li class="flex items-start gap-2"><strong class="text-red-400 min-w-max">Merah (+)</strong> Berarti Risiko. Jika nilai ini naik, kemiskinan ikut naik.</li>
                    <li class="flex items-start gap-2"><strong class="text-white min-w-max">Skor Mendekati 1</strong> Berarti korelasi sangat memengaruhi satu sama lain.</li>
                </ul>
            </div>
        </div>

        <!-- Scatter Plots -->
        <div class="xl:col-span-7 bg-white p-8 md:p-10 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
            <h2 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                Peta Sebaran per Kecamatan
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($hasilKorelasi as $index => $row)
                <div class="group flex flex-col border border-slate-100 rounded-3xl p-5 bg-white hover:shadow-xl hover:border-blue-100 transition-all duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-sm font-extrabold text-slate-700 leading-tight w-2/3">{{ $row['indikator'] }}</h3>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $row['korelasi'] < 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            r = {{ number_format($row['korelasi'], 2) }}
                        </span>
                    </div>
                    <div class="relative w-full h-[220px] bg-slate-50/50 rounded-2xl p-2">
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
            
            new Chart(ctx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: row.indikator,
                        data: dataPoints,
                        backgroundColor: row.korelasi < 0 ? 'rgba(16, 185, 129, 0.6)' : 'rgba(220, 38, 38, 0.6)', // Emerald if negative, Red if positive
                        borderColor: row.korelasi < 0 ? 'rgba(16, 185, 129, 1)' : 'rgba(220, 38, 38, 1)',
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
                            title: { display: true, text: row.indikator, font: {size: 10} },
                            ticks: { font: {size: 10} }
                        },
                        y: {
                            title: { display: true, text: 'Penduduk Miskin', font: {size: 10} },
                            ticks: { font: {size: 10} }
                        }
                    }
                }
            });
        });
    });
</script>
@endpush
