<?php

namespace App\Models\Regions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Village (Desa/Kelurahan) model.
 */
class Village extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'district_id',
        'name',
        'slug',
        'postal_code',
        'latitude',
        'longitude',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function scopeWhereVillageId($query, $villageId)
    {
        return $query->where('id', $villageId);
    }

    public function scopeWhereDistrictId($query, $districtId)
    {
        return $query->where('district_id', $districtId);
    }
}
