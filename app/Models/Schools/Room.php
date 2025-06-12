<?php

namespace App\Models\Schools;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Model untuk data ruangan
 *
 * @property string $id
 * @property string $school_id
 * @property int $room_ref_id
 * @property string $building_id
 * @property string|null $code
 * @property string $name
 * @property string|null $reg_no
 * @property int|null $floor
 * @property float|null $length
 * @property float|null $width
 * @property float|null $area
 * @property int|null $capacity
 * @property float|null $plaster_area
 * @property float|null $ceiling_area
 * @property float|null $wall_area
 * @property float|null $window_area
 * @property float|null $door_area
 * @property float|null $frame_len
 * @property float|null $floor_area
 * @property float|null $elec_area
 * @property int|null $elec_points
 * @property float|null $water_len
 * @property int|null $water_points
 * @property int|null $drain_len
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Room extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'rooms';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'school_id',
        'room_ref_id',
        'building_id',
        'code',
        'name',
        'reg_no',
        'floor',
        'length',
        'width',
        'area',
        'capacity',
        'plaster_area',
        'ceiling_area',
        'wall_area',
        'window_area',
        'door_area',
        'frame_len',
        'floor_area',
        'elec_area',
        'elec_points',
        'water_len',
        'water_points',
        'drain_len'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'area' => 'decimal:2',
            'plaster_area' => 'decimal:2',
            'ceiling_area' => 'decimal:2',
            'wall_area' => 'decimal:2',
            'window_area' => 'decimal:2',
            'door_area' => 'decimal:2',
            'frame_len' => 'decimal:2',
            'floor_area' => 'decimal:2',
            'elec_area' => 'decimal:2',
            'water_len' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke sekolah pemilik ruangan
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relasi ke referensi jenis ruangan
     */
    public function reference(): BelongsTo
    {
        return $this->belongsTo(RoomReference::class, 'room_ref_id');
    }

    /**
     * Relasi ke bangunan tempat ruangan berada
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Relasi ke kondisi ruangan
     */
    public function conditions(): MorphMany
    {
        return $this->morphMany(InfraCondition::class, 'entity');
    }

    /**
     * Relasi ke status hukum ruangan
     */
    public function legalStatuses(): MorphMany
    {
        return $this->morphMany(InfraLegal::class, 'entity');
    }

    /**
     * Relasi ke dokumen ruangan
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(InfraDocument::class, 'entity');
    }

    /**
     * Relasi ke riwayat perolehan ruangan
     */
    public function acquisitions(): MorphMany
    {
        return $this->morphMany(InfraAcquisition::class, 'entity');
    }

    /**
     * Relasi ke riwayat pemindahan ruangan
     */
    public function relocations(): MorphMany
    {
        return $this->morphMany(InfraRelocation::class, 'entity');
    }

    /**
     * Relasi ke user pembuat
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke user pengupdate
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Hitung kepadatan ruangan (orang per meter persegi)
     */
    public function getDensityAttribute(): ?float
    {
        if (!$this->capacity || !$this->area) return null;
        return $this->capacity / $this->area;
    }

    /**
     * Scope untuk ruangan dengan kapasitas di atas tertentu
     */
    public function scopeWithCapacityAbove($query, int $capacity)
    {
        return $query->where('capacity', '>', $capacity);
    }

    /**
     *
     */
    public function latestCondition()
    {
        return $this->morphOne(InfraCondition::class, 'entity')->latestOfMany('checked_at');
    }
}
