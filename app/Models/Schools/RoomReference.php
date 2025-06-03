<?php

namespace App\Models\Schools;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model untuk referensi jenis ruangan
 *
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string|null $desc
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class RoomReference extends Model
{
    protected $table = 'room_refs';

    protected $fillable = ['name', 'type', 'code', 'desc'];

    public static function defaultRoomReference()
    {
        return [
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

            // ===== SPECIAL CASES =====
            ['name' => 'Ruangan Lainnya', 'type' => 'other', 'code' => 'ZZZ', 'desc' => 'Aset tidak terklasifikasi']
        ];
    }

    /**
     * Relasi ke ruangan dengan referensi ini
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'room_ref_id');
    }

    /**
     * Accessor untuk nama lengkap dengan kode
     */
    public function getFullNameAttribute(): string
    {
        return $this->code ? "[{$this->code}] {$this->name}" : $this->name;
    }
}
