<?php

namespace App\Models\Schools;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Model untuk data tanah
 *
 * @property string $id
 * @property string $school_id
 * @property int $infra_cat_id
 * @property string $name
 * @property string|null $cert_no
 * @property float|null $length
 * @property float|null $width
 * @property float|null $area
 * @property float|null $avail_area
 * @property string|null $ownership
 * @property float|null $njop
 * @property string|null $notes
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Land extends Model
{
    use HasUuids;

    protected $table = 'lands';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'infra_cat_id',
        'name',
        'cert_no',
        'length',
        'width',
        'area',
        'avail_area',
        'ownership',
        'njop',
        'notes'
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
            'avail_area' => 'decimal:2',
            'njop' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke sekolah pemilik tanah
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
     * Relasi ke bangunan di tanah ini
     */
    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    /**
     * Relasi ke kondisi tanah
     */
    public function conditions(): MorphMany
    {
        return $this->morphMany(InfraCondition::class, 'entity');
    }

    /**
     * Relasi ke status hukum tanah
     */
    public function legalStatuses(): MorphMany
    {
        return $this->morphMany(InfraLegal::class, 'entity');
    }

    /**
     * Relasi ke dokumen tanah
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(InfraDocument::class, 'entity');
    }

    /**
     * Relasi ke riwayat perolehan tanah
     */
    public function acquisitions(): MorphMany
    {
        return $this->morphMany(InfraAcquisition::class, 'entity');
    }

    /**
     * Relasi ke riwayat pemindahan tanah
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
     * Hitung persentase area yang tersedia
     */
    public function getAvailablePercentageAttribute(): float
    {
        if (!$this->area) return 0;
        return ($this->avail_area / $this->area) * 100;
    }

    /**
     * Scope untuk tanah dengan sertifikat
     */
    public function scopeWithCertificate($query)
    {
        return $query->whereNotNull('cert_no');
    }
}
