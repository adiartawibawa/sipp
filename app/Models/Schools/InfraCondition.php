<?php

namespace App\Models\Schools;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Model untuk kondisi infrastruktur
 *
 * @property string $id
 * @property string $entity_id
 * @property string $entity_type
 * @property string $condition
 * @property string|null $slug
 * @property float $percentage
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $checked_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class InfraCondition extends Model implements HasMedia
{
    use HasUuids, HasFactory;
    use InteractsWithMedia;

    protected $table = 'infra_conditions';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'entity_id',
        'entity_type',
        'condition',
        'slug',
        'percentage',
        'notes',
        'checked_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:2',
            'checked_at' => 'date',
        ];
    }

    public static function defaultInfraCondition()
    {
        return [
            [
                'condition' => 'baik',
                'slug' => 'good',
                'percentage' => 100,
                'notes' => 'Kondisi sempurna tanpa kerusakan'
            ],
            [
                'condition' => 'rusak_ringan',
                'slug' => 'light_damage',
                'percentage' => 70,
                'notes' => 'Kerusakan kecil, masih bisa berfungsi'
            ],
            [
                'condition' => 'rusak_sedang',
                'slug' => 'medium_damage',
                'percentage' => 40,
                'notes' => 'Kerusakan signifikan, perlu perbaikan'
            ],
            [
                'condition' => 'rusak_berat',
                'slug' => 'heavy_damage',
                'percentage' => 10,
                'notes' => 'Kerusakan parah, tidak bisa digunakan'
            ],
            [
                'condition' => 'tidak_layak',
                'slug' => 'unusable',
                'percentage' => 0,
                'notes' => 'Tidak memenuhi standar keselamatan'
            ]
        ];
    }

    /**
     * Relasi polymorphic ke entitas terkait
     */
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope untuk kondisi baik
     */
    public function scopeGood($query)
    {
        return $query->where('condition', 'good');
    }

    /**
     * Scope untuk kondisi rusak ringan
     */
    public function scopeLightDamage($query)
    {
        return $query->where('condition', 'light');
    }

    /**
     * Scope untuk kondisi rusak berat
     */
    public function scopeHeavyDamage($query)
    {
        return $query->where('condition', 'heavy');
    }

    /**
     * Accessor untuk status lengkap dengan persentase
     */
    public function getFullConditionAttribute(): string
    {
        return "{$this->condition} ({$this->percentage}%)";
    }

    /**
     * Accessor untuk format tanggal pemeriksaan
     */
    public function getFormattedCheckedAtAttribute(): string
    {
        return $this->checked_at?->format('d F Y') ?? '-';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('condition_photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages();
    }
}
