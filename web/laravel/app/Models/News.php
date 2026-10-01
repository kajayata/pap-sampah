<?php

namespace App\Models;

use App\Services\ImageStorageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    use HasFactory;

    /*
     * Tabel news hanya menggunakan created_at.
     * updated_at tidak digunakan.
     */
    public const UPDATED_AT = null;

    /*
     * Status berita.
     */
    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_PUBLISHED = 'PUBLISHED';
    public const STATUS_ARCHIVED = 'ARCHIVED';

    /*
     * Kolom yang boleh diisi menggunakan mass assignment.
     */
    protected $fillable = [
        'title',
        'slug',
        'thumbnail_storage_key',
        'content',
        'author_id',
        'status',
        'published_at',
    ];

    /*
     * Tambahkan thumbnail_url secara otomatis
     * ketika model diubah menjadi array / JSON.
     */
    protected $appends = [
        'thumbnail_url',
    ];

    /*
     * Casting tipe data.
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /*
     * Relasi ke User sebagai penulis berita.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'author_id'
        );
    }

    /*
     * URL thumbnail.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_storage_key) {
            return null;
        }

        return ImageStorageService::getUrl(
            $this->thumbnail_storage_key
        );
    }

    /*
     * Scope untuk berita yang benar-benar sudah tayang.
     *
     * Syarat:
     * 1. Status harus PUBLISHED.
     * 2. published_at harus tersedia.
     * 3. Waktu publish tidak boleh berada di masa depan.
     */
    public function scopePublished($query)
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where(
                'published_at',
                '<=',
                now()
            );
    }

    /*
     * Mengecek apakah berita sudah benar-benar tayang.
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    /*
     * Mengecek apakah berita masih Draft.
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /*
     * Mengecek apakah berita sudah Archived.
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }
}
