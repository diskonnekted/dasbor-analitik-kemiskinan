<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bantuan_sosials', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->string('kecamatan');
            $table->integer('jumlah_penerima');
            $table->bigInteger('anggaran_ribu_rupiah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bantuan_sosials');
    }
};
