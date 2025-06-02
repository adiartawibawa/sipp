<?php

namespace App\Imports;

use App\Models\Schools\School;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class SchoolsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new School([
            'npsn' => $row['npsn'] ?? null,
            'name' => $row['nama_sekolah'],
            'nss' => $row['nss'] ?? null,
            'edu_type' => $row['jenjang_pendidikan'] ?? null,
            'status' => isset($row['status']) ? strtolower($row['status']) : null,
            'est_year' => $row['tahun_berdiri'] ?? null,
            'op_permit_no' => $row['no_izin_operasional'] ?? null,
            'op_permit_date' => isset($row['tanggal_izin_operasional']) && \Carbon\Carbon::hasFormat($row['tanggal_izin_operasional'], 'd/m/Y')
                ? Carbon::createFromFormat('d/m/Y', $row['tanggal_izin_operasional'])
                : null,
            'accreditation' => $row['akreditasi'] ?? null,
            'accred_score' => $row['nilai_akreditasi'] ?? null,
            'accred_year' => $row['tahun_akreditasi'] ?? null,
            'curriculum' => $row['kurikulum'] ?? null,
            'village_id' => $row['kode_desakelurahan'] ?? null,
            'district_id' => $row['kode_kecamatan'] ?? null,
            'regency_id' => $row['kode_kabupatenkota'] ?? null,
            'province_id' => $row['kode_provinsi'] ?? null,
            'postal_code' => $row['kode_pos'] ?? null,
            'latitude' => $row['latitude'] ?? null,
            'longitude' => $row['longitude'] ?? null,
            'address' => $row['alamat'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            // 'nama_sekolah' => 'required|string|max:255',
            // 'npsn' => 'nullable|string|max:20|unique:schools,npsn',
            // 'nss' => 'nullable|string|max:20',
            // 'jenjang_pendidikan' => 'nullable|string',
            // 'status' => 'nullable|in:negeri,swasta',
            // 'tahun_berdiri' => 'nullable|integer|min:1900|max:' . date('Y'),
            // 'no_izin_operasional' => 'nullable|string|max:50',
            // 'tanggal_izin_operasional' => 'nullable|date_format:d/m/Y',
            // 'akreditasi' => 'nullable|string',
            // 'nilai_akreditasi' => 'nullable|string|max:10',
            // 'tahun_akreditasi' => 'nullable|integer|min:1900|max:' . date('Y'),
            // 'kurikulum' => 'nullable|string',
            // 'kode_desakelurahan' => 'nullable|string|max:20',
            // 'kode_kecamatan' => 'nullable|string|max:20',
            // 'kode_kabupatenkota' => 'nullable|string|max:20',
            // 'kode_provinsi' => 'nullable|string|max:20',
            // 'kode_pos' => 'nullable|string|max:10',
            // 'latitude' => 'nullable|numeric|between:-90,90',
            // 'longitude' => 'nullable|numeric|between:-180,180',
            // 'alamat' => 'nullable|string',
        ];
    }
}
