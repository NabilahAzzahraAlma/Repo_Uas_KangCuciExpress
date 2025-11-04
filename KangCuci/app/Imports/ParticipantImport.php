<?php

namespace App\Imports;

use App\Models\participant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class participantImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $kk = participant::where('kartu_keluarga', $row['kartu_keluarga'])->first();
        if (empty($kk)) {
            return new participant([
                'kartu_keluarga' => $row['kartu_keluarga'],
                'rt' => $row['rt'],
                'rw' => $row['rw'],
            ]);
        }
    }
}
