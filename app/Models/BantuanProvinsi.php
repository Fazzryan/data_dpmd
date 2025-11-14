<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BantuanProvinsi extends Model
{
    use HasFactory;

    protected $table = 'bantuan_provinsi';

    protected $fillable = [
        'nama_desa',
        'nama_kecamatan',
        'tpapd',
        'bpd',
        'fisik',
        'total_banprov',
        'lolos_verifikasi',
        'sudah_cair',
        'tahun',
    ];

    protected $casts = [
        'lolos_verifikasi' => 'boolean',
        'sudah_cair' => 'boolean',
    ];
}
