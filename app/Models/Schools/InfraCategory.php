<?php

namespace App\Models\Schools;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model untuk kategori infrastruktur
 *
 * @property int $id
 * @property string $name
 * @property string|null $desc
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class InfraCategory extends Model
{
    protected $table = 'infra_cats';

    protected $fillable = ['name', 'desc'];

    /**
     * Relasi ke tanah dalam kategori ini
     */
    public function lands(): HasMany
    {
        return $this->hasMany(Land::class, 'infra_cat_id');
    }

    /**
     * Relasi ke bangunan dalam kategori ini
     */
    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'infra_cat_id');
    }

    /**
     * Scope untuk kategori aktif
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('name');
    }
}
