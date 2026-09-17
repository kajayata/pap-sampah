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
