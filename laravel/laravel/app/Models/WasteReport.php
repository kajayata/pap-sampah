<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WasteReport extends Model
{
    use HasFactory;

    // Status Constants
    public const STATUS_PENDING_VALIDATION = 'PENDING_VALIDATION';
    public const STATUS_VALIDATED = 'VALIDATED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_ASSIGNED = 'ASSIGNED';
    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    public const STATUS_PENDING_VERIFICATION = 'PENDING_VERIFICATION';
    public const STATUS_RESOLVED = 'RESOLVED';

    protected $fillable = [
        'report_code',
        'reported_by',
        'village_id',
        'category_id',
        'description',
        'status',
        'validated_by',
        'validated_at',
        'resolved_at',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'validated_at' => 'datetime',
            'resolved_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(WasteCategory::class, 'category_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(WasteReportPhoto::class, 'report_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(WasteReportStatusHistory::class, 'report_id');
    }

    public function cleanupTask(): HasOne
    {
        return $this->hasOne(CleanupTask::class, 'report_id');
    }

    protected $appends = ['status_label'];

    /**
     * Scope for active reports (reports that are still actively tracked on heatmaps).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_RESOLVED, self::STATUS_REJECTED]);
    }

    /**
     * Scope to automatically select latitude and longitude from PostGIS location point.
     */
    public function scopeWithCoordinates(Builder $query): Builder
    {
        if (empty($query->getQuery()->columns)) {
            $query->select('waste_reports.*');
        }

        return $query->selectRaw('ST_Y(waste_reports.location) as latitude, ST_X(waste_reports.location) as longitude');
    }

    public function getLatitudeAttribute($value)
    {
        if ($value !== null) {
            return (float) $value;
        }

        if (isset($this->attributes['location'])) {
            $point = \Illuminate\Support\Facades\DB::selectOne(
                'SELECT ST_Y(location) as lat FROM waste_reports WHERE id = ?',
                [$this->id]
            );
            return $point ? (float) $point->lat : null;
        }

        return null;
    }

    public function getLongitudeAttribute($value)
    {
        if ($value !== null) {
            return (float) $value;
        }

        if (isset($this->attributes['location'])) {
            $point = \Illuminate\Support\Facades\DB::selectOne(
                'SELECT ST_X(location) as lng FROM waste_reports WHERE id = ?',
                [$this->id]
            );
            return $point ? (float) $point->lng : null;
        }

        return null;
    }

    public function getStatusLabelAttribute(): ?string
    {
        if (empty($this->status)) {
            return null;
        }

        return match ($this->status) {
            self::STATUS_PENDING_VALIDATION => 'Menunggu Validasi',
            self::STATUS_VALIDATED => 'Tervalidasi',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_ASSIGNED => 'Ditugaskan',
            self::STATUS_IN_PROGRESS => 'Sedang Dibersihkan',
            self::STATUS_PENDING_VERIFICATION => 'Menunggu Verifikasi',
            self::STATUS_RESOLVED => 'Selesai',
            default => (string) $this->status,
        };
    }

    public function isPendingValidation(): bool
    {
        return $this->status === self::STATUS_PENDING_VALIDATION;
    }

    public function isValidated(): bool
    {
        return $this->status === self::STATUS_VALIDATED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }
}
