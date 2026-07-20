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
        Schema::create('kesejahteraan_kecamatans', function (Blueprint $table) {
            $table->id();
            $table->string('kecamatan');
            $table->integer('tahun');
            $table->integer('desil_1')->nullable();
            $table->integer('desil_2')->nullable();
            $table->integer('desil_3')->nullable();
            $table->integer('desil_4')->nullable();
            $table->timestamps();
            $table->unique(['kecamatan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kesejahteraan_kecamatans');
    }
};
