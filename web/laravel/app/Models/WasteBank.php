<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WasteBank extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'village_id',
        'name',
        'address',
        'phone',
        'description',
        'is_active',
        'location',
    ];

    protected $appends = [
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function getLatitudeAttribute(): ?float
    {
        return isset($this->attributes['lat']) ? (float) $this->attributes['lat'] : null;
    }

    public function getLongitudeAttribute(): ?float
    {
        return isset($this->attributes['lng']) ? (float) $this->attributes['lng'] : null;
    }

    public function scopeWithCoordinates($query)
    {
        return $query->select('waste_banks.*')
            ->selectRaw('ST_Y(location) as lat, ST_X(location) as lng');
    }
}
