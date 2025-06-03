<?php

namespace App\Models\Schools;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model untuk kategori infrastruktur
 *
 * @property int $id
 * @property string $name
 * @property string|null $desc
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class InfraCategory extends Model
{
    protected $table = 'infra_cats';

    protected $fillable = ['name', 'type', 'code', 'desc'];


    public static function defaultInfraCategory()
    {
        return [
            // ===== CORE ASSETS =====
            ['name' => 'Tanah', 'type' => 'land', 'code' => 'TNH', 'desc' => 'Aset tanah sekolah'],

            // ===== BUILDINGS =====
            ['name' => 'Bangunan Gedung', 'type' => 'building', 'code' => 'BDG', 'desc' => 'Struktur fisik utama sekolah'],
            ['name' => 'Bangunan Pendidikan', 'type' => 'building', 'code' => 'BDP', 'desc' => 'Gedung khusus proses pembelajaran'],
            ['name' => 'Bangunan Penunjang', 'type' => 'building', 'code' => 'BDPN', 'desc' => 'Fasilitas pendukung non-pembelajaran'],
            ['name' => 'Bangunan Administrasi', 'type' => 'building', 'code' => 'BDA', 'desc' => 'Gedung administrasi dan manajemen'],
            ['name' => 'Bangunan Sanitasi', 'type' => 'building', 'code' => 'BDS', 'desc' => 'Fasilitas kebersihan dan kesehatan'],
            ['name' => 'Bangunan Olahraga', 'type' => 'building', 'code' => 'BDOL', 'desc' => 'Sarana olahraga indoor'],
            ['name' => 'Bangunan Khusus', 'type' => 'building', 'code' => 'BDK', 'desc' => 'Fasilitas kebutuhan khusus'],

            // ===== OUTDOOR FACILITIES =====
            ['name' => 'Lapangan Olahraga', 'type' => 'outdoor', 'code' => 'OLP', 'desc' => 'Area olahraga outdoor'],
            ['name' => 'Taman', 'type' => 'outdoor', 'code' => 'OTM', 'desc' => 'Area penghijauan sekolah'],
            ['name' => 'Parkir', 'type' => 'outdoor', 'code' => 'OPK', 'desc' => 'Area parkir kendaraan'],

            // ===== SPECIAL CASES =====
            ['name' => 'Fasilitas Lainnya', 'type' => 'other', 'code' => 'ZZZ', 'desc' => 'Aset tidak terklasifikasi']
        ];
    }

    /**
     * Relasi ke tanah dalam kategori ini
     */
    public function lands(): HasMany
    {
        return $this->hasMany(Land::class, 'infra_cat_id');
    }

    /**
     * Relasi ke bangunan dalam kategori ini
     */
    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'infra_cat_id');
    }

    /**
     * Scope untuk kategori aktif
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('name');
    }
}
