<?php

namespace App\Exports;

use App\Models\JenisLayanan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JenisLayananExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $cabang;

    public function __construct($cabang)
    {
        $this->cabang = $cabang;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return JenisLayanan::query()
            ->join('cabang as c', 'c.id', '=', 'jenis_layanan.cabang_id')
            ->where('c.slug', $this->cabang)
            ->orderBy('jenis_layanan.id', 'asc')->get(['jenis_layanan.*', 'c.slug']);
    }

    public function map($data): array
    {
        if ($data->for_participant) {
            $forparticipant = 'Ya';
        } else {
            $forparticipant = 'Tidak';
        }
        return [
            $data->nama,
            $forparticipant,
            $data->deskripsi,
            $data->slug,
        ];
    }

    public function headings(): array
    {
        return [
            'nama_layanan',
            'untuk_participant',
            'deskripsi',
            'cabang',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}
