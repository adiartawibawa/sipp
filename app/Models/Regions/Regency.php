<?php

namespace App\Models\Regions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Regency or City model.
 */
class Regency extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'province_id', 'name', 'slug', 'latitude', 'longitude'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'regency_id');
    }

    public function scopeWhereRegencyId($query, $regencyId)
    {
        return $query->where('id', $regencyId);
    }

    public function scopeWhereProvinceId($query, $provinceId)
    {
        return $query->where('province_id', $provinceId);
    }
}
