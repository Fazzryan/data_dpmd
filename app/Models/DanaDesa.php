<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DanaDesa extends Model
{
    use HasFactory;

    protected $table = 'dana_desa';

    protected $fillable = [
        'nama_desa',
        'nama_kecamatan',
        'pagu_blt',
        'pagu_ketahanan_pangan',
        'pagu_stunting',
        'pagu_proklim',
        'pagu_potensi_desa',
        'pagu_ti',
        'pagu_padat_karya',
        'pagu_non_prioritas',
        'status_realisasi',
        'tahap',
        'tahun',
    ];
}
