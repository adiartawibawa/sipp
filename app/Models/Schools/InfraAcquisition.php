<?php

namespace App\Models\Schools;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Model untuk riwayat perolehan infrastruktur
 *
 * @property string $id
 * @property string $entity_id
 * @property string $entity_type
 * @property string $source
 * @property float|null $amount
 * @property int|null $year
 * @property string|null $method
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class InfraAcquisition extends Model
{
    protected $table = 'infra_acquisitions';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'entity_id',
        'entity_type',
        'source',
        'amount',
        'year',
        'method',
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
            'amount' => 'decimal:2',
            'year' => 'integer',
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
     * Scope untuk perolehan tahun tertentu
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }
}
