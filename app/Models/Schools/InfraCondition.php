<?php

namespace App\Models\Schools;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
class InfraCondition extends Model
{
    use HasUuids, HasFactory;

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

    // /**
    //  * Get the default condition by slug
    //  */
    // public static function getDefaultBySlug(string $slug): ?array
    // {
    //     return collect(static::defaultInfraCondition())
    //         ->firstWhere('slug', $slug);
    // }

    // /**
    //  * Create a new condition from default data
    //  */
    // public static function createFromDefault(string $slug, Model $entity): ?InfraCondition
    // {
    //     $default = static::getDefaultBySlug($slug);

    //     if (!$default) {
    //         return null;
    //     }

    //     return $entity->conditions()->create([
    //         'condition' => $default['condition'],
    //         'slug' => $default['slug'],
    //         'percentage' => $default['percentage'],
    //         'notes' => $default['notes'],
    //         'checked_at' => now(),
    //     ]);
    // }

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
}
