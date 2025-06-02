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
