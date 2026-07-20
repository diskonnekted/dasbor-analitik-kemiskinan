@extends('layouts.app')

@section('title', 'Simulasi Intervensi Kebijakan')

@section('content')
<div class="w-full mx-auto py-8 px-4 sm:px-8 lg:px-12 space-y-8 bg-[#f8fafc] min-h-screen" 
     x-data="simulasiData()" 
     x-init="initChart()">
    
    <!-- Hero Header Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-purple-950 to-indigo-950 rounded-[2.5rem] p-8 md:p-12 shadow-2xl border border-slate-800 flex flex-col xl:flex-row xl:items-center justify-between gap-8">
        <!-- Decorative Glow -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-fuchsia-500/20 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-20 w-80 h-80 bg-blue-500/20 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl flex items-start gap-6">
            <a href="{{ route('dashboard') }}" class="mt-2 bg-white/10 hover:bg-white/20 p-3 rounded-2xl text-white transition-all backdrop-blur-md border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-fuchsia-300 text-xs font-bold tracking-widest uppercase mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                    Prescriptive Analytics
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-2">Simulasi <span class="text-transparent bg-clip-text bg-gradient-to-r from-fuchsia-400 to-indigo-400">Kebijakan Anggaran</span></h1>
                <p class="text-slate-400 text-lg">Modul simulasi (*What-If Analysis*) interaktif untuk mengukur presisi dampak penambahan anggaran terhadap target pengentasan kemiskinan per kecamatan berdasarkan kalkulasi elastisitas historis.</p>
            </div>
        </div>
        
        <div class="relative z-10 flex flex-col items-center justify-center bg-white/5 p-6 rounded-3xl border border-white/10 backdrop-blur-md min-w-[200px]">
            <span class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Status Simulator</span>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-emerald-400 font-bold tracking-widest uppercase">Online</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Panel Kontrol Simulasi -->
        <div class="lg:col-span-4 bg-white p-8 md:p-10 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col space-y-6 relative overflow-hidden">
            <div class="absolute left-0 top-0 w-24 h-24 bg-fuchsia-50 rounded-br-full -z-0"></div>
            
            <h2 class="text-xl font-black text-slate-800 relative z-10 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-fuchsia-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                Panel Parameter
            </h2>
            
            <div class="relative z-10">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Pilih Kecamatan Sasaran</label>
                <select x-model="selectedKecamatan" @change="updateSimulasi" class="w-full form-select rounded-2xl border-slate-200 shadow-sm focus:border-fuchsia-500 focus:ring focus:ring-fuchsia-200 py-4 px-5 text-slate-800 bg-slate-50 font-black cursor-pointer transition-colors">
                    <option value="">-- Pilih Wilayah Operasi --</option>
                    @foreach($kecamatanList as $kec)
                        <option value="{{ $kec }}">{{ $kec }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="selectedKecamatan !== ''" x-transition.opacity class="space-y-8 pt-6 border-t border-slate-100 relative z-10">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Tambahan Injeksi Anggaran (Juta Rupiah)</label>
                    <div class="relative mt-8 mb-4">
                        <input type="range" x-model.number="tambahanAnggaran" min="0" max="50000" step="500" @input="updateSimulasi" class="w-full h-3 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-fuchsia-600 shadow-inner">
                        <div class="absolute -top-10 left-1/2 transform -translate-x-1/2 bg-slate-800 text-white text-xs font-bold py-1 px-3 rounded-lg opacity-0 transition-opacity" :class="{'opacity-100': tambahanAnggaran > 0}">
                            Geser Slider
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs font-bold text-slate-300 uppercase tracking-widest">
                        <span>Rp 0</span>
                        <span>Rp 50 Miliar</span>
                    </div>
                    
                    <div class="mt-6 flex justify-center">
                        <div class="bg-gradient-to-r from-fuchsia-500 to-indigo-600 p-[2px] rounded-2xl w-full">
                            <div class="bg-white px-6 py-4 rounded-[14px] text-center">
                                <span class="block text-[10px] text-fuchsia-500 font-bold uppercase tracking-widest mb-1">Injeksi Baru</span>
                                <div class="text-2xl font-black text-slate-800">
                                    + Rp <span x-text="new Intl.NumberFormat('id-ID').format(tambahanAnggaran)"></span> <span class="text-base text-slate-500 font-bold">Juta</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800 p-6 rounded-2xl relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest mb-1">Total Estimasi Anggaran (Lama + Baru)</p>
                        <p class="text-2xl font-black text-white">Rp <span x-text="new Intl.NumberFormat('id-ID').format(getAnggaranTotal())"></span> <span class="text-sm font-medium text-slate-400">Juta</span></p>
                    </div>
                </div>
            </div>

            <div x-show="selectedKecamatan === ''" class="flex-1 flex flex-col items-center justify-center text-center p-8 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                <div class="bg-white p-4 rounded-full shadow-sm border border-slate-100 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" /></svg>
                </div>
                <p class="text-slate-400 text-sm font-bold">Pilih kecamatan sasaran untuk menyalakan panel simulasi.</p>
            </div>
        </div>

        <!-- Visualisasi Hasil Simulasi -->
        <div class="lg:col-span-8 bg-white p-8 md:p-10 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-50 rounded-bl-full -z-0"></div>
            
            <h2 class="text-xl font-black text-slate-800 mb-8 relative z-10 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                Dampak Kebijakan (Proyeksi Real-Time)
            </h2>
            
            <div x-show="selectedKecamatan === ''" class="flex-1 flex flex-col items-center justify-center border-2 border-dashed border-slate-100 rounded-2xl bg-slate-50/30">
                <div class="animate-pulse flex space-x-4">
                    <div class="flex-1 space-y-6 py-1">
                        <div class="h-2 bg-slate-200 rounded"></div>
                        <div class="space-y-3">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="h-2 bg-slate-200 rounded col-span-2"></div>
                                <div class="h-2 bg-slate-200 rounded col-span-1"></div>
                            </div>
                            <div class="h-2 bg-slate-200 rounded"></div>
                        </div>
                    </div>
                </div>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs mt-6">Awaiting Input Parameters...</p>
            </div>

            <div x-show="selectedKecamatan !== ''" class="flex-1 flex flex-col relative z-10" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Base Case Card -->
                    <div class="bg-slate-50 p-6 sm:p-8 rounded-[1.5rem] border border-slate-200 relative overflow-hidden">
                        <div class="absolute right-0 bottom-0 opacity-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </div>
                        <h3 class="text-slate-400 font-bold text-[10px] uppercase tracking-widest mb-2 relative z-10">Kondisi Awal (Base Case)</h3>
                        <p class="text-4xl font-black text-slate-800 relative z-10"><span x-text="new Intl.NumberFormat('id-ID').format(getKemiskinanAwal())"></span> <span class="text-lg font-medium text-slate-400">Jiwa</span></p>
                    </div>
                    
                    <!-- Simulasi Card -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-6 sm:p-8 rounded-[1.5rem] border border-indigo-100 shadow-inner relative overflow-hidden group">
                        <div class="absolute right-0 bottom-0 opacity-5 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 text-indigo-900" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <h3 class="text-indigo-600 font-bold text-[10px] uppercase tracking-widest mb-2 relative z-10">Proyeksi Setelah Intervensi</h3>
                        <div class="flex flex-col sm:flex-row sm:items-end gap-3 relative z-10">
                            <p class="text-4xl font-black text-indigo-700"><span x-text="new Intl.NumberFormat('id-ID').format(getKemiskinanSimulasi())"></span> <span class="text-lg font-medium text-indigo-400">Jiwa</span></p>
                            
                            <div x-show="getPenurunan() > 0" x-transition class="bg-emerald-500 text-white font-bold text-sm px-3 py-1.5 rounded-xl mb-1 flex items-center gap-1.5 shadow-md shadow-emerald-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span>Terselamatkan: <span x-text="new Intl.NumberFormat('id-ID').format(getPenurunan())"></span> Jiwa</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative flex-1 w-full min-h-[350px] bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
                    <canvas id="simulasiChart"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const dbKecamatan = @json($dataKecamatan);

    document.addEventListener('alpine:init', () => {
        let simulasiChartInstance = null;

        Alpine.data('simulasiData', () => ({
            selectedKecamatan: '',
            tambahanAnggaran: 0,

            getKemiskinanAwal() {
                if(!this.selectedKecamatan) return 0;
                return parseInt(dbKecamatan[this.selectedKecamatan].kemiskinan_awal) || 0;
            },

            getAnggaranAwal() {
                if(!this.selectedKecamatan) return 0;
                return parseFloat(dbKecamatan[this.selectedKecamatan].anggaran_awal_juta) || 0;
            },

            getAnggaranTotal() {
                return this.getAnggaranAwal() + this.tambahanAnggaran;
            },

            getPenurunan() {
                if(!this.selectedKecamatan) return 0;
                // Rumus simulasi: elastisitas * tambahan anggaran
                let elastisitas = parseFloat(dbKecamatan[this.selectedKecamatan].elastisitas);
                let penurunan = Math.floor(elastisitas * this.tambahanAnggaran);
                // Tidak bisa turun lebih dari jumlah awal
                if (penurunan > this.getKemiskinanAwal()) {
                    penurunan = this.getKemiskinanAwal();
                }
                return penurunan;
            },

            getKemiskinanSimulasi() {
                return this.getKemiskinanAwal() - this.getPenurunan();
            },

            initChart() {
                const ctx = document.getElementById('simulasiChart').getContext('2d');
                if (simulasiChartInstance) {
                    simulasiChartInstance.destroy();
                }
                simulasiChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Kondisi Awal', 'Proyeksi Simulasi'],
                        datasets: [{
                            label: 'Penduduk Sangat Miskin (Jiwa)',
                            data: [0, 0],
                            backgroundColor: [
                                'rgba(100, 116, 139, 0.8)', // Slate untuk base
                                'rgba(37, 99, 235, 0.9)'    // Blue untuk simulasi
                            ],
                            borderRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.raw.toLocaleString('id-ID') + ' Jiwa';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true,
                                grid: { color: '#F1F5F9' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            },

            updateSimulasi() {
                if(!this.selectedKecamatan) {
                    this.tambahanAnggaran = 0;
                    return;
                }
                
                if(simulasiChartInstance) {
                    simulasiChartInstance.data.datasets[0].data = [this.getKemiskinanAwal(), this.getKemiskinanSimulasi()];
                    
                    // Highlight bar simulasi dengan hijau jika penurunan drastis
                    let bgColors = ['rgba(100, 116, 139, 0.8)'];
                    if(this.getPenurunan() > 0) {
                        bgColors.push('rgba(16, 185, 129, 0.9)'); // Emerald
                    } else {
                        bgColors.push('rgba(37, 99, 235, 0.9)'); // Blue
                    }
                    simulasiChartInstance.data.datasets[0].backgroundColor = bgColors;

                    simulasiChartInstance.update();
                }
            }
        }));
    });
</script>
@endpush
