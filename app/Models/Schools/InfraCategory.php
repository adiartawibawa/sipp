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

            // ===== ROOMS =====
            // Learning Spaces
            ['name' => 'Ruang Kelas', 'type' => 'room', 'code' => 'RKL', 'desc' => 'Ruang pembelajaran reguler'],
            ['name' => 'Laboratorium IPA', 'type' => 'room', 'code' => 'RLI', 'desc' => 'Lab ilmu pengetahuan alam'],
            ['name' => 'Laboratorium Komputer', 'type' => 'room', 'code' => 'RLK', 'desc' => 'Lab teknologi informasi'],
            ['name' => 'Laboratorium Bahasa', 'type' => 'room', 'code' => 'RLB', 'desc' => 'Lab bahasa asing'],
            ['name' => 'Ruang Praktek', 'type' => 'room', 'code' => 'RPK', 'desc' => 'Ruang praktik kejuruan (SMK)'],
            ['name' => 'Ruang Multimedia', 'type' => 'room', 'code' => 'RMM', 'desc' => 'Ruang pembelajaran digital'],
            ['name' => 'Ruang Perpustakaan', 'type' => 'room', 'code' => 'RPT', 'desc' => 'Ruang baca dan literasi'],

            // Administrative Spaces
            ['name' => 'Ruang Kepala Sekolah', 'type' => 'room', 'code' => 'RKS', 'desc' => 'Kantor kepala sekolah'],
            ['name' => 'Ruang Guru', 'type' => 'room', 'code' => 'RGR', 'desc' => 'Ruang kerja guru'],
            ['name' => 'Ruang Tata Usaha', 'type' => 'room', 'code' => 'RTU', 'desc' => 'Kantor administrasi'],
            ['name' => 'Ruang BP/BK', 'type' => 'room', 'code' => 'RBK', 'desc' => 'Bimbingan konseling siswa'],

            // Support Facilities
            ['name' => 'Ruang UKS', 'type' => 'room', 'code' => 'RUK', 'desc' => 'Unit kesehatan sekolah'],
            ['name' => 'Ruang OSIS', 'type' => 'room', 'code' => 'ROS', 'desc' => 'Kantor organisasi siswa'],
            ['name' => 'Kantin', 'type' => 'room', 'code' => 'RKN', 'desc' => 'Area makan sekolah'],
            ['name' => 'Gudang', 'type' => 'room', 'code' => 'RGD', 'desc' => 'Penyimpanan inventaris'],
            ['name' => 'Toilet', 'type' => 'room', 'code' => 'RTL', 'desc' => 'Fasilitas sanitasi'],
            ['name' => 'Ruang Ibadah', 'type' => 'room', 'code' => 'RIB', 'desc' => 'Tempat ibadah multireligi'],
            ['name' => 'Ruang Serbaguna', 'type' => 'room', 'code' => 'RSG', 'desc' => 'Aula multifungsi'],

            // Special Needs
            ['name' => 'Ruang Inklusi', 'type' => 'room', 'code' => 'RIN', 'desc' => 'Untuk siswa berkebutuhan khusus'],
            ['name' => 'Ruang Terapi', 'type' => 'room', 'code' => 'RTR', 'desc' => 'Terapi fisik/konseling'],

            // Technical
            ['name' => 'Ruang Data', 'type' => 'room', 'code' => 'RDT', 'desc' => 'Pusat data dan server'],
            ['name' => 'Ruang PMP', 'type' => 'room', 'code' => 'RPM', 'desc' => 'Monitoring mutu pendidikan'],

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
