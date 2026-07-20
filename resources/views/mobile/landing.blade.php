@extends('layouts.mobile')

@section('title', 'Beranda')
@section('pagelabel', 'Beranda')

@section('content')
<div class="w-full px-4 py-5 space-y-5" x-data="povertyClock()">

    <!-- Filter -->
    <form method="GET" action="{{ route('landing') }}" class="bg-white border-2 border-[#171717] p-4 shadow-[4px_4px_0px_0px_#171717]">
        <label for="kec-landing" class="block text-[9px] font-mono font-black text-neutral-500 uppercase tracking-widest mb-2">Wilayah</label>
        <div class="relative border-2 border-[#171717] bg-white shadow-[2px_2px_0px_0px_#171717]">
            <select id="kec-landing" name="kecamatan" onchange="this.form.submit()" class="w-full appearance-none bg-transparent border-none py-3 px-4 focus:ring-0 font-mono font-black cursor-pointer">
                <option value="Semua">Kabupaten Banjarnegara</option>
                @foreach($kecamatans as $kec)
                    <option value="{{ $kec }}" {{ $selectedKecamatan === $kec ? 'selected' : '' }}>Kec. {{ ucwords(strtolower($kec)) }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <!-- Poverty Clock -->
    <div class="bg-white border-2 border-[#171717] p-6 shadow-[4px_4px_0px_0px_#171717] text-center">
        <div class="relative w-56 h-56 mx-auto flex items-center justify-center">
            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 100 100" aria-hidden="true">
                <circle cx="50" cy="50" r="42" fill="none" stroke="#e5e5d8" stroke-width="9" />
                <circle cx="50" cy="50" r="42" fill="none" stroke="#171717" stroke-width="9" stroke-dasharray="263.9" :stroke-dashoffset="getStrokeOffset()" stroke-linecap="butt" style="transition: stroke-dashoffset 0.5s ease-in-out" />
            </svg>
            <div class="relative z-10 flex flex-col items-center px-4">
                <span class="text-3xl font-serif font-black leading-none tracking-tight" x-text="new Intl.NumberFormat('id-ID').format(animatedValue)"></span>
                <span class="text-[8px] font-mono font-black uppercase tracking-widest text-neutral-500 mt-2">Off-Track SDG1</span>
            </div>
        </div>
        <p class="text-xs font-serif font-black leading-relaxed mt-5 bg-[#f4f4f0] border-2 border-[#171717] p-3">
            Penduduk di {{ $selectedKecamatan && $selectedKecamatan !== 'Semua' ? 'Kec. ' . ucwords(strtolower($selectedKecamatan)) : 'Banjarnegara' }} yang hidup dalam kemiskinan ekstrem.
        </p>
    </div>

    <!-- Metrics -->
    @isset($metrics)
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white border-2 border-[#171717] p-4 shadow-[3px_3px_0px_0px_#171717]">
            <div class="text-[#171717] p-1.5 bg-[#f4f4f0] border-2 border-[#171717] inline-flex mb-2"><i data-lucide="trending-up" class="h-5 w-5" aria-hidden="true"></i></div>
            <div class="text-[9px] font-mono font-black uppercase tracking-widest mb-1">Target Escape Rate</div>
            <div class="font-black text-lg font-mono">{{ $metrics['targetRate'] }} <span class="text-[10px] text-neutral-500">org/dtk</span></div>
        </div>
        <div class="bg-white border-2 border-[#171717] p-4 shadow-[3px_3px_0px_0px_#171717]">
            <div class="text-[#171717] p-1.5 bg-[#f4f4f0] border-2 border-[#171717] inline-flex mb-2"><i data-lucide="users" class="h-5 w-5" aria-hidden="true"></i></div>
            <div class="text-[9px] font-mono font-black uppercase tracking-widest mb-1">Escaped Today</div>
            <div class="font-black text-lg font-mono">{{ $metrics['escapedToday'] }}</div>
        </div>
        <div class="bg-white border-2 border-[#171717] p-4 shadow-[3px_3px_0px_0px_#171717]">
            <div class="text-[#171717] p-1.5 bg-[#f4f4f0] border-2 border-[#171717] inline-flex mb-2"><i data-lucide="trending-up" class="h-5 w-5" aria-hidden="true"></i></div>
            <div class="text-[9px] font-mono font-black uppercase tracking-widest mb-1">Current Escape Rate</div>
            <div class="font-black text-lg font-mono">{{ $metrics['currentRate'] }} <span class="text-[10px] text-neutral-500">org/dtk</span></div>
        </div>
        <div class="bg-white border-2 border-[#171717] p-4 shadow-[3px_3px_0px_0px_#171717]">
            <div class="text-[#171717] p-1.5 bg-[#f4f4f0] border-2 border-[#171717] inline-flex mb-2"><i data-lucide="trending-down" class="h-5 w-5" aria-hidden="true"></i></div>
            <div class="text-[9px] font-mono font-black uppercase tracking-widest mb-1">Fell Into Poverty</div>
            <div class="font-black text-lg font-mono">{{ $metrics['fellToday'] }}</div>
        </div>
    </div>
    @endisset

    <!-- Timeline -->
    <div class="bg-white border-2 border-[#171717] p-5 shadow-[4px_4px_0px_0px_#171717]">
        <div class="flex items-center justify-between mb-4">
            <span class="text-[9px] font-mono font-black uppercase tracking-widest text-neutral-500">Timeline</span>
            <span class="px-3 py-1 bg-[#171717] text-[#f4f4f0] text-xs font-mono font-bold border-2 border-[#171717]" x-text="selectedYear"></span>
        </div>
        <input type="range" class="timeline-slider" min="{{ min(array_keys($timelineData)) }}" max="{{ max(array_keys($timelineData)) }}" step="1" x-model="selectedYear" @input="updateValue()" aria-label="Pilih tahun">
        <div class="flex justify-between mt-3 text-[9px] font-bold font-mono tracking-widest">
            @foreach(array_keys($timelineData) as $yr)
                <span class="{{ $yr > $tahunSekarang ? 'underline font-black' : '' }}">{{ $yr > $tahunSekarang ? "'" . substr($yr, -2) . '*' : "'" . substr($yr, -2) }}</span>
            @endforeach
        </div>
        <p class="text-center mt-4 text-[10px] font-mono font-bold text-neutral-600">* proyeksi statistik berdasarkan tren historis.</p>
    </div>

    <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-[#171717] text-white font-mono font-black text-sm border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        Masuk Dasbor Analitik <i data-lucide="arrow-right" class="w-5 h-5" aria-hidden="true"></i>
    </a>
</div>

<style>
    .timeline-slider { -webkit-appearance: none; width: 100%; height: 8px; background: #e5e5d8; border: 2px solid #171717; outline: none; }
    .timeline-slider::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; width: 22px; height: 22px; background: #171717; border: 2px solid #171717; cursor: pointer; box-shadow: 2px 2px 0px 0px #f4f4f0; }
</style>
@endsection

@push('scripts')
<script>
    const timelineData = @json($timelineData);
    const dataValues = Object.values(timelineData);
    const maxDataValue = (dataValues.length ? Math.max(...dataValues) : 1) * 1.5 || 1;

    document.addEventListener('alpine:init', () => {
        Alpine.data('povertyClock', () => ({
            selectedYear: {{ $tahunSekarang }},
            currentValue: 0,
            animatedValue: 0,

            init() { this.updateValue(); },

            updateValue() {
                let targetValue = timelineData[this.selectedYear] || 0;
                this.currentValue = targetValue;
                this.animateCounter(this.animatedValue, targetValue);
            },

            getStrokeOffset() {
                const circumference = 263.9;
                const ratio = this.currentValue / maxDataValue;
                return circumference - (ratio * circumference);
            },

            animateCounter(start, end) {
                let duration = 1000;
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const easeOut = 1 - Math.pow(1 - progress, 3);
                    this.animatedValue = Math.floor(start + (end - start) * easeOut);
                    if (progress < 1) window.requestAnimationFrame(step);
                    else this.animatedValue = end;
                };
                window.requestAnimationFrame(step);
            }
        }));
    });
</script>
@endpush
