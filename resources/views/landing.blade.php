<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banjarnegara Poverty Clock</title>

    <!-- Fonts: Inter (CursorGothic substitute) + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&family=jetbrains-mono:400,500&display=swap" rel="stylesheet" />

    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .number-display {
            font-size: clamp(3.5rem, 11vw, 7rem);
            font-weight: 400;
            color: #26251e;
            line-height: 1.05;
            letter-spacing: -0.03em;
            font-variant-numeric: tabular-nums;
        }
        .donut-ring { transition: stroke-dashoffset 0.5s ease-in-out; }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-canvas text-ink font-sans" x-data="povertyClock()">

    <!-- ============ Top Navigation ============ -->
    <header class="w-full border-b border-hairline z-50 bg-canvas">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <span class="text-[22px] font-medium tracking-tight">VEDA<span class="text-primary">.</span></span>
                <span class="hidden sm:block h-5 w-px bg-hairline-strong"></span>
                <span class="hidden sm:block caption-uppercase text-muted">Poverty Clock</span>
            </a>

            <nav class="hidden md:flex items-center gap-6 nav-link">
                <a href="{{ route('dashboard') }}" class="text-body hover:text-primary transition-colors">Spasial</a>
                <a href="{{ route('analisa') }}" class="text-body hover:text-primary transition-colors">Diagnostik</a>
                <a href="{{ route('klaster') }}" class="text-body hover:text-primary transition-colors">Klaster</a>
                <a href="{{ route('prediksi') }}" class="text-body hover:text-primary transition-colors">Proyeksi</a>
                <a href="{{ route('simulasi') }}" class="text-body hover:text-primary transition-colors">Simulasi</a>
            </nav>

            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('landing') }}">
                    <select name="kecamatan" onchange="this.form.submit()" class="select h-10 w-52">
                        <option value="Semua">Kabupaten Banjarnegara</option>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec }}" {{ $selectedKecamatan === $kec ? 'selected' : '' }}>Kecamatan {{ ucwords(strtolower($kec)) }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('landing') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </header>

    <!-- ============ Hero ============ -->
    <main class="flex-grow flex flex-col items-center justify-center px-4 w-full">

        <div class="w-full max-w-[1200px] mx-auto py-14 flex flex-col items-center">

            <!-- AI timeline signature pills (analysis pipeline stages) -->
            <div class="flex flex-wrap items-center justify-center gap-2 mb-10">
                <span class="timeline-pill timeline-thinking">Kumpul</span>
                <span class="text-muted-soft text-sm" aria-hidden="true">→</span>
                <span class="timeline-pill timeline-grep">Saring</span>
                <span class="text-muted-soft text-sm" aria-hidden="true">→</span>
                <span class="timeline-pill timeline-read">Baca</span>
                <span class="text-muted-soft text-sm" aria-hidden="true">→</span>
                <span class="timeline-pill timeline-edit">Analisis</span>
                <span class="text-muted-soft text-sm" aria-hidden="true">→</span>
                <span class="timeline-pill timeline-done">Selesai</span>
            </div>

            <!-- Donut + number -->
            <div class="relative flex items-center justify-center w-full max-w-[460px] h-[300px] sm:h-[360px] mb-4">
                <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" fill="none" stroke="#e6e5e0" stroke-width="20" />
                    <circle cx="50" cy="50" r="40" fill="none" stroke="#f54e00" stroke-width="20"
                            stroke-dasharray="251.2"
                            :stroke-dashoffset="getStrokeOffset()"
                            class="donut-ring" />
                </svg>

                <div class="absolute top-8 left-[58%] flex items-center gap-2">
                    <div class="w-3.5 h-3.5 rounded-full bg-primary"></div>
                    <div>
                        <div class="caption-uppercase text-muted">"OFF-TRACK" SDG1</div>
                        <div class="title-sm text-primary tabular" x-text="new Intl.NumberFormat('id-ID').format(currentValue)"></div>
                    </div>
                </div>

                <div class="absolute flex flex-col items-center z-10 px-4">
                    <div class="number-display" x-text="new Intl.NumberFormat('id-ID').format(animatedValue)"></div>
                    <div class="caption-uppercase text-muted text-center mt-3 leading-relaxed max-w-xs">
                        Penduduk di {{ $selectedKecamatan && $selectedKecamatan !== 'Semua' ? 'Kec. ' . ucwords(strtolower($selectedKecamatan)) : 'Banjarnegara' }}
                        yang hidup dalam kemiskinan ekstrem
                    </div>
                </div>
            </div>

            <p class="body-md text-body mt-6 max-w-xl text-center">
                Estimasi real-time berbasis data kesejahteraan penduduk per kecamatan.
            </p>
        </div>

        <!-- ============ 4 Metrics ============ -->
        <div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 pb-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $metricItems = [
                        ['label' => 'Target Escape Rate', 'value' => $metrics['targetRate'].' orang/detik', 'accent' => 'success', 'symbol' => '◷'],
                        ['label' => 'Escaped Poverty Today', 'value' => $metrics['escapedToday'], 'accent' => 'ink', 'symbol' => '↑'],
                        ['label' => 'Current Escape Rate', 'value' => $metrics['currentRate'].' orang/detik', 'accent' => 'primary', 'symbol' => '◆'],
                        ['label' => 'Fell Into Poverty Today', 'value' => $metrics['fellToday'], 'accent' => 'error', 'symbol' => '↓'],
                    ];
                @endphp
                @foreach($metricItems as $m)
                <div class="feature-card flex items-start gap-4">
                    <span class="flex-shrink-0 text-2xl {{ $m['accent'] === 'success' ? 'text-success' : ($m['accent'] === 'primary' ? 'text-primary' : ($m['accent'] === 'error' ? 'text-error' : 'text-ink')) }}">{{ $m['symbol'] }}</span>
                    <div>
                        <div class="caption-uppercase text-muted mb-1">{{ $m['label'] }}</div>
                        <div class="title-md text-ink tabular">{{ $m['value'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </main>

    <!-- ============ Timeline Slider ============ -->
    <div class="w-full mt-auto bg-canvas border-t border-hairline py-14 px-4 sm:px-10">
        <div class="max-w-[1000px] mx-auto relative">
            <div class="section-label mb-8">Garis Waktu Kemiskinan</div>

            <div class="relative pt-3">
                <div class="absolute top-0 px-3 py-1 bg-primary text-on-primary text-sm font-medium rounded-md transform -translate-x-1/2 transition-all duration-200 tabular"
                     :style="'left: ' + getSliderPercentage() + '%'"
                     x-text="selectedYear">
                    2024
                </div>

                <input type="range"
                       class="w-full"
                       min="{{ min(array_keys($timelineData)) }}"
                       max="{{ max(array_keys($timelineData)) }}"
                       step="1"
                       x-model="selectedYear"
                       @input="updateValue()">

                <div class="flex justify-between mt-4 caption-uppercase text-muted relative">
                    @foreach(array_keys($timelineData) as $yr)
                        <span class="{{ $yr > $tahunSekarang ? 'text-primary' : '' }}">
                            {{ $yr }} {{ $yr > $tahunSekarang ? '*' : '' }}
                        </span>
                    @endforeach
                </div>

                <div class="text-center mt-8 body-sm text-muted">
                    <span class="text-primary">*</span> Data tahun <span class="font-medium text-ink">{{ $tahunSekarang + 1 }}</span> dan seterusnya merupakan proyeksi statistik berdasarkan tren historis.
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="border-t border-hairline py-6">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 flex flex-col md:flex-row items-center justify-between gap-2 body-sm text-muted">
            <span>©2026 Pemerintah Kabupaten Banjarnegara</span>
            <span class="text-muted-soft">VEDA<span class="text-primary">.</span> / Poverty Clock / Banjarnegara</span>
        </div>
    </footer>

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
                    const years = Object.keys(timelineData);
                    const minY = Math.min(...years);
                    const maxY = Math.max(...years);
                    return ((this.selectedYear - minY) / (maxY - minY)) * 100;
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
</body>
</html>