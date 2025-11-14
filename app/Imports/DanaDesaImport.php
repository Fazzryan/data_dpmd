<?php

namespace App\Imports;

use App\Models\DanaDesa;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class DanaDesaImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // Lewati baris pertama (header)
        foreach ($rows->skip(1) as $row) {
            DanaDesa::create([
                'nama_desa' => $row[0],
                'nama_kecamatan' => $row[1],
                'pagu_blt' => $row[2],
                'pagu_ketahanan_pangan' => $row[3],
                'pagu_stunting' => $row[4],
                'pagu_proklim' => $row[5],
                'pagu_potensi_desa' => $row[6],
                'pagu_ti' => $row[7],
                'pagu_padat_karya' => $row[8],
                'pagu_non_prioritas' => $row[9],
                'status_realisasi' => $row[10],
                'tahap' => $row[11],
                'tahun' => $row[12],
            ]);
        }
    }
}
