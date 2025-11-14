<?php

namespace App\Imports;

use App\Models\BantuanProvinsi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BantuanProvinsiImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Jika nama_desa kosong, lewati baris ini
        if (empty($row['nama_desa'])) {
            return null;
        }

        return new BantuanProvinsi([
            'nama_desa'        => $row['nama_desa'] ?? null,
            'nama_kecamatan'   => $row['nama_kecamatan'] ?? null,
            'tpapd'            => $row['tpapd'] ?? 0,
            'bpd'              => $row['bpd'] ?? 0,
            'fisik'            => $row['fisik'] ?? 0,
            'total_banprov'    => $row['total_banprov'] ?? 0,
            'lolos_verifikasi' => filter_var($row['lolos_verifikasi'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'sudah_cair'       => filter_var($row['sudah_cair'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'tahun'            => $row['tahun'] ?? date('Y'),
        ]);
    }
}
