<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalisaController;
use App\Http\Controllers\PrediksiController;

Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('landing');
Route::get('/dasbor', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/export/csv', [DashboardController::class, 'exportCsv'])->name('export.csv');
Route::get('/analisa', [AnalisaController::class, 'index'])->name('analisa');
Route::get('/prediksi', [PrediksiController::class, 'index'])->name('prediksi');
Route::get('/klaster', [\App\Http\Controllers\ClusterController::class, 'index'])->name('klaster');
Route::get('/simulasi', [\App\Http\Controllers\SimulasiController::class, 'index'])->name('simulasi');
Route::get('/rekomendasi', [\App\Http\Controllers\RekomendasiController::class, 'index'])->name('rekomendasi');
Route::get('/rekomendasi/cetak/{kecamatan}', [\App\Http\Controllers\RekomendasiController::class, 'cetak'])->name('rekomendasi.cetak')->where('kecamatan', '.*');
Route::get('/makro', [\App\Http\Controllers\KonteksMakroController::class, 'index'])->name('makro');
