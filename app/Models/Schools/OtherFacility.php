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
 * Model untuk fasilitas lainnya
 *
 * @property string $id
 * @property string $school_id
 * @property string $category
 * @property string $name
 * @property string|null $code
 * @property int $qty
 * @property string|null $specs
 * @property float|null $value
 * @property int|null $acq_year
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class OtherFacility extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'other_facil';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'infra_cat_id',
        'name',
        'code',
        'qty',
        'specs',
        'value',
        'acq_year'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'value' => 'decimal:2',
            'acq_year' => 'integer',
        ];
    }

    /**
     * Relasi ke sekolah pemilik fasilitas
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
     * Relasi ke kondisi bangunan
     */
    public function conditions(): MorphMany
    {
        return $this->morphMany(InfraCondition::class, 'entity');
    }

    /**
     * Relasi ke riwayat perolehan fasilitas
     */
    public function acquisitions(): MorphMany
    {
        return $this->morphMany(InfraAcquisition::class, 'entity');
    }

    /**
     * Relasi ke riwayat pemindahan fasilitas
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
     * Hitung usia fasilitas
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->acq_year) return null;
        return now()->year - $this->acq_year;
    }

    /**
     * Scope untuk fasilitas dengan nilai di atas tertentu
     */
    public function scopeWithValueAbove($query, float $value)
    {
        return $query->where('value', '>', $value);
    }

    public function latestCondition()
    {
        return $this->morphOne(InfraCondition::class, 'entity')->latestOfMany('checked_at');
    }
}
