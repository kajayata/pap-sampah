<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'village_id',
        'name',
        'email',
        'password',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function cleanupTaskWorkers(): HasMany
    {
        return $this->hasMany(CleanupTaskWorker::class, 'worker_id');
    }

    public function wasteReports(): HasMany
    {
        return $this->hasMany(WasteReport::class, 'reported_by');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin_kecamatan');
    }

    public function isVillageAdmin(): bool
    {
        return $this->hasRole('admin_desa');
    }

    /**
     * Determine if this user can edit or delete a given news article.
     * Super Admin can manage all news.
     * Village Admin can only manage articles authored by themselves or users in the same village (excluding Super Admin articles).
     */
    public function canManageNews(News $article): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $article->loadMissing('author.role');
        $author = $article->author;

        if (!$author) {
            return false;
        }

        // Cannot manage articles authored by Super Admin
        if ($author->isSuperAdmin()) {
            return false;
        }

        // Own article
        if ($article->author_id === $this->id) {
            return true;
        }

        // Same village admin
        if ($this->village_id && $author->village_id === $this->village_id) {
            return true;
        }

        return false;
    }
}
