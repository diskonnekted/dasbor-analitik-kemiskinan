@extends('layouts.app')

@section('title', 'Prediksi Kemiskinan Masa Depan')

@section('content')
<div class="flex-grow overflow-y-auto w-full py-8 px-6 sm:px-8 space-y-8 bg-[#f4f4f0] text-[#171717]">
    
    <!-- Hero Header Section -->
    <div class="relative bg-white border-2 border-[#171717] p-8 md:p-10 shadow-[4px_4px_0px_0px_#171717] flex flex-col xl:flex-row xl:items-center justify-between gap-8">
        
        <div class="relative z-10 max-w-3xl flex items-start gap-6">
            <a href="{{ route('dashboard') }}" class="mt-2 bg-white text-[#171717] p-3 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] hover:shadow-[4px_4px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">
                <i data-lucide="arrow-left" class="h-6 w-6"></i>
            </a>
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#171717] text-white text-xs font-mono font-bold tracking-widest uppercase mb-4 border border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                    Statistical Forecasting
                </div>
                <h1 class="text-3xl md:text-4xl font-serif font-black text-[#171717] uppercase tracking-tight mb-2">Proyeksi Masa Depan</h1>
                <p class="text-neutral-600 font-mono font-bold text-sm">Peramalan kuantitatif jumlah penduduk miskin menggunakan algoritma <b>Regresi Linear Sederhana</b> berdasarkan tren data aktual tahun {{ min($years) }} - {{ max($years) }}.</p>
            </div>
        </div>
        
        <div class="relative z-10 flex flex-col items-center justify-center bg-white p-5 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] min-w-[200px]">
            <span class="text-[#171717] text-xs font-mono font-black uppercase tracking-widest mb-1">Horizon Waktu</span>
            <div class="flex items-baseline gap-1">
                <span class="text-5xl font-mono font-black text-[#171717]">3</span>
                <span class="text-lg font-bold text-neutral-500 font-mono">Tahun</span>
            </div>
            <div class="mt-2 text-[10px] text-white font-mono font-bold bg-[#171717] px-2 py-1 border border-[#171717] shadow-[2px_2px_0px_0px_#f4f4f0]">Ke Depan</div>
        </div>
    </div>

    <!-- Alert Status Tren -->
    @if($slope > 0)
    <div class="bg-white p-6 sm:p-8 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] relative overflow-hidden">
        <div class="flex items-start sm:items-center gap-6">
            <div class="bg-[#171717] text-white p-4 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#f4f4f0] hidden sm:block">
                <i data-lucide="alert-triangle" class="h-10 w-10"></i>
            </div>
            <div>
                <h3 class="text-[#171717] font-serif font-black text-xl md:text-2xl mb-2 uppercase flex items-center gap-2">
                    Peringkat Kritis: Tren Kemiskinan Meningkat
                </h3>
                <p class="text-neutral-600 font-mono font-bold text-sm leading-relaxed max-w-4xl">Model matematis mendeteksi <span class="underline">kenaikan linier</span> dari waktu ke waktu. Proyeksi algoritma memprediksi jumlah penduduk miskin akan terus bertambah dalam 3 tahun ke depan jika tidak ada intervensi dan kebijakan program baru yang radikal.</p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-white p-6 sm:p-8 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] relative overflow-hidden">
        <div class="flex items-start sm:items-center gap-6">
            <div class="bg-[#171717] text-white p-4 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#f4f4f0] hidden sm:block">
                <i data-lucide="check" class="h-10 w-10"></i>
            </div>
            <div>
                <h3 class="text-[#171717] font-serif font-black text-xl md:text-2xl mb-2 uppercase flex items-center gap-2">
                    Kabar Baik: Tren Kemiskinan Menurun
                </h3>
                <p class="text-neutral-600 font-mono font-bold text-sm leading-relaxed max-w-4xl">Model matematis mendeteksi <span class="underline">penurunan linier</span> yang stabil. Proyeksi algoritma menyimpulkan bahwa program pengentasan kemiskinan saat ini bekerja dengan efektif dan berada di jalur yang sangat tepat untuk 3 tahun ke depan.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Tabel Prediksi (Takes 4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-8 md:p-10 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] h-full flex flex-col relative">
                
                <h2 class="text-xl font-serif font-black text-[#171717] mb-6 flex items-center gap-3 uppercase border-b-2 border-[#171717] pb-2">
                    <i data-lucide="table" class="w-6 h-6"></i>
                    Angka Proyeksi
                </h2>
                
                <div class="flex-1">
                    <div class="space-y-4">
                        <!-- Table Headers -->
                        <div class="grid grid-cols-3 gap-4 mb-2 pb-2 border-b-2 border-[#171717]">
                            <div class="text-[9px] font-mono font-black text-[#171717] uppercase tracking-widest pl-2">Tahun</div>
                            <div class="text-[9px] font-mono font-black text-[#171717] uppercase tracking-widest text-center">Linear Reg.</div>
                            <div class="text-[9px] font-mono font-black text-[#171717] uppercase tracking-widest text-right pr-2">Exp. Smooth</div>
                        </div>
                        
                        @foreach($futureYears as $idx => $fy)
                        <div class="grid grid-cols-3 gap-4 items-center p-4 bg-white border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">
                            <!-- Tahun -->
                            <div class="flex items-center gap-3">
                                <div class="bg-[#f4f4f0] px-3 py-1.5 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] font-mono font-black text-[#171717]">
                                    {{ $fy }}
                                </div>
                            </div>
                            
                            <!-- Regresi Linier -->
                            <div class="text-center font-mono font-black text-[#171717] text-lg">
                                {{ number_format($predictionDataLR[$idx], 0, ',', '.') }}
                            </div>
                            
                            <!-- Double Exp Smoothing -->
                            <div class="text-right font-mono font-black text-[#171717] text-lg">
                                {{ number_format($predictionDataDES[$idx], 0, ',', '.') }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t-2 border-[#171717]">
                    <h3 class="text-[10px] font-mono font-black text-neutral-500 uppercase tracking-widest mb-3">Metodologi (Duel Algoritma)</h3>
                    <div class="space-y-3 font-mono text-xs">
                        <div class="p-3 bg-[#f4f4f0] border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                            <span class="font-black underline block mb-1">Linear Regression:</span> Menarik garis tren lurus (konstan) dari historis tahun {{ min($years) }} - {{ max($years) }}.
                        </div>
                        <div class="p-3 bg-[#f4f4f0] border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                            <span class="font-black underline block mb-1">Exp. Smoothing:</span> Model dinamis (Holt's Method) yang memberi bobot lebih tinggi pada data terbaru untuk memproyeksikan momentum.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart (Takes 8 cols) -->
        <div class="lg:col-span-8 bg-white p-8 md:p-10 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col relative">
            
            <h2 class="text-xl font-serif font-black text-[#171717] mb-2 flex items-center gap-3 uppercase">
                <i data-lucide="line-chart" class="w-6 h-6"></i>
                Grafik Peramalan (Forecasting)
            </h2>
            <p class="text-neutral-500 font-mono font-bold text-xs uppercase tracking-widest mb-8">Komparasi model matematis regresi vs algoritma penghalusan eksponensial.</p>
            
            <div class="relative flex-1 w-full min-h-[400px] border border-[#171717] p-4 bg-white">
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
                        borderColor: '#171717',
                        backgroundColor: '#171717',
                        borderWidth: 3,
                        pointRadius: 6,
                        pointBackgroundColor: '#171717',
                        fill: false,
                        tension: 0,
                        order: 1
                    },
                    {
                        label: 'Proyeksi (Linear Regression)',
                        data: @json($chartPredictionLR),
                        borderColor: '#52524a',
                        backgroundColor: '#52524a',
                        borderWidth: 2.5,
                        borderDash: [5, 5],
                        pointRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#52524a',
                        pointBorderWidth: 2,
                        fill: false,
                        tension: 0,
                        order: 2
                    },
                    {
                        label: 'Proyeksi (Exponential Smoothing)',
                        data: @json($chartPredictionDES),
                        borderColor: '#a3a398',
                        backgroundColor: '#a3a398',
                        borderWidth: 2.5,
                        borderDash: [2, 3],
                        pointRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#a3a398',
                        pointBorderWidth: 2,
                        fill: false,
                        tension: 0,
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
                            font: { family: 'Inter', weight: 'bold' }
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
                        grid: { color: '#e5e5d8' },
                        ticks: { font: { family: 'Inter' }, color: '#171717' }
                    },
                    x: {
                        grid: { color: '#171717', width: 1 },
                        ticks: { font: { family: 'Inter' }, color: '#171717' }
                    }
                }
            }
        });
    });
</script>
@endpush
