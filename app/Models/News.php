<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'imported_content_id',
    'source_id',
    'category_id',
    'title',
    'slug',
    'summary',
    'body',
    'featured_image_path',
    'gallery',
    'origin_url',
    'origin_published_at',
    'published_at',
    'status',
    'processed_payload',
    'admin_edited_at',
    'event_starts_at',
    'event_ends_at',
])]
class News extends Model
{
    /** @use HasFactory<NewsFactory> */
    use HasFactory;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'origin_published_at' => 'datetime',
            'published_at' => 'datetime',
            'admin_edited_at' => 'datetime',
            'event_starts_at' => 'datetime',
            'event_ends_at' => 'datetime',
            'gallery' => 'array',
            'processed_payload' => 'array',
            'status' => ContentStatus::class,
        ];
    }

    /**
     * @return BelongsTo<ImportedContent, $this>
     */
    public function importedContent(): BelongsTo
    {
        return $this->belongsTo(ImportedContent::class);
    }

    /**
     * @return BelongsTo<Source, $this>
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    /**
     * @return HasMany<NfcPoint, $this>
     */
    public function nfcPoints(): HasMany
    {
        return $this->hasMany(NfcPoint::class);
    }

    /**
     * @return HasMany<NewsView, $this>
     */
    public function views(): HasMany
    {
        return $this->hasMany(NewsView::class);
    }

    /**
     * @return HasMany<NfcScan, $this>
     */
    public function nfcScans(): HasMany
    {
        return $this->hasMany(NfcScan::class);
    }

    /**
     * @param  Builder<News>  $query
     * @return Builder<News>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::Published);
    }

    /**
     * @return array<string, mixed>
     */
    public function toPublicArray(): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'summary' => $this->summary,
            'body' => $this->body,
            'type' => $this->category?->slug ?? 'noticia',
            'status' => $this->status->value,
            'published_at' => $this->published_at?->toDateString(),
            'event_starts_at' => $this->event_starts_at?->format('Y-m-d H:i'),
            'event_ends_at' => $this->event_ends_at?->format('Y-m-d H:i'),
            'external_url' => $this->origin_url,
            'image' => $this->featured_image_path,
        ];
    }
}
