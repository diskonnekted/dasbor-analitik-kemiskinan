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
        Schema::create('indikator_makros', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('wilayah');
            $table->string('nama_indikator');
            $table->string('kategori');
            $table->double('nilai')->nullable();
            $table->string('satuan')->nullable();
            $table->timestamps();

            $table->index(['nama_indikator', 'tahun']);
            $table->index('wilayah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_makros');
    }
};
