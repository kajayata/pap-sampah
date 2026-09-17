<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CleanupTask extends Model
{
    use HasFactory;

    public $timestamps = false;

    // Status Constants
    public const STATUS_ASSIGNED = 'ASSIGNED';
    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    public const STATUS_PENDING_VERIFICATION = 'PENDING_VERIFICATION';
    public const STATUS_VERIFICATION_REJECTED = 'VERIFICATION_REJECTED';
    public const STATUS_COMPLETED = 'COMPLETED';

    protected $fillable = [
        'report_id',
        'assigned_by',
        'status',
        'assigned_at',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(WasteReport::class, 'report_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function workers(): HasMany
    {
        return $this->hasMany(CleanupTaskWorker::class, 'task_id');
    }

    protected $appends = ['status_label'];

    public function photos(): HasMany
    {
        return $this->hasMany(CleanupTaskPhoto::class, 'task_id');
    }

    public function getStatusLabelAttribute(): ?string
    {
        if (empty($this->status)) {
            return null;
        }

        return match ($this->status) {
            self::STATUS_ASSIGNED => 'Menunggu Diterima Petugas',
            self::STATUS_IN_PROGRESS => 'Sedang Dikerjakan',
            self::STATUS_PENDING_VERIFICATION => 'Menunggu Verifikasi Admin',
            self::STATUS_VERIFICATION_REJECTED => 'Pembersihan Ulang',
            self::STATUS_COMPLETED => 'Selesai',
            default => (string) $this->status,
        };
    }

    public function isAssigned(): bool
    {
        return $this->status === self::STATUS_ASSIGNED;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isPendingVerification(): bool
    {
        return $this->status === self::STATUS_PENDING_VERIFICATION;
    }

    public function isVerificationRejected(): bool
    {
        return $this->status === self::STATUS_VERIFICATION_REJECTED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
