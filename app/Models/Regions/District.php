<?php

namespace App\Models\Regions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * District (Kecamatan) model.
 */
class District extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'regency_id', 'name', 'slug', 'latitude', 'longitude'];

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'regency_id');
    }

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class, 'district_id');
    }

    public function scopeWhereDistrictId($query, $districtId)
    {
        return $query->where('id', $districtId);
    }

    public function scopeWhereRegencyId($query, $regencyId)
    {
        return $query->where('regency_id', $regencyId);
    }
}
