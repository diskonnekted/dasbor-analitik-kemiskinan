@extends('layouts.mobile')

@section('title', 'Rekomendasi Kebijakan')
@section('pagelabel', 'Kebijakan')

@section('content')
<div class="w-full px-4 py-5 space-y-5">

    <!-- Header -->
    <div class="bg-white border-2 border-[#171717] p-5 shadow-[4px_4px_0px_0px_#171717]">
        <div class="inline-flex items-center gap-2 px-2 py-1 bg-[#171717] text-white text-[9px] font-mono font-bold tracking-widest uppercase mb-3 border border-[#171717]">
            <i data-lucide="gavel" class="w-3 h-3" aria-hidden="true"></i> Decision Support
        </div>
        <h1 class="text-2xl font-serif font-black uppercase tracking-tight mb-2">Rekomendasi Kebijakan</h1>
        <p class="text-neutral-600 font-mono font-bold text-xs leading-relaxed">Kecamatan prioritas, faktor pendorong kemiskinan, dan arahan intervensi lintas dinas (Tahun {{ $maxTahun ?? '-' }}).</p>
    </div>

    @if(isset($error))
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] font-mono font-bold text-sm" role="alert">
        {{ $error }}
    </div>
    @else

    <!-- Ringkasan Kabupaten -->
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-[#171717] text-white p-4 border-2 border-[#171717] shadow-[3px_3px_0px_0px_#171717]">
            <h3 class="font-mono text-neutral-300 font-black mb-1 text-[9px] uppercase tracking-widest">Sangat Miskin</h3>
            <p class="text-xl font-mono font-black">{{ number_format($ringkasan['total_sangat_miskin'], 0, ',', '.') }}</p>
            <span class="text-[9px] font-mono font-bold text-neutral-400">Jiwa</span>
        </div>
        <div class="bg-white p-4 border-2 border-[#171717] shadow-[3px_3px_0px_0px_#171717]">
            <h3 class="text-neutral-500 font-black mb-1 text-[9px] uppercase tracking-widest font-mono">Prioritas Tinggi</h3>
            <p class="text-xl font-mono font-black text-[#171717]">{{ $ringkasan['jml_prioritas_tinggi'] }} <span class="text-xs font-bold text-neutral-500">/ {{ $ringkasan['jml_kecamatan'] }}</span></p>
            <span class="text-[9px] font-mono font-bold text-neutral-400">Kecamatan</span>
        </div>
        <div class="bg-white p-4 border-2 border-[#171717] shadow-[3px_3px_0px_0px_#171717]">
            <h3 class="text-neutral-500 font-black mb-1 text-[9px] uppercase tracking-widest font-mono">Cakupan Bansos</h3>
            <p class="text-xl font-mono font-black text-[#171717]">{{ number_format($ringkasan['total_penerima'], 0, ',', '.') }}</p>
            <span class="text-[9px] font-mono font-bold text-neutral-400">KK</span>
        </div>
        <div class="bg-white p-4 border-2 border-[#171717] shadow-[3px_3px_0px_0px_#171717]">
            <h3 class="text-neutral-500 font-black mb-1 text-[9px] uppercase tracking-widest font-mono">Kemiskinan Kab.</h3>
            <p class="text-xl font-mono font-black text-[#171717]">{{ $ringkasan['persentase_kab'] !== null ? number_format($ringkasan['persentase_kab'], 2, ',', '.') . '%' : 'N/A' }}</p>
        </div>
    </div>

    <!-- Peringkat Prioritas -->
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <h2 class="text-base font-serif font-black uppercase mb-4 flex items-center gap-2 border-b-2 border-[#171717] pb-2">
            <i data-lucide="list-ordered" class="w-4 h-4" aria-hidden="true"></i> Peringkat Prioritas
        </h2>
        <div class="space-y-2">
            @foreach($rows as $i => $r)
            @php
                $badge = $r['prioritas'] === 'Tinggi'
                    ? 'bg-[#171717] text-white'
                    : ($r['prioritas'] === 'Menengah' ? 'bg-[#a3a398] text-[#171717]' : 'bg-[#e5e5d8] text-[#171717]');
            @endphp
            <div class="flex items-center gap-3 p-2.5 border-2 border-[#171717] {{ $i < 3 ? 'bg-[#f4f4f0]' : 'bg-white' }}">
                <span class="font-mono font-black text-sm w-5 text-center flex-shrink-0">{{ $i + 1 }}</span>
                <div class="flex-1 min-w-0">
                    <span class="block font-serif font-black uppercase text-sm leading-tight truncate">{{ ucwords(strtolower($r['kecamatan'])) }}</span>
                    <span class="block text-[9px] font-mono font-bold text-neutral-500">{{ number_format($r['sangat_miskin'], 0, ',', '.') }} Jiwa · Skor {{ $r['skor'] }}</span>
                </div>
                <span class="inline-block text-[8px] font-mono font-black uppercase tracking-wider px-1.5 py-1 border-2 border-[#171717] flex-shrink-0 {{ $badge }}">{{ $r['prioritas'] }}</span>
            </div>
            @endforeach
        </div>
        <p class="text-[9px] font-mono font-bold text-neutral-500 mt-3 leading-relaxed">Skor = 70% beban kemiskinan ekstrem + 30% kesenjangan cakupan bantuan.</p>
    </div>

    <!-- Faktor Pendorong -->
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <h2 class="text-base font-serif font-black uppercase mb-4 flex items-center gap-2 border-b-2 border-[#171717] pb-2">
            <i data-lucide="activity" class="w-4 h-4" aria-hidden="true"></i> Faktor Pendorong
        </h2>
        <div class="space-y-3">
            @forelse($faktor as $f)
            <div class="p-3 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <h4 class="font-mono font-black text-[10px] uppercase tracking-tight">{{ $f['indikator'] }}</h4>
                    <span class="font-mono font-black text-sm">{{ number_format($f['korelasi'], 2) }}</span>
                </div>
                <div class="w-full bg-neutral-100 h-2.5 border-2 border-[#171717] overflow-hidden">
                    <div class="h-full bg-[#171717]" style="width: {{ min(100, $f['abs'] * 100) }}%"></div>
                </div>
                <div class="flex items-center justify-between mt-1.5">
                    <span class="text-[8px] font-mono font-bold uppercase tracking-widest text-neutral-500">{{ $f['kategori'] }} · {{ $f['arah'] }}</span>
                    <span class="text-[8px] font-mono font-black uppercase tracking-widest bg-[#f4f4f0] border border-[#171717] px-1.5">{{ $f['kekuatan'] }}</span>
                </div>
            </div>
            @empty
            <div class="text-center py-10 bg-neutral-100 border-2 border-[#171717] border-dashed">
                <i data-lucide="database" class="h-8 w-8 text-neutral-400 mx-auto mb-2" aria-hidden="true"></i>
                <p class="text-neutral-600 font-mono font-bold text-[10px] uppercase">Belum ada faktor untuk dianalisa.</p>
            </div>
            @endforelse
        </div>
        <p class="text-[9px] font-mono font-bold text-neutral-500 mt-3 leading-relaxed">Korelasi Pearson indikator terhadap penduduk sangat miskin per kecamatan.</p>
    </div>

    <!-- Rekomendasi Otomatis -->
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <h2 class="text-base font-serif font-black uppercase mb-4 flex items-center gap-2 border-b-2 border-[#171717] pb-2">
            <i data-lucide="clipboard-check" class="w-4 h-4" aria-hidden="true"></i> Arahan Intervensi
        </h2>
        <div class="space-y-4">
            @forelse($rekomendasi as $rek)
            <div class="bg-[#f4f4f0] p-4 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <div class="flex items-center justify-between gap-2 mb-3 pb-2 border-b-2 border-[#171717]">
                    <h3 class="font-serif font-black text-base uppercase leading-tight">{{ ucwords(strtolower($rek['kecamatan'])) }}</h3>
                    <div class="text-right flex-shrink-0">
                        <span class="block font-mono font-black text-xs">{{ number_format($rek['sangat_miskin'], 0, ',', '.') }} Jiwa</span>
                        <span class="block text-[8px] font-mono font-bold uppercase tracking-widest text-neutral-500">Skor {{ $rek['skor'] }}</span>
                    </div>
                </div>
                <p class="text-[10px] font-mono font-bold mb-3 bg-white border-2 border-[#171717] p-2">{{ $rek['catatan_cakupan'] }}</p>
                <ul class="space-y-2.5">
                    @foreach($rek['aksi'] as $a)
                    <li class="flex gap-2">
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                        <div class="min-w-0">
                            <span class="block font-mono font-black text-[10px] uppercase tracking-wide">{{ $a['dinas'] }}</span>
                            <span class="block text-[10px] font-mono font-bold text-neutral-700 leading-snug">{{ $a['aksi'] }}</span>
                            <span class="block text-[8px] font-mono font-bold text-neutral-400 mt-0.5 italic">Dasar: {{ $a['dasar'] }}</span>
                            @if(!empty($a['narasi']))
                            <details class="mt-1.5 group">
                                <summary class="cursor-pointer list-none inline-flex items-center gap-1 text-[8px] font-mono font-black uppercase tracking-widest text-[#171717] bg-white border-2 border-[#171717] px-2 py-0.5 select-none">
                                    <i data-lucide="chevron-down" class="w-3 h-3 transition-transform group-open:rotate-180" aria-hidden="true"></i>
                                    Penjelasan
                                </summary>
                                <p class="text-[10px] font-mono font-medium text-neutral-600 leading-relaxed mt-2 text-justify">{{ $a['narasi'] }}</p>
                                @if(!empty($a['penjelasan_korelasi']))
                                <p class="text-[9px] font-mono font-bold text-[#171717] leading-relaxed mt-2 bg-white border-l-4 border-[#171717] pl-2 py-1">{{ $a['penjelasan_korelasi'] }}</p>
                                @endif
                            </details>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('rekomendasi.cetak', ['kecamatan' => $rek['kecamatan']]) }}" target="_blank" class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[#171717] text-white font-mono font-black uppercase text-[10px] tracking-widest border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                    <i data-lucide="printer" class="w-4 h-4" aria-hidden="true"></i>
                    Cetak Detail
                </a>
            </div>
            @empty
            <div class="text-center py-10 bg-neutral-100 border-2 border-[#171717] border-dashed">
                <i data-lucide="check-circle" class="h-8 w-8 text-neutral-400 mx-auto mb-2" aria-hidden="true"></i>
                <p class="text-neutral-600 font-mono font-bold text-[10px] uppercase">Tidak ada kecamatan prioritas tinggi.</p>
            </div>
            @endforelse
        </div>
        <p class="text-[9px] font-mono font-bold text-neutral-500 mt-4 leading-relaxed border-t-2 border-[#171717] pt-3">Rekomendasi otomatis dari faktor korelasi terkuat (&gt; 0.4), bersifat pendukung keputusan. Verifikasi lapangan tetap diperlukan.</p>
    </div>

    @endif
</div>
@endsection
