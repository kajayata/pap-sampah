<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WasteReportPhoto extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $appends = ['url'];

    protected $fillable = [
        'report_id',
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

    public function report(): BelongsTo
    {
        return $this->belongsTo(WasteReport::class, 'report_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
