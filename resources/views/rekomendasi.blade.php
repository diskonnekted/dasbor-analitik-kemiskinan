@extends('layouts.app')

@section('title', 'Rekomendasi Kebijakan Berbasis Data')

@section('content')
<div class="flex-grow overflow-y-auto w-full py-8 px-6 sm:px-8 space-y-8 bg-[#f4f4f0] text-[#171717]">

    <!-- Hero Header -->
    <div class="bg-white border-2 border-[#171717] p-8 md:p-10 shadow-[4px_4px_0px_0px_#171717] flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#171717] text-white text-[10px] font-mono font-bold tracking-widest uppercase mb-4 border border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <i data-lucide="gavel" class="w-3.5 h-3.5" aria-hidden="true"></i>
                Decision Support System
            </div>
            <h1 class="text-3xl md:text-4xl font-serif font-black text-[#171717] uppercase tracking-tight mb-2">Rekomendasi Kebijakan</h1>
            <p class="text-neutral-600 font-mono font-bold text-xs md:text-sm leading-relaxed">Ringkasan eksekutif untuk pengambilan keputusan berbasis data: kecamatan prioritas, faktor pendorong kemiskinan, dan arahan intervensi lintas dinas (Tahun {{ $maxTahun ?? '-' }}).</p>
        </div>

        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-[#171717] font-mono font-black uppercase text-xs border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] hover:shadow-[4px_4px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">
            <span>Dasbor Utama</span>
            <i data-lucide="arrow-right" class="w-4 h-4" aria-hidden="true"></i>
        </a>
    </div>

    @if(isset($error))
    <div class="bg-white text-[#171717] p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] font-mono font-bold">
        {{ $error }}
    </div>
    @else

    <!-- Ringkasan Kabupaten -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1 bg-[#171717] text-white p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
            <h3 class="font-mono text-neutral-300 font-black mb-2 text-[10px] uppercase tracking-widest">Total Sangat Miskin</h3>
            <p class="text-3xl font-mono font-black">{{ number_format($ringkasan['total_sangat_miskin'], 0, ',', '.') }} <span class="text-xs font-bold text-neutral-400">Jiwa</span></p>
        </div>
        <div class="bg-white p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
            <h3 class="text-neutral-500 font-black mb-2 text-[10px] uppercase tracking-widest font-mono">Kecamatan Prioritas Tinggi</h3>
            <p class="text-3xl font-mono font-black text-[#171717]">{{ $ringkasan['jml_prioritas_tinggi'] }} <span class="text-xs font-bold text-neutral-500">/ {{ $ringkasan['jml_kecamatan'] }} Kec.</span></p>
        </div>
        <div class="bg-white p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
            <h3 class="text-neutral-500 font-black mb-2 text-[10px] uppercase tracking-widest font-mono">Cakupan Penerima Bansos</h3>
            <p class="text-3xl font-mono font-black text-[#171717]">{{ number_format($ringkasan['total_penerima'], 0, ',', '.') }} <span class="text-xs font-bold text-neutral-500">KK</span></p>
        </div>
        <div class="bg-white p-6 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
            <h3 class="text-neutral-500 font-black mb-2 text-[10px] uppercase tracking-widest font-mono">Kemiskinan Kabupaten</h3>
            <p class="text-3xl font-mono font-black text-[#171717]">{{ $ringkasan['persentase_kab'] !== null ? number_format($ringkasan['persentase_kab'], 2, ',', '.') . '%' : 'N/A' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

        <!-- Peringkat Prioritas Kecamatan -->
        <div class="xl:col-span-7 bg-white p-6 md:p-8 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col">
            <h2 class="text-lg font-serif font-black text-[#171717] mb-6 flex items-center gap-2 uppercase border-b-2 border-[#171717] pb-2">
                <i data-lucide="list-ordered" class="w-5 h-5" aria-hidden="true"></i>
                Peringkat Prioritas Intervensi
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b-2 border-[#171717]">
                            <th class="text-[9px] font-mono font-black uppercase tracking-widest pb-2 pr-2">#</th>
                            <th class="text-[9px] font-mono font-black uppercase tracking-widest pb-2 pr-2">Kecamatan</th>
                            <th class="text-[9px] font-mono font-black uppercase tracking-widest pb-2 pr-2 text-right">Sangat Miskin</th>
                            <th class="text-[9px] font-mono font-black uppercase tracking-widest pb-2 pr-2 text-right">Skor</th>
                            <th class="text-[9px] font-mono font-black uppercase tracking-widest pb-2 text-center">Prioritas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $i => $r)
                        @php
                            $badge = $r['prioritas'] === 'Tinggi'
                                ? 'bg-[#171717] text-white'
                                : ($r['prioritas'] === 'Menengah' ? 'bg-[#a3a398] text-[#171717]' : 'bg-[#e5e5d8] text-[#171717]');
                        @endphp
                        <tr class="border-b border-[#171717]/20">
                            <td class="py-3 pr-2 font-mono font-black text-sm">{{ $i + 1 }}</td>
                            <td class="py-3 pr-2 font-serif font-black uppercase text-sm">{{ ucwords(strtolower($r['kecamatan'])) }}</td>
                            <td class="py-3 pr-2 font-mono font-black text-sm text-right">{{ number_format($r['sangat_miskin'], 0, ',', '.') }}</td>
                            <td class="py-3 pr-2 font-mono font-black text-sm text-right">{{ $r['skor'] }}</td>
                            <td class="py-3 text-center">
                                <span class="inline-block text-[9px] font-mono font-black uppercase tracking-wider px-2 py-1 border-2 border-[#171717] {{ $badge }}">{{ $r['prioritas'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-[10px] font-mono font-bold text-neutral-500 mt-4 leading-relaxed">Skor = 70% beban kemiskinan ekstrem (desil 1) + 30% kesenjangan cakupan bantuan. Makin tinggi skor, makin mendesak intervensi.</p>
        </div>

        <!-- Faktor Pendorong -->
        <div class="xl:col-span-5 bg-white p-6 md:p-8 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col">
            <h2 class="text-lg font-serif font-black text-[#171717] mb-6 flex items-center gap-2 uppercase border-b-2 border-[#171717] pb-2">
                <i data-lucide="activity" class="w-5 h-5" aria-hidden="true"></i>
                Faktor Pendorong Kemiskinan
            </h2>

            <div class="space-y-3">
                @forelse($faktor as $f)
                <div class="p-4 bg-white border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <h4 class="font-mono font-black text-[11px] uppercase tracking-tight">{{ $f['indikator'] }}</h4>
                        <span class="font-mono font-black text-sm">{{ number_format($f['korelasi'], 2) }}</span>
                    </div>
                    <div class="w-full bg-neutral-100 h-3 border-2 border-[#171717] overflow-hidden">
                        <div class="h-full bg-[#171717]" style="width: {{ min(100, $f['abs'] * 100) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-[9px] font-mono font-bold uppercase tracking-widest text-neutral-500">{{ $f['kategori'] }} · {{ $f['arah'] }}</span>
                        <span class="text-[9px] font-mono font-black uppercase tracking-widest bg-[#f4f4f0] border border-[#171717] px-1.5">{{ $f['kekuatan'] }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 bg-neutral-100 border-2 border-[#171717] border-dashed">
                    <i data-lucide="database" class="h-10 w-10 text-neutral-400 mx-auto mb-3" aria-hidden="true"></i>
                    <p class="text-neutral-600 font-mono font-bold text-xs uppercase">Belum ada faktor untuk dianalisa.</p>
                </div>
                @endforelse
            </div>
            <p class="text-[10px] font-mono font-bold text-neutral-500 mt-4 leading-relaxed">Korelasi Pearson tiap indikator terhadap jumlah penduduk sangat miskin per kecamatan. Positif = searah, Negatif = berlawanan.</p>
        </div>
    </div>

    <!-- Rekomendasi Otomatis -->
    <div class="bg-white p-6 md:p-8 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <h2 class="text-lg font-serif font-black text-[#171717] mb-6 flex items-center gap-2 uppercase border-b-2 border-[#171717] pb-2">
            <i data-lucide="clipboard-check" class="w-5 h-5" aria-hidden="true"></i>
            Arahan Intervensi — Kecamatan Prioritas Tinggi
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @forelse($rekomendasi as $rek)
            <div class="relative bg-[#f4f4f0] p-5 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <div class="flex items-center justify-between gap-3 mb-3 pb-3 border-b-2 border-[#171717]">
                    <h3 class="font-serif font-black text-lg uppercase">{{ ucwords(strtolower($rek['kecamatan'])) }}</h3>
                    <div class="text-right">
                        <span class="block font-mono font-black text-sm">{{ number_format($rek['sangat_miskin'], 0, ',', '.') }} Jiwa</span>
                        <span class="block text-[9px] font-mono font-bold uppercase tracking-widest text-neutral-500">Skor {{ $rek['skor'] }}</span>
                    </div>
                </div>

                <p class="text-[11px] font-mono font-bold text-[#171717] mb-4 bg-white border-2 border-[#171717] p-2">{{ $rek['catatan_cakupan'] }}</p>

                <ul class="space-y-3">
                    @foreach($rek['aksi'] as $a)
                    <li class="flex gap-3">
                        <i data-lucide="chevron-right" class="w-4 h-4 mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                        <div class="min-w-0">
                            <span class="block font-mono font-black text-[11px] uppercase tracking-wide">{{ $a['dinas'] }}</span>
                            <span class="block text-[11px] font-mono font-bold text-neutral-700 leading-snug">{{ $a['aksi'] }}</span>
                            <span class="block text-[9px] font-mono font-bold text-neutral-400 mt-0.5 italic">Dasar: {{ $a['dasar'] }}</span>
                            @if(!empty($a['narasi']))
                            <details class="mt-1.5 group">
                                <summary class="cursor-pointer list-none inline-flex items-center gap-1 text-[9px] font-mono font-black uppercase tracking-widest text-[#171717] bg-white border-2 border-[#171717] px-2 py-0.5 shadow-[1px_1px_0px_0px_#171717] select-none">
                                    <i data-lucide="chevron-down" class="w-3 h-3 transition-transform group-open:rotate-180" aria-hidden="true"></i>
                                    Penjelasan
                                </summary>
                                <p class="text-[10px] font-mono font-medium text-neutral-600 leading-relaxed mt-2 text-justify">{{ $a['narasi'] }}</p>
                                @if(!empty($a['penjelasan_korelasi']))
                                <p class="text-[10px] font-mono font-bold text-[#171717] leading-relaxed mt-2 bg-white border-l-4 border-[#171717] pl-2 py-1">{{ $a['penjelasan_korelasi'] }}</p>
                                @endif
                            </details>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>

                <a href="{{ route('rekomendasi.cetak', ['kecamatan' => $rek['kecamatan']]) }}" target="_blank" class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#171717] text-white font-mono font-black uppercase text-[10px] tracking-widest border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] hover:shadow-[4px_4px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">
                    <i data-lucide="printer" class="w-4 h-4" aria-hidden="true"></i>
                    Cetak Detail Rekomendasi
                </a>
            </div>
            @empty
            <div class="lg:col-span-2 text-center py-12 bg-neutral-100 border-2 border-[#171717] border-dashed">
                <i data-lucide="check-circle" class="h-10 w-10 text-neutral-400 mx-auto mb-3" aria-hidden="true"></i>
                <p class="text-neutral-600 font-mono font-bold text-xs uppercase">Tidak ada kecamatan prioritas tinggi.</p>
            </div>
            @endforelse
        </div>

        <p class="text-[10px] font-mono font-bold text-neutral-500 mt-6 leading-relaxed border-t-2 border-[#171717] pt-4">Rekomendasi dihasilkan otomatis dari faktor korelasi terkuat (&gt; 0.4) dan bersifat pendukung keputusan. Verifikasi lapangan tetap diperlukan sebelum penetapan kebijakan.</p>
    </div>

    @endif
</div>
@endsection
