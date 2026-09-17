<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CleanupTaskPhoto extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    public const TYPE_BEFORE = 'BEFORE';
    public const TYPE_AFTER = 'AFTER';

    protected $appends = ['url'];

    protected $fillable = [
        'task_id',
        'type',
        'storage_key',
        'mime_type',
        'file_size',
        'width',
        'height',
        'captured_at',
        'uploaded_by',
    ];

    public function getUrlAttribute(): ?string
    {
        return \App\Services\ImageStorageService::getUrl($this->storage_key);
    }

    public function scopeWithCoordinates($query)
    {
        if (empty($query->getQuery()->columns)) {
            $query->select('cleanup_task_photos.*');
        }

        return $query->selectRaw('ST_Y(cleanup_task_photos.location) as latitude, ST_X(cleanup_task_photos.location) as longitude');
    }

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'captured_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(CleanupTask::class, 'task_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
