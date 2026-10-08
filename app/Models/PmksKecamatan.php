<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmksKecamatan extends Model
{
    protected $table = 'pmks_kecamatans';

    protected $fillable = [
        'tahun',
        'kecamatan',
        'tuna_susila',
        'cacat_netra',
        'cacat_bisu_tuli',
        'anak_terlantar',
        'lansia_terlantar',
        'jumlah',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'tuna_susila' => 'integer',
        'cacat_netra' => 'integer',
        'cacat_bisu_tuli' => 'integer',
        'anak_terlantar' => 'integer',
        'lansia_terlantar' => 'integer',
        'jumlah' => 'integer',
    ];
}