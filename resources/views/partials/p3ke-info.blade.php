@php
    $p3keText = $text ?? 'Desil menunjukkan tingkatan kesejahteraan, bukan desil statistik. Desil 1 adalah kelompok dengan kesejahteraan terendah. Sumber: P3KE (Pensasaran Percepatan Penghapusan Kemiskinan Ekstrem), Bappeda Kabupaten Banjarnegara.';
@endphp
<span x-data="{ open: false }" class="relative inline-flex align-middle ml-1.5">
    <button type="button"
        @mouseenter="open = true" @mouseleave="open = false"
        @focus="open = true" @blur="open = false"
        @click="open = !open" @click.away="open = false"
        class="inline-flex items-center justify-center w-4 h-4 rounded-full border border-hairline-strong text-muted hover:text-ink hover:border-ink transition-colors bg-transparent"
        aria-label="Penjelasan sumber data P3KE"
        title="Sumber: P3KE — Bappeda Kabupaten Banjarnegara">
        <svg viewBox="0 0 24 24" fill="none" class="w-3 h-3" aria-hidden="true">
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/>
            <path d="M12 10.5v5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            <circle cx="12" cy="7.6" r="1.1" fill="currentColor"/>
        </svg>
    </button>
    <div x-show="open" x-cloak x-transition.opacity.duration.150ms
        class="absolute z-[1200] w-72 -translate-x-1/2 left-1/2 bottom-full mb-2 panel px-3.5 py-3 shadow-lg"
        role="tooltip">
        <p class="body-xs text-body mb-1.5"><strong class="font-medium text-ink">Desil 1 — kesejahteraan terendah.</strong></p>
        <p class="body-xs text-muted leading-relaxed">{{ $p3keText }}</p>
    </div>
</span>