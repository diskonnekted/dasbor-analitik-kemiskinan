@extends('layouts.app')

@section('title', 'Prediksi Kemiskinan Masa Depan')

@section('content')
<div class="w-full mx-auto py-8 px-4 sm:px-8 lg:px-12 space-y-8 bg-[#f8fafc] min-h-screen">
    
    <!-- Hero Header Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-950 to-indigo-950 rounded-[2.5rem] p-8 md:p-12 shadow-2xl border border-slate-800 flex flex-col xl:flex-row xl:items-center justify-between gap-8">
        <!-- Decorative Glow -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-cyan-500/20 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-20 w-80 h-80 bg-blue-500/20 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl flex items-start gap-6">
            <a href="{{ route('dashboard') }}" class="mt-2 bg-white/10 hover:bg-white/20 p-3 rounded-2xl text-white transition-all backdrop-blur-md border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-cyan-300 text-xs font-bold tracking-widest uppercase mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4 4-6 6" /></svg>
                    Statistical Forecasting
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-2">Proyeksi <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">Masa Depan</span></h1>
                <p class="text-slate-400 text-lg">Peramalan kuantitatif jumlah penduduk miskin menggunakan algoritma <b>Regresi Linear Sederhana</b> berdasarkan tren data aktual tahun {{ min($years) }} - {{ max($years) }}.</p>
            </div>
        </div>
        
        <div class="relative z-10 flex flex-col items-center justify-center bg-white/5 p-6 rounded-3xl border border-white/10 backdrop-blur-md min-w-[200px]">
            <span class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Horizon Waktu</span>
            <div class="flex items-baseline gap-1">
                <span class="text-5xl font-black text-white">3</span>
                <span class="text-lg font-bold text-cyan-400">Tahun</span>
            </div>
            <div class="mt-2 text-[10px] text-blue-400 font-mono bg-blue-400/10 px-2 py-1 rounded">Ke Depan</div>
        </div>
    </div>

    <!-- Alert Status Tren -->
    @if($slope > 0)
    <div class="relative overflow-hidden bg-gradient-to-r from-red-500 to-red-600 p-6 sm:p-8 rounded-[2rem] shadow-lg shadow-red-500/20 border border-red-400 group">
        <div class="absolute -right-12 -top-12 opacity-20 group-hover:scale-110 transition-transform duration-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-64 w-64 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
            </svg>
        </div>
        <div class="relative z-10 flex items-start sm:items-center gap-6">
            <div class="bg-white/20 p-4 rounded-2xl backdrop-blur-sm border border-white/30 hidden sm:block">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div>
                <h3 class="text-white font-black text-xl md:text-2xl mb-2 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-white animate-pulse sm:hidden"></span>
                    Peringatan Kritis: Tren Kemiskinan Meningkat
                </h3>
                <p class="text-red-100 text-sm md:text-base leading-relaxed max-w-4xl">Model matematis mendeteksi <b>kenaikan linier</b> dari waktu ke waktu. Proyeksi algoritma kami memprediksi jumlah penduduk miskin akan terus bertambah parah dalam 3 tahun ke depan jika tidak ada intervensi dan kebijakan program baru yang radikal.</p>
            </div>
        </div>
    </div>
    @else
    <div class="relative overflow-hidden bg-gradient-to-r from-emerald-500 to-teal-600 p-6 sm:p-8 rounded-[2rem] shadow-lg shadow-emerald-500/20 border border-emerald-400 group">
        <div class="absolute -right-12 -top-12 opacity-20 group-hover:scale-110 transition-transform duration-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-64 w-64 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
        </div>
        <div class="relative z-10 flex items-start sm:items-center gap-6">
            <div class="bg-white/20 p-4 rounded-2xl backdrop-blur-sm border border-white/30 hidden sm:block">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <h3 class="text-white font-black text-xl md:text-2xl mb-2 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-white animate-pulse sm:hidden"></span>
                    Kabar Baik: Tren Kemiskinan Menurun
                </h3>
                <p class="text-emerald-50 text-sm md:text-base leading-relaxed max-w-4xl">Model matematis mendeteksi <b>penurunan linier</b> yang stabil. Proyeksi algoritma menyimpulkan bahwa program pengentasan kemiskinan saat ini bekerja dengan efektif dan berada di jalur yang sangat tepat untuk 3 tahun ke depan.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Tabel Prediksi (Takes 4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-8 md:p-10 rounded-[2rem] shadow-sm border border-slate-100 h-full flex flex-col relative overflow-hidden">
                <div class="absolute left-0 top-0 w-24 h-24 bg-blue-50 rounded-br-full -z-0"></div>
                
                <h2 class="text-2xl font-black text-slate-800 mb-6 relative z-10 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Angka Proyeksi
                </h2>
                
                <div class="flex-1 relative z-10">
                    <div class="space-y-4">
                        <!-- Table Headers -->
                        <div class="grid grid-cols-3 gap-4 mb-2 pb-2 border-b border-slate-200">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-2">Tahun</div>
                            <div class="text-[10px] font-bold text-red-400 uppercase tracking-widest text-center">Linear Reg.</div>
                            <div class="text-[10px] font-bold text-fuchsia-400 uppercase tracking-widest text-right pr-2">Exp. Smoothing</div>
                        </div>
                        
                        @foreach($futureYears as $idx => $fy)
                        <div class="grid grid-cols-3 gap-4 items-center p-4 bg-white/50 hover:bg-white border border-slate-100 hover:border-blue-200 rounded-2xl transition-colors shadow-sm group">
                            <!-- Tahun -->
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 shadow-sm font-black text-blue-700">
                                    {{ $fy }}
                                </div>
                            </div>
                            
                            <!-- Regresi Linier -->
                            <div class="text-center">
                                <span class="block text-xl font-bold {{ $slope > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                                    {{ number_format($predictionDataLR[$idx], 0, ',', '.') }}
                                </span>
                            </div>
                            
                            <!-- Double Exp Smoothing -->
                            <div class="text-right">
                                <span class="block text-xl font-black {{ $lastTrend > 0 ? 'text-fuchsia-600' : 'text-emerald-600' }}">
                                    {{ number_format($predictionDataDES[$idx], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 relative z-10">
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Metodologi (Duel Algoritma)</h3>
                    <div class="space-y-2">
                        <p class="text-xs text-slate-600 leading-relaxed bg-red-50/50 p-3 rounded-xl border border-red-100">
                            <span class="font-bold text-red-600">Linear Regression:</span> Menarik garis tren lurus (konstan) dari historis tahun {{ min($years) }} - {{ max($years) }}.
                        </p>
                        <p class="text-xs text-slate-600 leading-relaxed bg-fuchsia-50/50 p-3 rounded-xl border border-fuchsia-100">
                            <span class="font-bold text-fuchsia-600">Exp. Smoothing:</span> Model dinamis (Holt's Method) yang memberi bobot lebih tinggi pada data terbaru, menangkap perubahan momentum & tren secara reaktif.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart (Takes 8 cols) -->
        <div class="lg:col-span-8 bg-white p-8 md:p-10 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-50 rounded-bl-full -z-0"></div>
            
            <h2 class="text-2xl font-black text-slate-800 mb-2 relative z-10 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                Grafik Peramalan (Forecasting)
            </h2>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-8 relative z-10">Komparasi model matematis regresi vs algoritma penghalusan eksponensial.</p>
            
            <div class="relative flex-1 w-full min-h-[400px] z-10 bg-slate-50/50 rounded-2xl p-4 border border-slate-50">
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
                        borderColor: '#1F2937', // slate-800
                        backgroundColor: '#1F2937',
                        borderWidth: 3,
                        pointRadius: 5,
                        pointBackgroundColor: '#1F2937',
                        fill: false,
                        tension: 0.1,
                        order: 1
                    },
                    {
                        label: 'Proyeksi (Linear Regression)',
                        data: @json($chartPredictionLR),
                        borderColor: '#DC2626', // red-600
                        backgroundColor: '#DC2626',
                        borderWidth: 3,
                        borderDash: [5, 5], // Putus-putus
                        pointRadius: 6,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#DC2626',
                        pointBorderWidth: 2,
                        fill: false,
                        tension: 0.1,
                        order: 2
                    },
                    {
                        label: 'Proyeksi (Exponential Smoothing)',
                        data: @json($chartPredictionDES),
                        borderColor: '#C026D3', // fuchsia-600
                        backgroundColor: '#C026D3',
                        borderWidth: 4,
                        borderDash: [2, 4], // Putus-putus beda gaya
                        pointRadius: 7,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#C026D3',
                        pointBorderWidth: 3,
                        fill: false,
                        tension: 0.3, // Sedikit lebih mulus
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
                            font: {
                                family: "'Inter', sans-serif",
                                weight: 'bold'
                            }
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
                        grid: {
                            color: '#F1F5F9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
