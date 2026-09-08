<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Database\Factories\ImportedContentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'source_id',
    'external_id',
    'origin_url',
    'origin_published_at',
    'extracted_at',
    'content_hash',
    'title',
    'raw_text',
    'raw_html',
    'media',
    'metadata',
    'status',
    'processing_error',
])]
class ImportedContent extends Model
{
    /** @use HasFactory<ImportedContentFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'origin_published_at' => 'datetime',
            'extracted_at' => 'datetime',
            'media' => 'array',
            'metadata' => 'array',
            'status' => ContentStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Source, $this>
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * @return HasOne<News, $this>
     */
    public function news(): HasOne
    {
        return $this->hasOne(News::class);
    }
}
