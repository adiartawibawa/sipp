<?php

namespace App\Models\Schools;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Model untuk status hukum infrastruktur
 *
 * @property string $id
 * @property string $entity_id
 * @property string $entity_type
 * @property string $status
 * @property string|null $doc_no
 * @property \Illuminate\Support\Carbon|null $doc_date
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class InfraLegal extends Model implements HasMedia
{
    use HasUuids, HasFactory;
    use InteractsWithMedia;

    protected $table = 'infra_legal';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'entity_id',
        'entity_type',
        'status',
        'doc_no',
        'doc_date',
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
            'doc_date' => 'date',
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
     * Accessor untuk status lengkap dengan nomor dokumen
     */
    public function getFullStatusAttribute(): string
    {
        return $this->doc_no ? "{$this->status} (No. {$this->doc_no})" : $this->status;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('legal_documents')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }
}
