<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirBersihKecamatan extends Model
{
    protected $table = 'air_bersih_kecamatans';

    protected $fillable = [
        'tahun',
        'kecamatan',
        'pelanggan',
        'air_disalurkan_m3',
        'nilai_rupiah',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'pelanggan' => 'integer',
        'air_disalurkan_m3' => 'float',
        'nilai_rupiah' => 'integer',
    ];
}