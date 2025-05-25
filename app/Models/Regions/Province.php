<?php

namespace App\Models\Regions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Province model based on PERMENDAGRI 58/2021.
 */
class Province extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'slug', 'latitude', 'longitude'];

    public function regencies(): HasMany
    {
        return $this->hasMany(Regency::class, 'province_id');
    }

    public function scopeWhereProvinceId($query, $provinceId)
    {
        return $query->where('id', $provinceId);
    }
}
