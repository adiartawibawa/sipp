<?php

namespace App\Models\Schools;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Model untuk riwayat pemindahan infrastruktur
 *
 * @property string $id
 * @property string $entity_id
 * @property string $entity_type
 * @property string|null $from
 * @property string $to
 * @property \Illuminate\Support\Carbon $moved_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class InfraRelocation extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'infra_relocations';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'entity_id',
        'entity_type',
        'from',
        'to',
        'moved_at',
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
            'moved_at' => 'date',
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
     * Accessor untuk lokasi lengkap
     */
    public function getFullLocationAttribute(): string
    {
        return $this->from ? "Dari {$this->from} ke {$this->to}" : "Dipindah ke {$this->to}";
    }
}
