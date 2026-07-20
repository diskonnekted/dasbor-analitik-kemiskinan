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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body class="bg-white text-secondary font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-slate-900 text-white shadow-md z-50">
            <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('landing') }}" class="text-3xl font-black tracking-tighter text-white hover:text-slate-200 transition-colors">VEDA</a>
                    <div class="h-6 w-px bg-slate-600"></div>
                    <span class="text-[10px] font-semibold tracking-widest uppercase text-slate-400">
                        Visual Economics<br>Dashboard Analytic
                    </span>
                </div>
                <nav class="flex flex-wrap justify-center gap-2 md:gap-6 text-sm font-medium">
                    <a href="{{ route('dashboard') }}" class="hover:text-white text-white/80 transition-colors {{ request()->routeIs('dashboard') ? 'text-white font-bold border-b-2 border-white pb-1' : '' }}">Dasbor Spasial</a>
                    <a href="{{ route('analisa') }}" class="hover:text-white text-white/80 transition-colors {{ request()->routeIs('analisa') ? 'text-white font-bold border-b-2 border-white pb-1' : '' }}">Analisa Diagnostik</a>
                    <a href="{{ route('klaster') }}" class="hover:text-white text-white/80 transition-colors {{ request()->routeIs('klaster') ? 'text-white font-bold border-b-2 border-white pb-1' : '' }}">Analisa Klaster</a>
                    <a href="{{ route('prediksi') }}" class="hover:text-white text-white/80 transition-colors {{ request()->routeIs('prediksi') ? 'text-white font-bold border-b-2 border-white pb-1' : '' }}">Proyeksi Masa Depan</a>
                    <a href="{{ route('simulasi') }}" class="hover:text-white text-white/80 transition-colors {{ request()->routeIs('simulasi') ? 'text-white font-bold border-b-2 border-white pb-1' : '' }}">Simulasi Kebijakan</a>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow w-full mx-auto p-0">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-secondary text-white text-center py-4">
            <p>&copy; 2026 Pemerintah Kabupaten Banjarnegara</p>
        </footer>
    </div>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @stack('scripts')
</body>
</html>
