<?php

namespace App\Models\Schools;

use App\Models\Regions\District;
use App\Models\Regions\Province;
use App\Models\Regions\Regency;
use App\Models\Regions\Village;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model untuk data sekolah
 *
 * @property string $id
 * @property string|null $npsn
 * @property string $name
 * @property string|null $nss
 * @property string|null $edu_type
 * @property string|null $status
 * @property int|null $est_year
 * @property string|null $op_permit_no
 * @property \Illuminate\Support\Carbon|null $op_permit_date
 * @property string|null $accreditation
 * @property string|null $accred_score
 * @property int|null $accred_year
 * @property string|null $curriculum
 * @property string|null $village_id
 * @property string|null $district_id
 * @property string|null $regency_id
 * @property string|null $province_id
 * @property string|null $postal_code
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $address
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class School extends Model
{
    use HasUuids;

    protected $table = 'schools';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'npsn',
        'name',
        'nss',
        'edu_type',
        'status',
        'est_year',
        'op_permit_no',
        'op_permit_date',
        'accreditation',
        'accred_score',
        'accred_year',
        'curriculum',
        'village_id',
        'district_id',
        'regency_id',
        'province_id',
        'postal_code',
        'latitude',
        'longitude',
        'address'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'op_permit_date' => 'date',
            'est_year' => 'integer',
            'accred_year' => 'integer',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    /**
     * Relasi ke tanah milik sekolah
     */
    public function lands(): HasMany
    {
        return $this->hasMany(Land::class);
    }

    /**
     * Relasi ke bangunan sekolah
     */
    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    /**
     * Relasi ke ruangan sekolah
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Relasi ke fasilitas lainnya
     */
    public function otherFacilities(): HasMany
    {
        return $this->hasMany(OtherFacility::class);
    }

    /**
     * Relasi ke desa/kelurahan
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    /**
     * Relasi ke kecamatan
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    /**
     * Relasi ke kabupaten/kota
     */
    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'regency_id');
    }

    /**
     * Relasi ke provinsi
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    /**
     * Scope untuk sekolah negeri
     */
    public function scopePublic($query)
    {
        return $query->where('status', 'negeri');
    }

    /**
     * Scope untuk sekolah swasta
     */
    public function scopePrivate($query)
    {
        return $query->where('status', 'swasta');
    }

    /**
     * Accessor untuk alamat lengkap
     */
    public function getFullAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->address,
            $this->village?->name,
            $this->district?->name,
            $this->regency?->name,
            $this->province?->name,
            $this->postal_code
        ]));
    }

    /**
     * Hitung total luas tanah sekolah
     */
    public function getTotalLandAreaAttribute(): float
    {
        return $this->lands->sum('area');
    }

    /**
     * Hitung total luas bangunan sekolah
     */
    public function getTotalBuildingAreaAttribute(): float
    {
        return $this->buildings->sum('area');
    }
}
