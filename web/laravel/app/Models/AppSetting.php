<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    public const CREATED_AT = null;

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Helper to get setting value by key.
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        $setting = static::find($key);
        return $setting ? $setting->value : $default;
    }
}
