@extends('layouts.app')

@section('title', 'Analisa Diagnostik & Korelasi')

@section('content')
<div class="flex-grow overflow-y-auto w-full py-8 px-6 sm:px-8 space-y-8 bg-[#f4f4f0] text-[#171717]">
    
    <!-- Hero Header -->
    <div class="bg-white border-2 border-[#171717] p-8 md:p-10 shadow-[4px_4px_0px_0px_#171717] flex flex-col md:flex-row md:items-center justify-between gap-8 mb-6">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#171717] text-white text-[10px] font-mono font-bold tracking-widest uppercase mb-4 border border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <i data-lucide="line-chart" class="w-3.5 h-3.5"></i>
                Advanced Analytics
            </div>
            <h1 class="text-3xl md:text-4xl font-serif font-black text-[#171717] uppercase tracking-tight mb-2">Analisa Diagnostik & Korelasi</h1>
            <p class="text-neutral-600 font-mono font-bold text-xs md:text-sm leading-relaxed">Membongkar pola tersembunyi: Temukan faktor infrastruktur, kesehatan, dan pendidikan yang memiliki dampak paling mematikan terhadap tingkat kemiskinan di Banjarnegara.</p>
        </div>
        
        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-[#171717] font-mono font-black uppercase text-xs border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] hover:shadow-[4px_4px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">
            <span>Dasbor Utama</span>
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>

    @if(isset($error))
    <div class="bg-white text-[#171717] p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] font-mono">
        {{ $error }}
    </div>
    @else
    
    <!-- Top Insight Highlight -->
    @php
        $topFactor = $hasilKorelasi[0] ?? null;
    @endphp
    @if($topFactor)
    <div class="bg-white p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col md:flex-row items-center gap-6 relative">
        <div class="flex-shrink-0 w-20 h-20 bg-[#171717] text-white flex items-center justify-center border-2 border-[#171717] shadow-[2px_2px_0px_0px_#f4f4f0]">
            <span class="text-2xl font-serif font-black">{{ number_format(abs($topFactor['korelasi']) * 100, 0) }}%</span>
        </div>
        
        <div class="flex-grow">
            <h3 class="text-[10px] font-mono font-black tracking-widest text-[#171717] uppercase mb-1">Temuan Kunci Signifikan</h3>
            <p class="text-xl md:text-2xl font-serif font-black text-[#171717] uppercase tracking-tight leading-none">
                "<span class="underline">{{ $topFactor['indikator'] }}</span>" 
                memiliki korelasi <strong class="bg-[#171717] text-white px-2 py-0.5 ml-1">{{ strtoupper($topFactor['kekuatan']) }}</strong>
            </p>
            <p class="text-[11px] font-mono font-bold text-neutral-500 mt-2">Terhadap tingkat Kemiskinan Ekstrem secara keseluruhan.</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        
        <!-- Tabel Ranking Korelasi -->
        <div class="xl:col-span-5 flex flex-col gap-6">
            <div class="bg-white p-6 shadow-[4px_4px_0px_0px_#171717] border-2 border-[#171717] flex-grow">
                <h2 class="text-sm font-serif font-black uppercase tracking-widest text-[#171717] mb-6 flex items-center gap-2 border-b-2 border-[#171717] pb-2">
                    <i data-lucide="bar-chart" class="w-4 h-4"></i>
                    Peringkat Korelasi
                </h2>
                
                <div class="space-y-4">
                    @foreach($hasilKorelasi as $index => $row)
                    <div class="flex items-center gap-4 p-3 bg-white border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                        <!-- Rank Badge -->
                        <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center font-mono font-black text-xs {{ $index === 0 ? 'bg-[#171717] text-white' : 'bg-white text-[#171717] border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]' }}">
                            #{{ $index + 1 }}
                        </div>
                        
                        <!-- Indicator Name -->
                        <div class="flex-grow">
                            <h4 class="font-black text-[#171717] text-[11px] font-mono uppercase tracking-tight mb-1">{{ $row['indikator'] }}</h4>
                            
                            <!-- Visual Bar -->
                            <div class="w-full bg-neutral-100 h-3 border-2 border-[#171717] flex overflow-hidden">
                                @php
                                    $width = abs($row['korelasi']) * 100;
                                @endphp
                                <div class="h-full bg-[#171717]" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                        
                        <!-- Score -->
                        <div class="flex flex-col items-end flex-shrink-0">
                            <span class="text-sm font-mono font-black text-[#171717]">
                                {{ number_format($row['korelasi'], 2) }}
                            </span>
                            <span class="text-[9px] uppercase font-mono tracking-widest font-black text-[#171717] bg-[#f4f4f0] border-2 border-[#171717] px-1.5 mt-1">{{ $row['kekuatan'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-white text-[#171717] p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
                <h4 class="text-[#171717] font-serif font-black text-xs uppercase tracking-widest mb-3 flex items-center gap-2 border-b-2 border-[#171717] pb-2">
                    <i data-lucide="info" class="w-4 h-4"></i>
                    Cara Membaca Matriks
                </h4>
                <ul class="text-xs space-y-2 font-mono font-bold text-neutral-600">
                    <li class="flex items-start gap-2"><strong class="underline text-[#171717] min-w-max">Nilai Negatif (-)</strong> Solusi. Jika nilai ini naik, kemiskinan turun.</li>
                    <li class="flex items-start gap-2"><strong class="underline text-[#171717] min-w-max">Nilai Positif (+)</strong> Risiko. Jika nilai ini naik, kemiskinan naik.</li>
                    <li class="flex items-start gap-2"><strong class="underline text-[#171717] min-w-max">Skor ~1 / -1</strong> Sangat memengaruhi satu sama lain secara linier.</li>
                </ul>
            </div>
        </div>

        <!-- Scatter Plots -->
        <div class="xl:col-span-7 bg-white p-6 md:p-8 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col">
            <h2 class="text-sm font-serif font-black uppercase tracking-widest text-[#171717] mb-6 flex items-center gap-2 border-b-2 border-[#171717] pb-2">
                <i data-lucide="line-chart" class="w-4 h-4"></i>
                Peta Sebaran per Kecamatan
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($hasilKorelasi as $index => $row)
                <div class="flex flex-col border-2 border-[#171717] p-4 bg-white shadow-[2px_2px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-[11px] font-mono font-black text-[#171717] uppercase leading-tight w-2/3">{{ $row['indikator'] }}</h3>
                        <span class="px-2 py-0.5 text-[9px] font-mono font-bold border-2 border-[#171717] bg-[#f4f4f0] text-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                            r = {{ number_format($row['korelasi'], 2) }}
                        </span>
                    </div>
                    <div class="relative w-full h-[200px] bg-white border border-[#171717] p-2 mt-2">
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
                        backgroundColor: '#171717',
                        borderColor: '#171717',
                        borderWidth: 1.5,
                        pointRadius: 5,
                        pointHoverRadius: 7
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
                            grid: { color: '#e5e5d8' },
                            title: { display: true, text: 'Variabel X', font: {family: 'Inter', size: 9, weight: 'bold'}, color: '#171717' },
                            ticks: { font: {family: 'Inter', size: 9}, color: '#171717' }
                        },
                        y: {
                            grid: { color: '#e5e5d8' },
                            title: { display: true, text: 'Penduduk Miskin (Y)', font: {family: 'Inter', size: 9, weight: 'bold'}, color: '#171717' },
                            ticks: { font: {family: 'Inter', size: 9}, color: '#171717' }
                        }
                    }
                }
            });
        });
    });
</script>
@endpush
