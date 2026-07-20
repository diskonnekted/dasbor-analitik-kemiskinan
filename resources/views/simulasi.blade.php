@extends('layouts.app')

@section('title', 'Simulasi Intervensi Kebijakan')

@section('content')
<style>
    /* Custom Neo-brutalist Range Slider matching pantarhei theme */
    .brutal-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 10px;
        background: #e5e5d8;
        border: 2px solid #171717;
        outline: none;
    }
    .brutal-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 24px;
        height: 24px;
        background: #171717;
        border: 2px solid #171717;
        cursor: pointer;
        box-shadow: 2px 2px 0px 0px #f4f4f0;
    }
</style>

<div class="flex-grow overflow-y-auto w-full py-8 px-6 sm:px-8 space-y-8 bg-[#f4f4f0] text-[#171717]" 
     x-data="simulasiData()" 
     x-init="initChart()">
    
    <!-- Hero Header Section -->
    <div class="relative bg-white border-2 border-[#171717] p-8 md:p-10 shadow-[4px_4px_0px_0px_#171717] flex flex-col xl:flex-row xl:items-center justify-between gap-8">
        
        <div class="relative z-10 max-w-3xl flex items-start gap-6">
            <a href="{{ route('dashboard') }}" aria-label="Kembali ke Dasbor" class="mt-2 bg-white text-[#171717] p-3 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] hover:shadow-[4px_4px_0px_0px_#171717] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">
                <i data-lucide="arrow-left" class="h-6 w-6" aria-hidden="true"></i>
            </a>
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#171717] text-white text-xs font-mono font-bold tracking-widest uppercase mb-4 border border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                    <i data-lucide="calculator" class="w-4 h-4"></i>
                    Prescriptive Analytics
                </div>
                <h1 class="text-3xl md:text-4xl font-serif font-black text-[#171717] uppercase tracking-tight mb-2">Simulasi Kebijakan Anggaran</h1>
                <p class="text-neutral-600 font-mono font-bold text-sm">Modul simulasi (*What-If Analysis*) interaktif untuk mengukur presisi dampak penambahan anggaran terhadap target pengentasan kemiskinan per kecamatan berdasarkan kalkulasi elastisitas historis.</p>
            </div>
        </div>
        
        <div class="relative z-10 flex flex-col items-center justify-center bg-white p-5 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] min-w-[200px]">
            <span class="text-[#171717] text-xs font-mono font-black uppercase tracking-widest mb-2">Status Simulator</span>
            <div class="flex items-center gap-2 font-mono font-black text-sm uppercase">
                <span class="w-3.5 h-3.5 bg-[#171717] border border-white"></span>
                <span>ONLINE</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Panel Kontrol Simulasi -->
        <div class="lg:col-span-4 bg-white p-8 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col space-y-6">
            
            <h2 class="text-lg font-serif font-black text-[#171717] flex items-center gap-3 uppercase border-b-2 border-[#171717] pb-2">
                <i data-lucide="sliders" class="w-5 h-5"></i>
                Panel Parameter
            </h2>
            
            <div>
                <label class="block text-[10px] font-mono font-black text-neutral-500 uppercase tracking-widest mb-2">Pilih Kecamatan Sasaran</label>
                <select x-model="selectedKecamatan" @change="updateSimulasi" class="w-full rounded-none border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] py-4 px-5 text-[#171717] font-mono font-black bg-white cursor-pointer focus:ring-0">
                    <option value="">-- Pilih Wilayah Operasi --</option>
                    @foreach($kecamatanList as $kec)
                        <option value="{{ $kec }}">{{ $kec }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="selectedKecamatan !== ''" x-transition.opacity class="space-y-8 pt-6 border-t-2 border-[#171717]">
                <div>
                    <label class="block text-[10px] font-mono font-black text-neutral-500 uppercase tracking-widest mb-4">Tambahan Injeksi Anggaran (Juta Rupiah)</label>
                    <div class="relative mt-8 mb-4">
                        <input type="range" x-model.number="tambahanAnggaran" min="0" max="50000" step="500" @input="updateSimulasi" class="brutal-slider">
                    </div>
                    <div class="flex items-center justify-between text-[10px] font-mono font-black text-[#171717] uppercase tracking-widest">
                        <span>Rp 0</span>
                        <span>Rp 50 Miliar</span>
                    </div>
                    
                    <div class="mt-6 flex justify-center">
                        <div class="bg-[#f4f4f0] p-4 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] w-full text-center">
                            <span class="block text-[9px] font-mono font-black text-neutral-500 uppercase tracking-widest mb-1">Injeksi Baru</span>
                            <div class="text-xl font-mono font-black text-[#171717]">
                                + Rp <span x-text="new Intl.NumberFormat('id-ID').format(tambahanAnggaran)"></span> <span class="text-sm font-bold text-neutral-500">Juta</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-[#171717] text-white p-5 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                    <div>
                        <p class="text-[9px] font-mono text-neutral-400 uppercase font-black tracking-widest mb-1">Total Estimasi Anggaran (Lama + Baru)</p>
                        <p class="text-xl font-mono font-black text-white">Rp <span x-text="new Intl.NumberFormat('id-ID').format(getAnggaranTotal())"></span> <span class="text-xs font-bold text-neutral-400">Juta</span></p>
                    </div>
                </div>
            </div>

            <div x-show="selectedKecamatan === ''" class="flex-1 flex flex-col items-center justify-center text-center p-8 bg-[#f4f4f0] border-2 border-[#171717] border-dashed">
                <i data-lucide="mouse-pointer" class="w-8 h-8 text-neutral-400 mb-4"></i>
                <p class="text-neutral-600 font-mono font-bold text-xs uppercase">Pilih kecamatan sasaran untuk menyalakan panel simulasi.</p>
            </div>
        </div>

        <!-- Visualisasi Hasil Simulasi -->
        <div class="lg:col-span-8 bg-white p-8 md:p-10 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] flex flex-col">
            
            <h2 class="text-xl font-serif font-black text-[#171717] mb-8 flex items-center gap-3 uppercase border-b-2 border-[#171717] pb-2">
                <i data-lucide="activity" class="w-6 h-6"></i>
                Dampak Kebijakan (Proyeksi Real-Time)
            </h2>
            
            <div x-show="selectedKecamatan === ''" class="flex-1 flex flex-col items-center justify-center border-2 border-dashed border-[#171717] bg-[#f4f4f0]">
                <p class="text-neutral-500 font-mono font-black uppercase tracking-widest text-xs">Menunggu Parameter Masukan...</p>
            </div>

            <div x-show="selectedKecamatan !== ''" class="flex-1 flex flex-col" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Base Case Card -->
                    <div class="bg-white p-6 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                        <h3 class="text-neutral-500 font-mono font-black text-[9px] uppercase tracking-widest mb-2">Kondisi Awal (Base Case)</h3>
                        <p class="text-3xl font-serif font-black text-[#171717]"><span x-text="new Intl.NumberFormat('id-ID').format(getKemiskinanAwal())"></span> <span class="text-sm font-bold text-neutral-500">Jiwa</span></p>
                    </div>
                    
                    <!-- Simulasi Card -->
                    <div class="bg-[#e5e5d8] p-6 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                        <h3 class="text-[#171717] font-mono font-black text-[9px] uppercase tracking-widest mb-2">Proyeksi Setelah Intervensi</h3>
                        <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                            <p class="text-3xl font-serif font-black text-[#171717]"><span x-text="new Intl.NumberFormat('id-ID').format(getKemiskinanSimulasi())"></span> <span class="text-sm font-bold text-neutral-600 font-mono">Jiwa</span></p>
                            
                            <div x-show="getPenurunan() > 0" x-transition class="bg-[#171717] text-white font-mono font-black text-[10px] uppercase border-2 border-[#171717] px-3 py-1.5 shadow-[2px_2px_0px_0px_#f4f4f0] flex items-center gap-1.5">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                <span>Terselamatkan: <span x-text="new Intl.NumberFormat('id-ID').format(getPenurunan())"></span> Jiwa</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative flex-1 w-full min-h-[350px] bg-white border border-[#171717] p-4">
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
                let elastisitas = parseFloat(dbKecamatan[this.selectedKecamatan].elastisitas);
                let penurunan = Math.floor(elastisitas * this.tambahanAnggaran);
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
                                '#a3a398', // Gray untuk base
                                '#171717'  // Black untuk simulasi
                            ],
                            borderColor: '#171717',
                            borderWidth: 1.5,
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
                                grid: { color: '#e5e5d8' },
                                ticks: { font: { family: 'Inter' }, color: '#171717' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Inter', weight: 'bold' }, color: '#171717' }
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
                    simulasiChartInstance.update();
                }
            }
        }));
    });
</script>
@endpush
