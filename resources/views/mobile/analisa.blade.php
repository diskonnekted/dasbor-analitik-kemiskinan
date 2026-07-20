@extends('layouts.mobile')

@section('title', 'Analisa Diagnostik & Korelasi')
@section('pagelabel', 'Korelasi')

@section('content')
<div class="w-full px-4 py-5 space-y-5">

    <!-- Header -->
    <div class="bg-white border-2 border-[#171717] p-5 shadow-[4px_4px_0px_0px_#171717]">
        <div class="inline-flex items-center gap-2 px-2 py-1 bg-[#171717] text-white text-[9px] font-mono font-bold tracking-widest uppercase mb-3 border border-[#171717]">
            <i data-lucide="line-chart" class="w-3 h-3" aria-hidden="true"></i> Advanced Analytics
        </div>
        <h1 class="text-2xl font-serif font-black uppercase tracking-tight mb-2">Analisa Korelasi</h1>
        <p class="text-neutral-600 font-mono font-bold text-xs leading-relaxed">Faktor infrastruktur, kesehatan, dan pendidikan yang paling berdampak terhadap tingkat kemiskinan di Banjarnegara.</p>
    </div>

    @if(isset($error))
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] font-mono font-bold text-sm" role="alert">
        {{ $error }}
    </div>
    @else

    @php $topFactor = $hasilKorelasi[0] ?? null; @endphp
    @if($topFactor)
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex items-center gap-4">
        <div class="flex-shrink-0 w-16 h-16 bg-[#171717] text-white flex items-center justify-center border-2 border-[#171717]">
            <span class="text-xl font-serif font-black">{{ number_format(abs($topFactor['korelasi']) * 100, 0) }}%</span>
        </div>
        <div>
            <h3 class="text-[9px] font-mono font-black tracking-widest uppercase mb-1">Temuan Kunci</h3>
            <p class="text-base font-serif font-black uppercase leading-tight">
                "<span class="underline">{{ $topFactor['indikator'] }}</span>"
                <span class="bg-[#171717] text-white px-1.5 py-0.5 inline-block mt-1 text-xs">{{ strtoupper($topFactor['kekuatan']) }}</span>
            </p>
        </div>
    </div>
    @endif

    <!-- Ranking -->
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <h2 class="text-sm font-serif font-black uppercase tracking-widest mb-4 flex items-center gap-2 border-b-2 border-[#171717] pb-2">
            <i data-lucide="bar-chart" class="w-4 h-4" aria-hidden="true"></i> Peringkat Korelasi
        </h2>
        <div class="space-y-3">
            @forelse($hasilKorelasi as $index => $row)
            <div class="flex items-center gap-3 p-3 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <div class="w-7 h-7 flex-shrink-0 flex items-center justify-center font-mono font-black text-[10px] {{ $index === 0 ? 'bg-[#171717] text-white' : 'bg-white border-2 border-[#171717]' }}">#{{ $index + 1 }}</div>
                <div class="flex-grow min-w-0">
                    <h4 class="font-black text-[10px] font-mono uppercase tracking-tight mb-1 truncate">{{ $row['indikator'] }}</h4>
                    <div class="w-full bg-neutral-100 h-2.5 border-2 border-[#171717] overflow-hidden">
                        <div class="h-full bg-[#171717]" style="width: {{ abs($row['korelasi']) * 100 }}%"></div>
                    </div>
                </div>
                <div class="flex flex-col items-end flex-shrink-0">
                    <span class="text-xs font-mono font-black">{{ number_format($row['korelasi'], 2) }}</span>
                    <span class="text-[8px] uppercase font-mono tracking-widest font-black bg-[#f4f4f0] border border-[#171717] px-1 mt-0.5">{{ $row['kekuatan'] }}</span>
                </div>
            </div>
            @empty
            <div class="text-center py-10 bg-neutral-100 border-2 border-[#171717] border-dashed">
                <i data-lucide="database" class="h-8 w-8 text-neutral-400 mx-auto mb-2" aria-hidden="true"></i>
                <p class="text-neutral-600 font-mono font-bold text-[10px] uppercase">Belum ada data korelasi.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Cara Membaca -->
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <h4 class="font-serif font-black text-xs uppercase tracking-widest mb-2 flex items-center gap-2 border-b-2 border-[#171717] pb-2">
            <i data-lucide="book-open" class="w-4 h-4" aria-hidden="true"></i> Cara Membaca Analisa
        </h4>
        <p class="text-[11px] font-mono font-bold text-neutral-600 leading-relaxed mb-4">Skor <strong class="text-[#171717]">korelasi</strong> mengukur keterkaitan tiap indikator dengan jumlah penduduk sangat miskin di 20 kecamatan (bukan sebab-akibat langsung).</p>

        <div class="space-y-3">
            <div class="bg-[#f4f4f0] p-3 border-2 border-[#171717]">
                <h5 class="font-mono font-black text-[10px] uppercase tracking-tight mb-2">1. Arah Hubungan</h5>
                <ul class="text-[11px] space-y-1.5 font-mono font-bold text-neutral-600 leading-relaxed">
                    <li><span class="font-black text-[#171717] bg-white border border-[#171717] px-1">POSITIF (+)</span> indikator naik → kemiskinan ikut naik (risiko).</li>
                    <li><span class="font-black text-[#171717] bg-white border border-[#171717] px-1">NEGATIF (−)</span> indikator naik → kemiskinan turun (pelindung).</li>
                </ul>
            </div>
            <div class="bg-[#f4f4f0] p-3 border-2 border-[#171717]">
                <h5 class="font-mono font-black text-[10px] uppercase tracking-tight mb-2">2. Kekuatan (Skor r)</h5>
                <ul class="text-[11px] space-y-1 font-mono font-bold text-neutral-600">
                    <li class="flex justify-between"><span>0,8 – 1,0</span><span class="font-black text-[#171717]">Sangat Kuat</span></li>
                    <li class="flex justify-between"><span>0,6 – 0,8</span><span class="font-black text-[#171717]">Kuat</span></li>
                    <li class="flex justify-between"><span>0,4 – 0,6</span><span class="font-black text-[#171717]">Sedang</span></li>
                    <li class="flex justify-between"><span>0,0 – 0,4</span><span class="font-black text-[#171717]">Lemah</span></li>
                </ul>
                <p class="text-[10px] font-mono font-bold text-neutral-500 mt-2">Dinilai dari nilai mutlaknya: −0,75 sama kuat dengan +0,75.</p>
            </div>
        </div>
    </div>

    <a href="{{ route('export.csv') }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-[#171717] text-white font-mono font-black text-sm border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <i data-lucide="download" class="w-5 h-5" aria-hidden="true"></i> Unduh Laporan (.csv)
    </a>

    @endif
</div>
@endsection
