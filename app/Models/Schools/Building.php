<?php

namespace App\Models\Schools;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Model untuk data bangunan
 *
 * @property string $id
 * @property string $school_id
 * @property int $infra_cat_id
 * @property string|null $land_id
 * @property string|null $code
 * @property string $name
 * @property float|null $length
 * @property float|null $width
 * @property float|null $area
 * @property string|null $ownership
 * @property string|null $borrow_status
 * @property float|null $asset_value
 * @property int|null $floors
 * @property int|null $build_year
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $permit_date
 * @property float|null $foundation_vol
 * @property float|null $roof_vol
 * @property float|null $truss_len
 * @property float|null $rafter_len
 * @property float|null $batten_len
 * @property float|null $roof_area
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Building extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'buildings';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'infra_cat_id',
        'land_id',
        'code',
        'name',
        'length',
        'width',
        'area',
        'ownership',
        'borrow_status',
        'asset_value',
        'floors',
        'build_year',
        'notes',
        'permit_date',
        'foundation_vol',
        'roof_vol',
        'truss_len',
        'rafter_len',
        'batten_len',
        'roof_area'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'area' => 'decimal:2',
            'asset_value' => 'decimal:2',
            'foundation_vol' => 'decimal:2',
            'roof_vol' => 'decimal:2',
            'truss_len' => 'decimal:2',
            'rafter_len' => 'decimal:2',
            'batten_len' => 'decimal:2',
            'roof_area' => 'decimal:2',
            'permit_date' => 'date',
            'build_year' => 'integer',
        ];
    }

    /**
     * Relasi ke sekolah pemilik bangunan
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relasi ke kategori infrastruktur
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(InfraCategory::class, 'infra_cat_id');
    }

    /**
     * Relasi ke tanah tempat bangunan berdiri
     */
    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }

    /**
     * Relasi ke ruangan dalam bangunan
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Relasi ke kondisi bangunan
     */
    public function conditions(): MorphMany
    {
        return $this->morphMany(InfraCondition::class, 'entity');
    }

    /**
     * Relasi ke status hukum bangunan
     */
    public function legalStatuses(): MorphMany
    {
        return $this->morphMany(InfraLegal::class, 'entity');
    }

    /**
     * Relasi ke dokumen bangunan
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(InfraDocument::class, 'entity');
    }

    /**
     * Relasi ke riwayat perolehan bangunan
     */
    public function acquisitions(): MorphMany
    {
        return $this->morphMany(InfraAcquisition::class, 'entity');
    }

    /**
     * Relasi ke riwayat pemindahan bangunan
     */
    public function relocations(): MorphMany
    {
        return $this->morphMany(InfraRelocation::class, 'entity');
    }

    /**
     * Relasi ke user pembuat
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke user pengupdate
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Hitung usia bangunan
     */
    public function getBuildingAgeAttribute(): ?int
    {
        if (!$this->build_year) return null;
        return now()->year - $this->build_year;
    }

    /**
     * Scope untuk bangunan dengan nilai aset di atas tertentu
     */
    public function scopeWithAssetValueAbove($query, float $value)
    {
        return $query->where('asset_value', '>', $value);
    }
}
