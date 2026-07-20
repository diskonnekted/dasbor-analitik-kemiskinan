<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Dasbor Analitik Kemiskinan') - Pemerintah Kabupaten Banjarnegara</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Portal data terpadu dan visualisasi spasial untuk analisis kemiskinan di Kabupaten Banjarnegara berbasis Open Data.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#f4f4f0] flex flex-col min-h-screen text-[#171717] font-sans" id="veda-mobile-shell">
    <a href="#main-content" class="skip-to-content">Lewati ke konten</a>

    <!-- Top Bar -->
    <header class="sticky top-0 z-[1000] bg-[#f4f4f0] border-b-2 border-[#171717] px-4 py-3 flex items-center justify-between gap-3 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2" aria-label="VEDA Banjarnegara — ke Dasbor">
            <div class="w-8 h-8 border-2 border-[#171717] bg-white shadow-[2px_2px_0px_0px_#171717] flex items-center justify-center font-serif font-black text-base rotate-[-2deg]" aria-hidden="true">V</div>
            <div class="leading-none">
                <span class="font-serif font-black text-lg tracking-tighter uppercase block">VEDA</span>
                <span class="text-[8px] font-mono font-bold text-neutral-500 uppercase tracking-widest block">Kab. Banjarnegara</span>
            </div>
        </a>
        <span class="text-[9px] font-mono font-black uppercase tracking-widest px-2 py-1 border-2 border-[#171717] bg-white shadow-[2px_2px_0px_0px_#171717]">@yield('pagelabel', 'Dasbor')</span>
    </header>

    <noscript>
        <div class="bg-[#171717] text-[#f4f4f0] text-center text-[10px] font-mono font-bold uppercase tracking-widest px-4 py-2 border-b-2 border-[#171717]">
            Aplikasi ini memerlukan JavaScript aktif untuk peta & grafik interaktif.
        </div>
    </noscript>

    <!-- Main Content -->
    <main id="main-content" class="flex-1 w-full pb-24 bg-[#f4f4f0]">
        @yield('content')
    </main>

    <!-- Bottom Navigation -->
    @php
        $navItems = [
            ['route' => 'dashboard', 'label' => 'Dasbor', 'icon' => 'layout-dashboard'],
            ['route' => 'rekomendasi', 'label' => 'Kebijakan', 'icon' => 'gavel'],
            ['route' => 'makro', 'label' => 'Makro', 'icon' => 'globe-2'],
            ['route' => 'analisa', 'label' => 'Korelasi', 'icon' => 'line-chart'],
            ['route' => 'klaster', 'label' => 'Klaster', 'icon' => 'brain'],
            ['route' => 'prediksi', 'label' => 'Proyeksi', 'icon' => 'trending-up'],
            ['route' => 'simulasi', 'label' => 'Simulasi', 'icon' => 'sliders'],
        ];
    @endphp
    <nav class="fixed bottom-0 inset-x-0 z-[1000] bg-[#f4f4f0] border-t-2 border-[#171717] flex items-stretch overflow-x-auto pb-[env(safe-area-inset-bottom)]" style="position:fixed;bottom:0;left:0;right:0;-webkit-overflow-scrolling:touch;" aria-label="Navigasi utama">
        @foreach($navItems as $item)
            @php $active = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               @if($active) aria-current="page" @endif
               class="flex-1 shrink-0 basis-[64px] flex flex-col items-center justify-center gap-1 py-2.5 px-1 text-center {{ $active ? 'bg-[#171717] text-white' : 'text-[#171717]' }}">
                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5" aria-hidden="true"></i>
                <span class="text-[9px] font-mono font-black uppercase tracking-wider">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
