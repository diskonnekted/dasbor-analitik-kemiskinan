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
        Schema::create('kemiskinan_data', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun')->unique();
            $table->decimal('jumlah_penduduk_miskin', 10, 2)->nullable();
            $table->decimal('persentase', 5, 2)->nullable();
            $table->decimal('indeks_kedalaman', 5, 2)->nullable();
            $table->decimal('indeks_keparahan', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kemiskinan_data');
    }
};
