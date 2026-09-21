<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CleanupTaskWorker extends Model
{
    use HasFactory;

    public $timestamps = false;

    // Status Constants
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_ACCEPTED = 'ACCEPTED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_COMPLETED = 'COMPLETED';

    protected $fillable = [
        'task_id',
        'worker_id',
        'status',
        'rejection_reason',
        'assigned_at',
        'accepted_at',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'accepted_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(CleanupTask::class, 'task_id');
    }

    protected $appends = ['status_label'];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function getStatusLabelAttribute(): ?string
    {
        if (empty($this->status)) {
            return null;
        }

        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Konfirmasi',
            self::STATUS_ACCEPTED => 'Menerima Tugas',
            self::STATUS_REJECTED => 'Menolak Tugas',
            self::STATUS_COMPLETED => 'Selesai Dibersihkan',
            default => (string) $this->status,
        };
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
