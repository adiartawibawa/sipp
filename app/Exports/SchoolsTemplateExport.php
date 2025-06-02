<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SchoolsTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [];
    }

    public function headings(): array
    {
        return [
            'NPSN',
            'Nama Sekolah',
            'NSS',
            'Jenjang Pendidikan',
            'Status (negeri/swasta)',
            'Tahun Berdiri',
            'No Izin Operasional',
            'Tanggal Izin Operasional (dd/mm/yyyy)',
            'Akreditasi',
            'Nilai Akreditasi',
            'Tahun Akreditasi',
            'Kurikulum',
            'Kode Desa/Kelurahan',
            'Kode Kecamatan',
            'Kode Kabupaten/Kota',
            'Kode Provinsi',
            'Kode Pos',
            'Latitude',
            'Longitude',
            'Alamat'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
