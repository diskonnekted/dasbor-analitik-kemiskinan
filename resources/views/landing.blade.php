<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banjarnegara Poverty Clock</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { 
            font-family: 'Montserrat', sans-serif; 
            background-color: #ffffff;
            color: #333333;
        }
        
        .number-display {
            font-size: 7rem;
            font-weight: 900;
            color: #ef4423; /* Orange/Red from screenshot */
            line-height: 1;
            letter-spacing: -2px;
            -webkit-text-stroke: 10px #ffffff;
            paint-order: stroke fill;
        }

        .metric-title {
            font-size: 0.65rem;
            letter-spacing: 2px;
            color: #777;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Donut Chart Animation */
        .donut-ring {
            transition: stroke-dashoffset 0.5s ease-in-out;
        }

        /* Ribbon Style */
        .ribbon {
            position: fixed;
            right: 0;
            background: #ef4423;
            color: white;
            padding: 10px 40px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.8rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%, 15px 50%);
            transition: all 0.3s ease;
        }
        
        .ribbon:hover {
            transform: translateX(-10px);
            background: #d83a1b;
        }

        .ribbon-dark {
            background: #113624; /* Dark green from screenshot */
            top: 150px;
        }
        .ribbon-dark:hover { background: #0a2417; }
        
        .ribbon-map {
            top: 100px;
        }

        /* Timeline Slider */
        .timeline-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 4px;
            background: #e2e8f0;
            outline: none;
        }
        
        .timeline-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 40px;
            height: 25px;
            background: #ef4423;
            cursor: pointer;
            border-radius: 4px;
            position: relative;
        }
        
        .timeline-slider::-webkit-slider-thumb::after {
            content: '';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-width: 0 10px 10px 10px;
            border-color: transparent transparent #ef4423 transparent;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col relative" x-data="povertyClock()">
    
    <!-- Ribbons -->
    <a href="{{ route('dashboard') }}" class="ribbon ribbon-map z-50">Map</a>
    <a href="{{ route('analisa') }}" class="ribbon ribbon-dark z-50">Analisa</a>

    <!-- Top Navigation (Aesthetic replica) -->
    <header class="w-full px-8 py-6 flex flex-wrap items-end gap-10 border-b border-gray-100 relative z-10">
        
        <!-- Logo -->
        <div class="flex flex-col items-center">
            <div class="flex gap-1 mb-1">
                <div class="w-3 h-3 bg-green-900 rounded-full"></div>
                <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
            </div>
            <div class="text-[0.6rem] font-bold tracking-widest text-center leading-tight">
                by VEDA<br>DATA LAB
            </div>
        </div>

        <!-- Fake Filters -->
        <div class="hidden lg:flex gap-8 items-end">
            <form method="GET" action="{{ route('landing') }}">
                <div class="text-[0.6rem] tracking-[2px] font-semibold text-gray-500 uppercase mb-2">Pencarian</div>
                <div class="border-b border-gray-300 pb-1 w-48 text-sm text-gray-700">
                    <select name="kecamatan" onchange="this.form.submit()" class="w-full bg-transparent border-none p-0 focus:ring-0 cursor-pointer appearance-none">
                        <option value="Semua">Kabupaten Banjarnegara</option>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec }}" {{ $selectedKecamatan === $kec ? 'selected' : '' }}>Kecamatan {{ ucwords(strtolower($kec)) }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
            <div>
                <div class="text-[0.6rem] tracking-[2px] font-semibold text-gray-500 uppercase mb-2">Filter Geografis</div>
                <div class="flex gap-1">
                    <span class="bg-red-200 text-red-900 text-xs px-6 py-1 font-medium">pedesaan</span>
                    <span class="bg-red-200 text-red-900 text-xs px-6 py-1 font-medium">perkotaan</span>
                </div>
            </div>
            <div>
                <div class="text-[0.6rem] tracking-[2px] font-semibold text-gray-500 uppercase mb-2">Filter Demografis</div>
                <div class="flex gap-1">
                    <span class="bg-red-200 text-red-900 text-xs px-4 py-1 font-medium">wanita</span>
                    <span class="bg-red-200 text-red-900 text-xs px-4 py-1 font-medium">pria</span>
                    <span class="bg-red-200 text-red-900 text-xs px-4 py-1 font-medium">semua umur</span>
                </div>
            </div>
        </div>

        <div class="ml-auto flex items-center gap-6">
            <button class="bg-[#113624] text-white px-6 py-1.5 rounded-full text-xs font-bold tracking-wider hover:bg-black transition-colors">RESET</button>
            <button class="text-[#ef4423]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col items-center justify-center relative px-4 w-full max-w-[1200px] mx-auto py-10">
        
        <div class="w-full flex flex-col lg:flex-row items-center justify-center gap-10">
            
            <!-- Donut Chart & Number Container -->
            <div class="relative flex items-center justify-center w-[400px] h-[400px]">
                
                <!-- SVG Donut -->
                <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 100 100">
                    <!-- Background Ring (Green) -->
                    <circle cx="50" cy="50" r="40" fill="none" stroke="#0a2e1d" stroke-width="20" />
                    <!-- Foreground Ring (Orange) -->
                    <circle cx="50" cy="50" r="40" fill="none" stroke="#ef4423" stroke-width="20" 
                            stroke-dasharray="251.2" 
                            :stroke-dashoffset="getStrokeOffset()"
                            class="donut-ring" />
                </svg>

                <div class="absolute top-10 left-[60%] flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-[#ef4423]"></div>
                    <div>
                        <div class="text-[0.6rem] font-bold text-gray-500 tracking-wider">"OFF-TRACK" SDG1</div>
                        <div class="text-sm font-bold text-[#ef4423]" x-text="new Intl.NumberFormat('id-ID').format(currentValue)"></div>
                    </div>
                </div>

                <!-- Central Text -->
                <div class="absolute left-[35%] flex flex-col z-10 w-[500px]">
                    <div class="number-display" x-text="new Intl.NumberFormat('id-ID').format(animatedValue)"></div>
                    <div class="text-xs font-bold tracking-[2px] text-gray-600 uppercase mt-2 max-w-xs leading-relaxed ml-2">
                        Penduduk di {{ $selectedKecamatan && $selectedKecamatan !== 'Semua' ? 'Kec. ' . ucwords(strtolower($selectedKecamatan)) : 'Banjarnegara' }}<br>yang hidup dalam<br>kemiskinan ekstrem
                    </div>
                </div>

            </div>

        </div>

        <!-- 4 Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-20 gap-y-10 mt-16 max-w-3xl">
            
            <!-- Target Escape Rate -->
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 text-[#0f8b43]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="metric-title mb-1">Target Escape Rate</div>
                    <div class="font-bold text-xl text-gray-900">{{ $metrics['targetRate'] }} orang/detik</div>
                </div>
            </div>

            <!-- Escaped Today -->
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13.49 5.48c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-3.6 13.9l1-4.4 2.1 2v6h2v-7.5l-2.1-2 .6-3c1.3 1.5 3.3 2.5 5.5 2.5v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1l-5.2 2.2v4.7h2v-3.4l1.8-.7-1.6 8.1-4.9-1-.4 2 7 1.4z"/>
                    </svg>
                </div>
                <div>
                    <div class="metric-title mb-1">Escaped Poverty Today</div>
                    <div class="font-bold text-xl text-gray-900">{{ $metrics['escapedToday'] }}</div>
                </div>
            </div>

            <!-- Current Escape Rate -->
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 text-[#ef4423]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div class="metric-title mb-1">Current Escape Rate</div>
                    <div class="font-bold text-xl text-gray-900">{{ $metrics['currentRate'] }} orang/detik</div>
                </div>
            </div>

            <!-- Fell into poverty -->
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 text-[#ef4423]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13.49 5.48c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-3.6 13.9l1-4.4 2.1 2v6h2v-7.5l-2.1-2 .6-3c1.3 1.5 3.3 2.5 5.5 2.5v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1l-5.2 2.2v4.7h2v-3.4l1.8-.7-1.6 8.1-4.9-1-.4 2 7 1.4z" transform="rotate(180 12 12)"/>
                    </svg>
                </div>
                <div>
                    <div class="metric-title mb-1">Fell Into Poverty Today</div>
                    <div class="font-bold text-xl text-gray-900">{{ $metrics['fellToday'] }}</div>
                </div>
            </div>

        </div>

    </main>

    <!-- Timeline Slider -->
    <div class="w-full mt-auto bg-white py-10 px-10 border-t border-gray-100 relative">
        <div class="max-w-[1000px] mx-auto relative pt-8">
            
            <!-- Custom Slider Thumb Label inside slider CSS doesn't support text easily, so we float a div -->
            <div class="absolute -top-2 px-3 py-1 bg-[#ef4423] text-white text-sm font-bold rounded shadow transform -translate-x-1/2 transition-all duration-200" 
                 :style="'left: ' + getSliderPercentage() + '%'"
                 x-text="selectedYear">
                2024
            </div>

            <input type="range" 
                   class="timeline-slider" 
                   min="{{ min(array_keys($timelineData)) }}" 
                   max="{{ max(array_keys($timelineData)) }}" 
                   step="1" 
                   x-model="selectedYear"
                   @input="updateValue()">
                   
            <!-- Ticks -->
            <div class="flex justify-between mt-4 text-[0.65rem] font-bold text-gray-400 tracking-widest relative">
                @foreach(array_keys($timelineData) as $yr)
                    <div class="flex flex-col items-center">
                        <span class="{{ $yr > $tahunSekarang ? 'text-[#ef4423]' : '' }}">
                            {{ $yr }} {{ $yr > $tahunSekarang ? '*' : '' }}
                        </span>
                    </div>
                @endforeach
            </div>
            
            <div class="text-center mt-6 text-xs text-gray-400">
                <span class="text-[#ef4423]">*</span> Data tahun <span class="font-bold">{{ $tahunSekarang + 1 }}</span> dan seterusnya merupakan proyeksi statistik berdasarkan tren historis.
            </div>
        </div>
    </div>

    <script>
        const timelineData = @json($timelineData);
        const maxDataValue = Math.max(...Object.values(timelineData)) * 1.5; // For donut ratio

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
                    // Circumference of r=40 is 2 * PI * 40 = 251.2
                    // We want orange ring to be proportional to poverty size relative to a theoretical maximum
                    const circumference = 251.2;
                    const ratio = this.currentValue / maxDataValue;
                    const offset = circumference - (ratio * circumference);
                    return offset; // Stroke dash offset
                },

                animateCounter(start, end) {
                    let duration = 1000; 
                    let startTimestamp = null;
                    
                    const step = (timestamp) => {
                        if (!startTimestamp) startTimestamp = timestamp;
                        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                        const easeOut = 1 - Math.pow(1 - progress, 3); // cubic ease out
                        
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
