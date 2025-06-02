<?php

namespace App\Exports;

use App\Models\Schools\School;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SchoolsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return School::all();
    }

    public function headings(): array
    {
        return [
            'NPSN',
            'Nama Sekolah',
            'NSS',
            'Jenjang Pendidikan',
            'Status',
            'Tahun Berdiri',
            'No Izin Operasional',
            'Tanggal Izin Operasional',
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

    public function map($school): array
    {
        return [
            $school->npsn,
            $school->name,
            $school->nss,
            $school->edu_type,
            $school->status,
            $school->est_year,
            $school->op_permit_no,
            $school->op_permit_date?->format('d/m/Y'),
            $school->accreditation,
            $school->accred_score,
            $school->accred_year,
            $school->curriculum,
            $school->village_id,
            $school->district_id,
            $school->regency_id,
            $school->province_id,
            $school->postal_code,
            $school->latitude,
            $school->longitude,
            $school->address,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
