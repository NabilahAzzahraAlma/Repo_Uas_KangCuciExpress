<?php

namespace App\Imports;

use App\Models\Cabang;
use App\Models\JenisLayanan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JenisLayananImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $cabang = Cabang::where('slug', $row['cabang'])->first();
        $nama = JenisLayanan::withTrashed()->where('nama', $row['nama_layanan'])->where('cabang_id', $cabang->id)->first();
        if ($row['untuk_participant'] == 'Ya') {
            $forparticipant = 1;
        } else if ($row['untuk_participant'] == 'Tidak') {
            $forparticipant = 0;
        }
        if (empty($nama)) {
            return new JenisLayanan([
                'nama' => $row['nama_layanan'],
                'for_participant' => $forparticipant,
                'deskripsi' => $row['deskripsi'],
                'cabang_id' => $cabang->id,
            ]);
        }
    }
}
