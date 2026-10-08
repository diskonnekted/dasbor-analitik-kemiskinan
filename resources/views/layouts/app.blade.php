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

    <!-- Fonts: Inter (CursorGothic substitute, 400/500/600) + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&family=jetbrains-mono:400,500&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- ArcGIS Maps SDK for JavaScript (Esri) -->
    <link rel="stylesheet" href="https://js.arcgis.com/4.31/esri/themes/light/main.css" />
</head>
<body class="bg-canvas text-ink font-sans antialiased">
    <div class="min-h-screen flex flex-col" x-data="{ open: false }">

        <!-- ============ Top Navigation (64px) ============ -->
        <header class="bg-canvas border-b border-hairline z-50 sticky top-0">
            <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
                <!-- Wordmark + brand accent -->
                <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                    <span class="text-[22px] font-medium tracking-tight text-ink">VEDA<span class="text-primary">.</span></span>
                    <span class="hidden lg:block h-5 w-px bg-hairline-strong"></span>
                    <span class="hidden lg:block caption-uppercase text-muted">
                        Visual Economics<br>Dashboard Analytic
                    </span>
                </a>

                <!-- Primary horizontal menu -->
                <nav class="hidden md:flex items-center gap-6 nav-link">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-ink' : 'text-body hover:text-ink' }} transition-colors">Spasial</a>
                    <a href="{{ route('analisa') }}" class="{{ request()->routeIs('analisa') ? 'text-ink' : 'text-body hover:text-ink' }} transition-colors">Diagnostik</a>
                    <a href="{{ route('klaster') }}" class="{{ request()->routeIs('klaster') ? 'text-ink' : 'text-body hover:text-ink' }} transition-colors">Klaster</a>
                    <a href="{{ route('prioritas') }}" class="{{ request()->routeIs('prioritas') ? 'text-ink' : 'text-body hover:text-ink' }} transition-colors">Prioritas</a>
                    <a href="{{ route('prediksi') }}" class="{{ request()->routeIs('prediksi') ? 'text-ink' : 'text-body hover:text-ink' }} transition-colors">Proyeksi</a>
                    <a href="{{ route('simulasi') }}" class="{{ request()->routeIs('simulasi') ? 'text-ink' : 'text-body hover:text-ink' }} transition-colors">Simulasi</a>
                </nav>

                <!-- CTA cluster -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('export.csv') }}" class="btn btn-primary">
                        <span aria-hidden="true">↓</span><span>Unduh Data</span>
                    </a>
                    <button class="md:hidden btn btn-secondary px-3" @click="open = !open" aria-label="Menu">
                        <span aria-hidden="true" x-text="open ? '[x]' : '[≡]'">[≡]</span>
                    </button>
                </div>
            </div>

            <!-- Mobile drawer -->
            <nav x-show="open" x-cloak @click.away="open = false" class="md:hidden border-t border-hairline bg-canvas px-4 py-2 flex flex-col nav-link">
                <a href="{{ route('dashboard') }}" class="py-3 border-b border-hairline-soft {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-body' }}">Spasial</a>
                <a href="{{ route('analisa') }}" class="py-3 border-b border-hairline-soft {{ request()->routeIs('analisa') ? 'text-primary' : 'text-body' }}">Diagnostik</a>
                <a href="{{ route('klaster') }}" class="py-3 border-b border-hairline-soft {{ request()->routeIs('klaster') ? 'text-primary' : 'text-body' }}">Klaster</a>
                <a href="{{ route('prioritas') }}" class="py-3 border-b border-hairline-soft {{ request()->routeIs('prioritas') ? 'text-primary' : 'text-body' }}">Prioritas</a>
                <a href="{{ route('prediksi') }}" class="py-3 border-b border-hairline-soft {{ request()->routeIs('prediksi') ? 'text-primary' : 'text-body' }}">Proyeksi</a>
                <a href="{{ route('simulasi') }}" class="py-3 {{ request()->routeIs('simulasi') ? 'text-primary' : 'text-body' }}">Simulasi</a>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="flex-grow w-full mx-auto p-0">
            @yield('content')
        </main>

        <!-- ============ Footer ============ -->
        <footer class="bg-canvas border-t border-hairline">
            <div class="w-full mx-auto px-4 sm:px-6 lg:px-10 py-12">
                <!-- 5-column link list -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-y-8 mb-12">
                    <div>
                        <p class="caption-uppercase text-muted mb-4">Analisis</p>
                        <ul class="space-y-2 body-sm">
                            <li><a href="{{ route('dashboard') }}" class="text-body hover:text-primary transition-colors">Dasbor Spasial</a></li>
                            <li><a href="{{ route('analisa') }}" class="text-body hover:text-primary transition-colors">Diagnostik &amp; Korelasi</a></li>
                            <li><a href="{{ route('klaster') }}" class="text-body hover:text-primary transition-colors">Klaster K-Means</a></li>
                            <li><a href="{{ route('prioritas') }}" class="text-body hover:text-primary transition-colors">Prioritas &amp; Ketepatan Sasaran</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="caption-uppercase text-muted mb-4">Intelegensi</p>
                        <ul class="space-y-2 body-sm">
                            <li><a href="{{ route('prediksi') }}" class="text-body hover:text-primary transition-colors">Proyeksi Masa Depan</a></li>
                            <li><a href="{{ route('simulasi') }}" class="text-body hover:text-primary transition-colors">Simulasi Kebijakan</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="caption-uppercase text-muted mb-4">Data</p>
                        <ul class="space-y-2 body-sm">
                            <li><a href="{{ route('export.csv') }}" class="text-body hover:text-primary transition-colors">Unduh CSV</a></li>
                            <li><a href="{{ route('landing') }}" class="text-body hover:text-primary transition-colors">Poverty Clock</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="caption-uppercase text-muted mb-4">Institusi</p>
                        <ul class="space-y-2 body-sm">
                            <li><a href="#" class="text-body hover:text-primary transition-colors">Bappeda Litbang</a></li>
                            <li><a href="#" class="text-body hover:text-primary transition-colors">Dinsos PPPA</a></li>
                            <li><a href="#" class="text-body hover:text-primary transition-colors">BPS Banjarnegara</a></li>
                        </ul>
                    </div>
                    <div>
                        <p class="caption-uppercase text-muted mb-4">Platform</p>
                        <ul class="space-y-2 body-sm">
                            <li><a href="{{ route('landing') }}" class="text-body hover:text-primary transition-colors">VEDA<span class="text-primary">.</span></a></li>
                            <li><span class="text-muted-soft">Banjarnegara, Jawa Tengah</span></li>
                        </ul>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-3 pt-6 border-t border-hairline-soft">
                    <p class="body-sm text-muted">©2026 Pemerintah Kabupaten Banjarnegara</p>
                    <div class="flex items-center gap-4 body-sm text-muted">
                        <a href="{{ route('landing') }}" class="hover:text-primary transition-colors">Beranda</a>
                        <span class="text-muted-soft">·</span>
                        <a href="{{ route('export.csv') }}" class="hover:text-primary transition-colors">Data Terbuka</a>
                        <span class="text-muted-soft">·</span>
                        <span class="text-muted-soft">ID</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>