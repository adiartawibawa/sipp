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

            // ===== OTHER FACILITIES =====
            ['name' => 'Parkir', 'type' => 'other', 'code' => 'OPK', 'desc' => 'Area parkir kendaraan'],
            ['name' => 'Fasilitas Olahraga', 'type' => 'other', 'code' => 'OLR', 'desc' => 'Peralatan dan sarana olahraga'],
            ['name' => 'Fasilitas Kesenian', 'type' => 'other', 'code' => 'KSN', 'desc' => 'Peralatan seni dan budaya'],
            ['name' => 'Fasilitas Kesehatan', 'type' => 'other', 'code' => 'KSH', 'desc' => 'Peralatan UKS dan kesehatan'],
            ['name' => 'Fasilitas Keamanan', 'type' => 'other', 'code' => 'KMN', 'desc' => 'Peralatan keamanan sekolah'],
            ['name' => 'Fasilitas Parkir', 'type' => 'other', 'code' => 'PRK', 'desc' => 'Sarana parkir kendaraan'],
            ['name' => 'Taman', 'type' => 'other', 'code' => 'TMN', 'desc' => 'Taman dan penghijauan sekolah'],
            ['name' => 'Peralatan Kantor', 'type' => 'other', 'code' => 'KTR', 'desc' => 'Peralatan administrasi kantor'],
            ['name' => 'Peralatan Laboratorium', 'type' => 'other', 'code' => 'LAB', 'desc' => 'Peralatan lab non-permanen'],
            ['name' => 'Peralatan Perpustakaan', 'type' => 'other', 'code' => 'PER', 'desc' => 'Peralatan pendukung perpustakaan'],
            ['name' => 'Peralatan Dapur', 'type' => 'other', 'code' => 'DAP', 'desc' => 'Peralatan dapur sekolah'],
            ['name' => 'Peralatan Kebersihan', 'type' => 'other', 'code' => 'KBR', 'desc' => 'Peralatan kebersihan sekolah'],
            ['name' => 'Peralatan Ibadah', 'type' => 'other', 'code' => 'IBD', 'desc' => 'Peralatan tempat ibadah'],
            ['name' => 'Peralatan Multimedia', 'type' => 'other', 'code' => 'MMD', 'desc' => 'Peralatan audio visual'],
            ['name' => 'Peralatan Elektronik', 'type' => 'other', 'code' => 'ELE', 'desc' => 'Peralatan elektronik sekolah'],
            ['name' => 'Furniture', 'type' => 'other', 'code' => 'FUR', 'desc' => 'Mebel dan perabot sekolah'],
            ['name' => 'Kendaraan Sekolah', 'type' => 'other', 'code' => 'KND', 'desc' => 'Kendaraan milik sekolah'],
            ['name' => 'Instalasi Listrik', 'type' => 'other', 'code' => 'LST', 'desc' => 'Instalasi listrik permanen'],
            ['name' => 'Instalasi Air', 'type' => 'other', 'code' => 'AIR', 'desc' => 'Instalasi air dan sanitasi'],
            ['name' => 'Lainnya', 'type' => 'other', 'code' => 'LNY', 'desc' => 'Fasilitas lain yang tidak tercakup'],

            // ===== SPECIAL CASES =====
            ['name' => 'Fasilitas Lainnya', 'type' => 'uncategorized', 'code' => 'ZZZ', 'desc' => 'Aset tidak terklasifikasi']
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
