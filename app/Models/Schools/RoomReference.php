<?php

namespace App\Models\Schools;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model untuk referensi jenis ruangan
 *
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string|null $desc
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class RoomReference extends Model
{
    protected $table = 'room_refs';

    protected $fillable = ['name', 'code', 'desc'];

    /**
     * Relasi ke ruangan dengan referensi ini
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'room_ref_id');
    }

    /**
     * Accessor untuk nama lengkap dengan kode
     */
    public function getFullNameAttribute(): string
    {
        return $this->code ? "[{$this->code}] {$this->name}" : $this->name;
    }
}
