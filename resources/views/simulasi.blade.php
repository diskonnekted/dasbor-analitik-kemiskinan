@extends('layouts.app')

@section('title', 'Simulasi Intervensi Kebijakan')

@section('content')
<div class="w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-14"
     x-data="simulasiData()"
     x-init="initChart()">

    <!-- ============ Page header ============ -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <div class="section-label mb-4">
                <span aria-hidden="true">[</span>Prescriptive Analytics<span aria-hidden="true">]</span>
            </div>
            <h1 class="display-lg text-ink">Simulasi Kebijakan Anggaran</h1>
            <p class="body-md text-body mt-3 max-w-2xl">
                Modul simulasi <em>what-if analysis</em> interaktif untuk mengukur presisi dampak penambahan anggaran terhadap target pengentasan kemiskinan per kecamatan berdasarkan elastisitas historis.@include('partials.p3ke-info')
            </p>
        </div>

        <div class="feature-card inline-flex items-center gap-3 py-4 px-6">
            <span class="w-2.5 h-2.5 rounded-full bg-success"></span>
            <span class="caption-uppercase text-success">Simulator Online</span>
        </div>
    </div>

    <!-- ============ Info elastisitas empiris ============ -->
    <div class="panel p-5 mb-8 border-hairline flex flex-col lg:flex-row lg:items-center gap-3 lg:gap-6">
        <span class="caption-uppercase text-muted shrink-0">Elastisitas Simulasi</span>
        <span class="code text-ink tabular">{{ number_format($elastisitasInfo['elastisitas'], 4, ',', '.') }} jiwa / Rp 1 juta</span>
        <span class="body-xs text-muted-soft">{{ $elastisitasInfo['sumber'] }}</span>
        <span class="body-xs text-muted-soft lg:ml-auto">n = {{ $elastisitasInfo['n'] }} observasi &middot; R&sup2; = {{ number_format($elastisitasInfo['r2'], 3, ',', '.') }} &middot; slope = {{ number_format($elastisitasInfo['slope'], 4, ',', '.') }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- ============ Panel kontrol ============ -->
        <div class="lg:col-span-4 feature-card flex flex-col">
            <h2 class="title-sm text-ink mb-6">Panel Parameter</h2>

            <label class="caption-uppercase text-muted block mb-3">Pilih Kecamatan Sasaran</label>
            <select x-model="selectedKecamatan" @change="updateSimulasi" class="select">
                <option value="">-- Pilih Wilayah Operasi --</option>
                @foreach($kecamatanList as $kec)
                    <option value="{{ $kec }}">{{ $kec }}</option>
                @endforeach
            </select>

            <div x-show="selectedKecamatan !== ''" x-transition.opacity class="mt-8 pt-8 border-t border-hairline space-y-8" x-cloak>
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <label class="caption-uppercase text-muted">Tambahan Injeksi Anggaran</label>
                        <span class="code text-primary tabular">+ Rp <span x-text="new Intl.NumberFormat('id-ID').format(tambahanAnggaran)"></span> Jt</span>
                    </div>
                    <input type="range" x-model.number="tambahanAnggaran" min="0" max="50000" step="500" @input="updateSimulasi">
                    <div class="flex items-center justify-between mt-3 body-xs text-muted-soft">
                        <span>Rp 0</span>
                        <span>Rp 50 Miliar</span>
                    </div>
                </div>

                <div class="panel p-5 bg-surface-strong">
                    <p class="caption-uppercase text-muted">Total Estimasi Anggaran</p>
                    <p class="code text-lg text-ink tabular mt-1">Rp <span x-text="new Intl.NumberFormat('id-ID').format(getAnggaranTotal())"></span> <span class="text-muted text-sm">Juta</span></p>
                </div>
            </div>

            <div x-show="selectedKecamatan === ''" class="flex-1 flex flex-col items-center justify-center text-center mt-8 p-8 border border-dashed border-hairline-strong rounded-[12px]" x-cloak>
                <p class="body-sm text-muted">Pilih kecamatan sasaran untuk menyalakan panel simulasi.</p>
            </div>
        </div>

        <!-- ============ Visualisasi ============ -->
        <div class="lg:col-span-8 feature-card flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h2 class="title-sm text-ink">Dampak Kebijakan (Proyeksi Real-Time)</h2>
                <span class="code text-muted">bar</span>
            </div>

            <div x-show="selectedKecamatan === ''" class="flex-1 flex flex-col items-center justify-center border border-dashed border-hairline-strong rounded-[12px] bg-canvas-soft py-20" x-cloak>
                <p class="caption-uppercase text-muted">Awaiting Input Parameters…</p>
            </div>

            <div x-show="selectedKecamatan !== ''" class="flex-1 flex flex-col" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- Base case -->
                    <div class="panel p-6 border-l-2 border-l-muted">
                        <h3 class="caption-uppercase text-muted mb-2">Kondisi Awal (Base Case)</h3>
                        <p class="display-sm text-ink tabular"><span x-text="new Intl.NumberFormat('id-ID').format(getKemiskinanAwal())"></span> <span class="body-sm text-muted">Jiwa</span></p>
                    </div>

                    <!-- Simulasi -->
                    <div class="panel p-6 border-l-2 border-l-success">
                        <h3 class="caption-uppercase text-success mb-2">Proyeksi Setelah Intervensi</h3>
                        <p class="display-sm text-success tabular"><span x-text="new Intl.NumberFormat('id-ID').format(getKemiskinanSimulasi())"></span> <span class="body-sm text-muted">Jiwa</span></p>
                        <div x-show="getPenurunan() > 0" x-transition class="badge-pill bg-success text-white mt-3">
                            Terselamatkan: <span x-text="new Intl.NumberFormat('id-ID').format(getPenurunan())"></span> Jiwa
                        </div>
                    </div>
                </div>

                <div class="relative flex-1 w-full min-h-[350px] bg-canvas-soft rounded-[8px] p-4">
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
                            label: 'Penduduk Kesejahteraan Terendah (Desil 1, Jiwa)',
                            data: [0, 0],
                            backgroundColor: [
                                'rgba(128, 125, 114, 0.7)',  // muted base
                                'rgba(245, 78, 0, 0.9)'     // primary simulasi
                            ],
                            borderRadius: 4,
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
                                grid: { color: '#efeee8' },
                                ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#a09c92' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#a09c92' }
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

                    let bgColors = ['rgba(128, 125, 114, 0.7)'];
                    if(this.getPenurunan() > 0) {
                        bgColors.push('rgba(31, 138, 101, 0.9)'); // success
                    } else {
                        bgColors.push('rgba(245, 78, 0, 0.9)');   // primary
                    }
                    simulasiChartInstance.data.datasets[0].backgroundColor = bgColors;

                    simulasiChartInstance.update();
                }
            }
        }));
    });
</script>
@endpush