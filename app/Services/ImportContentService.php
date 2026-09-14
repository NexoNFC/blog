<?php

namespace App\Services;

use App\Enums\ContentStatus;
use App\Models\Category;
use App\Models\ImportedContent;
use App\Models\News;
use App\Models\Source;
use Illuminate\Support\Str;

class ImportContentService
{
    /**
     * @param  array{
     *     origin_url: string,
     *     external_id?: string|null,
     *     title?: string|null,
     *     raw_text?: string|null,
     *     raw_html?: string|null,
     *     media?: array<string, mixed>|null,
     *     metadata?: array<string, mixed>|null,
     *     origin_published_at?: mixed,
     *     content_hash?: string|null
     * }  $payload
     */
    public function importOrFind(Source $source, array $payload): ImportedContent
    {
        $existing = $this->findExisting($source, $payload);

        if ($existing !== null) {
            return $existing;
        }

        $originUrl = $payload['origin_url'];
        $rawText = $payload['raw_text'] ?? null;

        return ImportedContent::query()->create([
            'source_id' => $source->id,
            'external_id' => $payload['external_id'] ?? null,
            'origin_url' => $originUrl,
            'origin_published_at' => $payload['origin_published_at'] ?? null,
            'extracted_at' => now(),
            'content_hash' => $payload['content_hash'] ?? hash('sha256', $originUrl.'|'.(string) $rawText),
            'title' => $payload['title'] ?? null,
            'raw_text' => $rawText,
            'raw_html' => $payload['raw_html'] ?? null,
            'media' => $payload['media'] ?? [],
            'metadata' => $payload['metadata'] ?? [],
            'status' => ContentStatus::Imported,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function draftFromImport(ImportedContent $imported, array $attributes = []): News
    {
        $existing = $imported->news;

        if ($existing !== null) {
            return $existing;
        }

        $title = $attributes['title'] ?? $imported->title ?? 'Contenido importado';
        $media = is_array($imported->media) ? $imported->media : [];
        $featured = $attributes['featured_image_path'] ?? ($media[0]['url'] ?? null);
        $gallery = $attributes['gallery'] ?? array_values(array_filter(array_map(
            fn (mixed $item): ?string => is_array($item) ? ($item['url'] ?? null) : null,
            array_slice($media, 1),
        )));

        return News::query()->create([
            'imported_content_id' => $imported->id,
            'source_id' => $imported->source_id,
            'category_id' => $attributes['category_id'] ?? $this->categoryIdFromImport($imported),
            'title' => $title,
            'slug' => $attributes['slug'] ?? Str::slug($title).'-'.$imported->id,
            'summary' => $attributes['summary'] ?? Str::limit((string) $imported->raw_text, 220),
            'body' => $attributes['body'] ?? (string) $imported->raw_text,
            'featured_image_path' => $featured,
            'gallery' => $gallery,
            'origin_url' => $imported->origin_url,
            'origin_published_at' => $imported->origin_published_at,
            'status' => ContentStatus::Draft,
            'published_at' => null,
            'processed_payload' => [
                'presentation' => 'original',
                'original_title' => $title,
                'original_summary' => $attributes['summary'] ?? Str::limit((string) $imported->raw_text, 220),
                'original_body' => (string) $imported->raw_text,
            ],
        ]);
    }

    /**
     * @param  array{origin_url: string, external_id?: string|null}  $payload
     */
    private function findExisting(Source $source, array $payload): ?ImportedContent
    {
        $byUrl = ImportedContent::query()
            ->where('source_id', $source->id)
            ->where('origin_url', $payload['origin_url'])
            ->first();

        if ($byUrl !== null) {
            return $byUrl;
        }

        $externalId = $payload['external_id'] ?? null;

        if ($externalId === null || $externalId === '') {
            return null;
        }

        return ImportedContent::query()
            ->where('source_id', $source->id)
            ->where('external_id', $externalId)
            ->first();
    }

    private function categoryIdFromImport(ImportedContent $imported): ?int
    {
        $slug = $imported->metadata['category_slug'] ?? null;

        if (! is_string($slug) || $slug === '') {
            return null;
        }

        $id = Category::query()->where('slug', $slug)->value('id');

        return is_numeric($id) ? (int) $id : null;
    }
}
