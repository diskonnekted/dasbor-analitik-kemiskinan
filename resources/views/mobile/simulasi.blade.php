@extends('layouts.mobile')

@section('title', 'Simulasi Intervensi Kebijakan')
@section('pagelabel', 'Simulasi')

@section('content')
<style>
    .brutal-slider { -webkit-appearance: none; width: 100%; height: 10px; background: #e5e5d8; border: 2px solid #171717; outline: none; }
    .brutal-slider::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; width: 24px; height: 24px; background: #171717; border: 2px solid #171717; cursor: pointer; box-shadow: 2px 2px 0px 0px #f4f4f0; }
    [x-cloak] { display: none !important; }
</style>

<div class="w-full px-4 py-5 space-y-5" x-data="simulasiData()" x-init="initChart()">

    <!-- Header -->
    <div class="bg-white border-2 border-[#171717] p-5 shadow-[4px_4px_0px_0px_#171717]">
        <div class="inline-flex items-center gap-2 px-2 py-1 bg-[#171717] text-white text-[9px] font-mono font-bold tracking-widest uppercase mb-3 border border-[#171717]">
            <i data-lucide="calculator" class="w-3 h-3" aria-hidden="true"></i> Prescriptive Analytics
        </div>
        <h1 class="text-2xl font-serif font-black uppercase tracking-tight mb-2">Simulasi Anggaran</h1>
        <p class="text-neutral-600 font-mono font-bold text-xs leading-relaxed">Simulasi <i>What-If</i> dampak penambahan anggaran terhadap pengentasan kemiskinan per kecamatan.</p>
    </div>

    <!-- Panel Kontrol -->
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717] space-y-5">
        <h2 class="text-base font-serif font-black uppercase flex items-center gap-2 border-b-2 border-[#171717] pb-2">
            <i data-lucide="sliders" class="w-5 h-5" aria-hidden="true"></i> Panel Parameter
        </h2>

        <div>
            <label for="kec-sim" class="block text-[9px] font-mono font-black text-neutral-500 uppercase tracking-widest mb-2">Kecamatan Sasaran</label>
            <select id="kec-sim" x-model="selectedKecamatan" @change="updateSimulasi" class="w-full border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] py-3 px-4 font-mono font-black bg-white cursor-pointer focus:ring-0">
                <option value="">-- Pilih Wilayah --</option>
                @foreach($kecamatanList as $kec)
                    <option value="{{ $kec }}">{{ $kec }}</option>
                @endforeach
            </select>
        </div>

        <div x-show="selectedKecamatan !== ''" x-transition.opacity class="space-y-5 pt-4 border-t-2 border-[#171717]">
            <div>
                <label for="anggaran-sim" class="block text-[9px] font-mono font-black text-neutral-500 uppercase tracking-widest mb-3">Tambahan Injeksi Anggaran (Juta Rp)</label>
                <input id="anggaran-sim" type="range" x-model.number="tambahanAnggaran" min="0" max="50000" step="500" @input="updateSimulasi" class="brutal-slider">
                <div class="flex items-center justify-between text-[9px] font-mono font-black uppercase tracking-widest mt-2"><span>Rp 0</span><span>Rp 50 M</span></div>
                <div class="mt-4 bg-[#f4f4f0] p-3 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717] text-center">
                    <span class="block text-[9px] font-mono font-black text-neutral-500 uppercase tracking-widest mb-1">Injeksi Baru</span>
                    <div class="text-lg font-mono font-black">+ Rp <span x-text="new Intl.NumberFormat('id-ID').format(tambahanAnggaran)"></span> <span class="text-xs font-bold text-neutral-500">Juta</span></div>
                </div>
            </div>
            <div class="bg-[#171717] text-white p-4 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <p class="text-[9px] font-mono text-neutral-400 uppercase font-black tracking-widest mb-1">Total Estimasi (Lama + Baru)</p>
                <p class="text-lg font-mono font-black">Rp <span x-text="new Intl.NumberFormat('id-ID').format(getAnggaranTotal())"></span> <span class="text-xs font-bold text-neutral-400">Juta</span></p>
            </div>
        </div>

        <div x-show="selectedKecamatan === ''" class="text-center p-6 bg-[#f4f4f0] border-2 border-[#171717] border-dashed">
            <i data-lucide="mouse-pointer" class="w-7 h-7 text-neutral-400 mx-auto mb-3" aria-hidden="true"></i>
            <p class="text-neutral-600 font-mono font-bold text-[11px] uppercase">Pilih kecamatan untuk menyalakan simulasi.</p>
        </div>
    </div>

    <!-- Hasil -->
    <div class="bg-white p-5 border-2 border-[#171717] shadow-[4px_4px_0px_0px_#171717]">
        <h2 class="text-base font-serif font-black uppercase mb-4 flex items-center gap-2 border-b-2 border-[#171717] pb-2">
            <i data-lucide="activity" class="w-5 h-5" aria-hidden="true"></i> Dampak Kebijakan
        </h2>

        <div x-show="selectedKecamatan === ''" class="text-center py-10 border-2 border-dashed border-[#171717] bg-[#f4f4f0]">
            <p class="text-neutral-500 font-mono font-black uppercase tracking-widest text-[10px]">Menunggu Parameter...</p>
        </div>

        <div x-show="selectedKecamatan !== ''" x-cloak class="space-y-4">
            <div class="bg-white p-4 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <h3 class="text-neutral-500 font-mono font-black text-[9px] uppercase tracking-widest mb-1">Kondisi Awal</h3>
                <p class="text-2xl font-serif font-black"><span x-text="new Intl.NumberFormat('id-ID').format(getKemiskinanAwal())"></span> <span class="text-xs font-bold text-neutral-500">Jiwa</span></p>
            </div>
            <div class="bg-[#e5e5d8] p-4 border-2 border-[#171717] shadow-[2px_2px_0px_0px_#171717]">
                <h3 class="font-mono font-black text-[9px] uppercase tracking-widest mb-1">Setelah Intervensi</h3>
                <p class="text-2xl font-serif font-black"><span x-text="new Intl.NumberFormat('id-ID').format(getKemiskinanSimulasi())"></span> <span class="text-xs font-bold text-neutral-600">Jiwa</span></p>
                <div x-show="getPenurunan() > 0" x-transition class="mt-2 bg-[#171717] text-white font-mono font-black text-[9px] uppercase px-2 py-1 inline-flex items-center gap-1">
                    <i data-lucide="chevron-down" class="w-3 h-3" aria-hidden="true"></i>
                    Terselamatkan: <span x-text="new Intl.NumberFormat('id-ID').format(getPenurunan())"></span> Jiwa
                </div>
            </div>
            <div class="relative w-full h-[260px] bg-white border border-[#171717] p-2"><canvas id="simulasiChart"></canvas></div>
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
                if (penurunan > this.getKemiskinanAwal()) penurunan = this.getKemiskinanAwal();
                return penurunan;
            },
            getKemiskinanSimulasi() {
                return this.getKemiskinanAwal() - this.getPenurunan();
            },
            initChart() {
                const ctx = document.getElementById('simulasiChart').getContext('2d');
                if (simulasiChartInstance) simulasiChartInstance.destroy();
                simulasiChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Kondisi Awal', 'Proyeksi Simulasi'],
                        datasets: [{
                            label: 'Penduduk Sangat Miskin (Jiwa)',
                            data: [0, 0],
                            backgroundColor: ['#a3a398', '#171717'],
                            borderColor: '#171717',
                            borderWidth: 1.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: function(context) { return context.raw.toLocaleString('id-ID') + ' Jiwa'; } } }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#e5e5d8' }, ticks: { font: { family: 'Inter', size: 10 }, color: '#171717' } },
                            x: { grid: { display: false }, ticks: { font: { family: 'Inter', weight: 'bold', size: 10 }, color: '#171717' } }
                        }
                    }
                });
            },
            updateSimulasi() {
                if(!this.selectedKecamatan) { this.tambahanAnggaran = 0; return; }
                if(simulasiChartInstance) {
                    simulasiChartInstance.data.datasets[0].data = [this.getKemiskinanAwal(), this.getKemiskinanSimulasi()];
                    simulasiChartInstance.update();
                }
            }
        }));
    });
</script>
@endpush
