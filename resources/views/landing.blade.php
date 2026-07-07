@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<style>
    .number-display {
        font-size: 7rem;
        font-weight: 900;
        color: #171717;
        line-height: 1;
        letter-spacing: -2px;
        -webkit-text-stroke: 10px #ffffff;
        paint-order: stroke fill;
    }

    .metric-title {
        font-size: 0.65rem;
        letter-spacing: 2px;
        color: #171717;
        text-transform: uppercase;
        font-weight: 800;
    }

    /* Donut Chart Animation */
    .donut-ring {
        transition: stroke-dashoffset 0.5s ease-in-out;
    }

    /* Timeline Slider */
    .timeline-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 8px;
        background: #ffffff;
        border: 2px solid #171717;
        outline: none;
    }
    
    .timeline-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 24px;
        height: 24px;
        background: #171717;
        border: 2px solid #171717;
        cursor: pointer;
        box-shadow: 2px 2px 0px 0px #f4f4f0;
    }
</style>

<div class="flex-grow overflow-y-auto w-full flex flex-col relative bg-[#f4f4f0]" x-data="povertyClock()">
    
    <!-- Top Filters -->
    <div class="w-full px-8 py-6 flex flex-wrap items-end gap-10 border-b-2 border-[#171717] relative z-10 bg-[#f4f4f0]">
        
        <!-- Filters -->
        <div class="hidden lg:flex gap-8 items-end">
            <form method="GET" action="{{ route('landing') }}">
                <div class="text-[0.6rem] tracking-[2px] font-bold text-[#171717] uppercase mb-2">Pencarian</div>
                <div class="border-2 border-[#171717] p-2 bg-white text-sm text-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                    <select name="kecamatan" onchange="this.form.submit()" class="w-full bg-transparent border-none p-0 focus:ring-0 cursor-pointer appearance-none font-mono font-bold">
                        <option value="Semua">Kabupaten Banjarnegara</option>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec }}" {{ $selectedKecamatan === $kec ? 'selected' : '' }}>Kecamatan {{ ucwords(strtolower($kec)) }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            <div>
                <div class="text-[0.6rem] tracking-[2px] font-bold text-[#171717] uppercase mb-2">Filter Geografis</div>
                <div class="flex gap-2">
                    <span class="bg-white border-2 border-[#171717] text-[#171717] text-xs px-4 py-1.5 font-bold shadow-[2px_2px_0px_0px_#171717]">pedesaan</span>
                    <span class="bg-white border-2 border-[#171717] text-[#171717] text-xs px-4 py-1.5 font-bold shadow-[2px_2px_0px_0px_#171717]">perkotaan</span>
                </div>
            </div>
            <div>
                <div class="text-[0.6rem] tracking-[2px] font-bold text-[#171717] uppercase mb-2">Filter Demografis</div>
                <div class="flex gap-2">
                    <span class="bg-white border-2 border-[#171717] text-[#171717] text-xs px-3 py-1.5 font-bold shadow-[2px_2px_0px_0px_#171717]">wanita</span>
                    <span class="bg-white border-2 border-[#171717] text-[#171717] text-xs px-3 py-1.5 font-bold shadow-[2px_2px_0px_0px_#171717]">pria</span>
                    <span class="bg-white border-2 border-[#171717] text-[#171717] text-xs px-3 py-1.5 font-bold shadow-[2px_2px_0px_0px_#171717]">semua umur</span>
                </div>
            </div>
        </div>

        <div class="ml-auto flex items-center gap-4">
            <button class="bg-[#171717] text-white border-2 border-[#171717] px-6 py-2 text-xs font-black tracking-wider shadow-[2px_2px_0px_0px_#171717] hover:shadow-[4px_4px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">RESET</button>
            <button class="text-[#171717] border-2 border-[#171717] p-2 bg-white shadow-[2px_2px_0px_0px_#171717]">
                <i data-lucide="filter" class="h-5 w-5"></i>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow flex flex-col items-center justify-center relative px-6 w-full max-w-[1200px] mx-auto py-8">
        
        <div class="w-full flex flex-col lg:flex-row items-center justify-center gap-10">
            
            <!-- Donut Chart & Number Container -->
            <div class="relative flex items-center justify-center w-[400px] h-[400px]">
                
                <!-- SVG Donut -->
                <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 100 100">
                    <!-- Background Ring -->
                    <circle cx="50" cy="50" r="40" fill="none" stroke="#ffffff" stroke-width="18" />
                    <!-- Foreground Ring -->
                    <circle cx="50" cy="50" r="40" fill="none" stroke="#171717" stroke-width="18" 
                            stroke-dasharray="251.2" 
                            :stroke-dashoffset="getStrokeOffset()"
                            class="donut-ring" />
                </svg>

                <div class="absolute top-10 left-[60%] flex items-center gap-2 bg-white border-2 border-[#171717] p-2 shadow-[2px_2px_0px_0px_#171717] z-20">
                    <div class="w-4 h-4 bg-[#171717]"></div>
                    <div>
                        <div class="text-[0.6rem] font-bold text-neutral-500 tracking-wider">OFF-TRACK SDG1</div>
                        <div class="text-xs font-black text-[#171717]" x-text="new Intl.NumberFormat('id-ID').format(currentValue)"></div>
                    </div>
                </div>

                <!-- Central Text -->
                <div class="absolute left-[35%] flex flex-col z-10 w-[500px]">
                    <div class="number-display font-serif" x-text="new Intl.NumberFormat('id-ID').format(animatedValue)"></div>
                    <div class="text-xs font-serif font-black tracking-tight text-[#171717] mt-2 max-w-xs leading-relaxed ml-2 bg-white border-2 border-[#171717] p-3 shadow-[4px_4px_0px_0px_#171717]">
                        Penduduk di {{ $selectedKecamatan && $selectedKecamatan !== 'Semua' ? 'Kec. ' . ucwords(strtolower($selectedKecamatan)) : 'Banjarnegara' }} yang hidup dalam kemiskinan ekstrem.
                    </div>
                </div>

            </div>

        </div>

        <!-- 4 Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-16 max-w-4xl w-full">
            
            <!-- Target Escape Rate -->
            <div class="bg-white border-2 border-[#171717] p-6 shadow-[4px_4px_0px_0px_#171717] hover:shadow-[6px_6px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all flex items-start gap-4">
                <div class="flex-shrink-0 text-[#171717] p-2 bg-[#f4f4f0] border-2 border-[#171717]">
                    <i data-lucide="trending-up" class="h-8 w-8"></i>
                </div>
                <div>
                    <div class="metric-title mb-1">Target Escape Rate</div>
                    <div class="font-black text-2xl text-[#171717] font-serif">{{ $metrics['targetRate'] }} orang/detik</div>
                </div>
            </div>

            <!-- Escaped Today -->
            <div class="bg-white border-2 border-[#171717] p-6 shadow-[4px_4px_0px_0px_#171717] hover:shadow-[6px_6px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all flex items-start gap-4">
                <div class="flex-shrink-0 text-[#171717] p-2 bg-[#f4f4f0] border-2 border-[#171717]">
                    <i data-lucide="users" class="h-8 w-8"></i>
                </div>
                <div>
                    <div class="metric-title mb-1">Escaped Poverty Today</div>
                    <div class="font-black text-2xl text-[#171717] font-serif">{{ $metrics['escapedToday'] }}</div>
                </div>
            </div>

            <!-- Current Escape Rate -->
            <div class="bg-white border-2 border-[#171717] p-6 shadow-[4px_4px_0px_0px_#171717] hover:shadow-[6px_6px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all flex items-start gap-4">
                <div class="flex-shrink-0 text-[#171717] p-2 bg-[#f4f4f0] border-2 border-[#171717]">
                    <i data-lucide="trending-up" class="h-8 w-8"></i>
                </div>
                <div>
                    <div class="metric-title mb-1">Current Escape Rate</div>
                    <div class="font-black text-2xl text-[#171717] font-serif">{{ $metrics['currentRate'] }} orang/detik</div>
                </div>
            </div>

            <!-- Fell into poverty -->
            <div class="bg-white border-2 border-[#171717] p-6 shadow-[4px_4px_0px_0px_#171717] hover:shadow-[6px_6px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all flex items-start gap-4">
                <div class="flex-shrink-0 text-[#171717] p-2 bg-[#f4f4f0] border-2 border-[#171717]">
                    <i data-lucide="trending-down" class="h-8 w-8"></i>
                </div>
                <div>
                    <div class="metric-title mb-1">Fell Into Poverty Today</div>
                    <div class="font-black text-2xl text-[#171717] font-serif">{{ $metrics['fellToday'] }}</div>
                </div>
            </div>

        </div>

    </div>

    <!-- Timeline Slider -->
    <div class="w-full mt-auto bg-[#f4f4f0] py-8 px-10 border-t-2 border-[#171717] relative">
        <div class="max-w-[1000px] mx-auto relative pt-8">
            
            <!-- Custom Slider Thumb Label -->
            <div class="absolute -top-4 px-4 py-1.5 bg-[#171717] text-[#f4f4f0] text-xs font-mono font-bold border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] transform -translate-x-1/2 transition-all duration-200" 
                 :style="'left: ' + getSliderPercentage() + '%'"
                 x-text="selectedYear">
                2024
            </div>

            <input type="range" 
                   class="timeline-slider" 
                   min="{{ min(array_keys($timelineData)) }}" 
                   max="{{ max(array_keys($timelineData)) }}" 
                   step="1" 
                   x-model="selectedYear"
                   @input="updateValue()">
                   
            <!-- Ticks -->
            <div class="flex justify-between mt-4 text-[0.7rem] font-bold font-mono text-[#171717] tracking-widest relative">
                @foreach(array_keys($timelineData) as $yr)
                    <div class="flex flex-col items-center">
                        <span class="{{ $yr > $tahunSekarang ? 'underline font-black' : '' }}">
                            {{ $yr }}{{ $yr > $tahunSekarang ? '*' : '' }}
                        </span>
                    </div>
                @endforeach
            </div>
            
            <div class="text-center mt-6 text-xs font-mono font-bold text-neutral-600">
                <span>*</span> Data tahun <span class="underline">{{ $tahunSekarang + 1 }}</span> dan seterusnya merupakan proyeksi statistik berdasarkan tren historis.
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    const timelineData = @json($timelineData);
    const maxDataValue = Math.max(...Object.values(timelineData)) * 1.5;

    document.addEventListener('alpine:init', () => {
        Alpine.data('povertyClock', () => ({
            selectedYear: {{ $tahunSekarang }},
            currentValue: 0,
            animatedValue: 0,
            
            init() {
                this.updateValue();
            },

            updateValue() {
                let targetValue = timelineData[this.selectedYear] || 0;
                this.currentValue = targetValue;
                this.animateCounter(this.animatedValue, targetValue);
            },

            getStrokeOffset() {
                const circumference = 251.2;
                const ratio = this.currentValue / maxDataValue;
                const offset = circumference - (ratio * circumference);
                return offset;
            },

            getSliderPercentage() {
                const min = {{ min(array_keys($timelineData)) }};
                const max = {{ max(array_keys($timelineData)) }};
                return ((this.selectedYear - min) / (max - min)) * 100;
            },

            animateCounter(start, end) {
                let duration = 1000; 
                let startTimestamp = null;
                
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const easeOut = 1 - Math.pow(1 - progress, 3);
                    
                    this.animatedValue = Math.floor(start + (end - start) * easeOut);
                    
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    } else {
                        this.animatedValue = end;
                    }
                };
                
                window.requestAnimationFrame(step);
            }
        }));
    });
</script>
@endpush
@endsection
