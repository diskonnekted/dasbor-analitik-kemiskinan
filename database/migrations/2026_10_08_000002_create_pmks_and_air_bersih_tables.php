<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel baru untuk data multidimensional:
     *  - PMKS (Penyandang Masalah Kesejahteraan Sosial) per kecamatan/tahun
     *  - Akses air bersih (PDAM) per kecamatan/tahun
     */
    public function up(): void
    {
        Schema::create('pmks_kecamatans', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('kecamatan');
            $table->integer('tuna_susila')->default(0);
            $table->integer('cacat_netra')->default(0);
            $table->integer('cacat_bisu_tuli')->default(0);
            $table->integer('anak_terlantar')->default(0);
            $table->integer('lansia_terlantar')->default(0);
            $table->integer('jumlah')->default(0);
            $table->timestamps();

            $table->unique(['kecamatan', 'tahun']);
        });

        Schema::create('air_bersih_kecamatans', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('kecamatan');
            $table->integer('pelanggan')->nullable();
            $table->double('air_disalurkan_m3')->nullable();
            $table->bigInteger('nilai_rupiah')->nullable();
            $table->timestamps();

            $table->unique(['kecamatan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('air_bersih_kecamatans');
        Schema::dropIfExists('pmks_kecamatans');
    }
};