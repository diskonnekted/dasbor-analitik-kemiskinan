<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dasbor Analitik Kemiskinan') - Pemerintah Kabupaten Banjarnegara</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Portal data terpadu dan visualisasi spasial untuk analisis kemiskinan di Kabupaten Banjarnegara berbasis Open Data.">
    <meta property="og:title" content="Dasbor Analitik Kemiskinan Banjarnegara">
    <meta property="og:description" content="Visualisasi data kesejahteraan penduduk per kecamatan.">
    <meta property="og:type" content="website">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-[#f4f4f0] flex flex-col h-screen overflow-hidden text-[#171717] font-sans" id="veda-workspace-shell">
    <!-- Header Ribbon -->
    <nav class="sticky top-0 z-50 bg-[#f4f4f0] border-b-2 border-[#171717] px-6 py-4 flex flex-wrap items-center justify-between gap-4 shrink-0 relative shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 border-2 border-[#171717] bg-white overflow-hidden shadow-[2px_2px_0px_0px_#171717] flex items-center justify-center font-serif font-black text-lg rotate-[-2deg]">
                V
            </div>
            <div>
                <span class="font-serif font-black text-2xl tracking-tighter uppercase text-[#171717] mt-1 block leading-none">
                    VEDA
                </span>
                <span class="text-[9px] font-mono font-bold text-neutral-500 uppercase tracking-widest block mt-0.5">Kab. Banjarnegara</span>
            </div>
        </div>

        <nav class="flex items-center gap-6">
            <a href="{{ route('dashboard') }}" class="text-xs font-bold uppercase tracking-wider hover:underline decoration-2 underline-offset-4 {{ request()->routeIs('dashboard') ? 'underline decoration-2' : '' }}">Dasbor</a>
            <a href="{{ route('analisa') }}" class="text-xs font-bold uppercase tracking-wider hover:underline decoration-2 underline-offset-4 {{ request()->routeIs('analisa') ? 'underline decoration-2' : '' }}">Korelasi</a>
            <a href="{{ route('klaster') }}" class="text-xs font-bold uppercase tracking-wider hover:underline decoration-2 underline-offset-4 {{ request()->routeIs('klaster') ? 'underline decoration-2' : '' }}">Klaster</a>
            <a href="{{ route('prediksi') }}" class="text-xs font-bold uppercase tracking-wider hover:underline decoration-2 underline-offset-4 {{ request()->routeIs('prediksi') ? 'underline decoration-2' : '' }}">Proyeksi</a>
            <a href="{{ route('simulasi') }}" class="text-xs font-bold uppercase tracking-wider hover:underline decoration-2 underline-offset-4 {{ request()->routeIs('simulasi') ? 'underline decoration-2' : '' }}">Simulasi</a>
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('landing') }}" class="text-xs font-bold uppercase tracking-wider px-4 py-2 border-2 border-[#171717] bg-white shadow-[2px_2px_0px_0px_#171717] hover:shadow-[4px_4px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">
                Keluar
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 flex overflow-hidden w-full m-0 p-0 relative bg-[#f4f4f0]">
        @yield('content')
    </main>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
