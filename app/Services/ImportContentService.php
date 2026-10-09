<?php

namespace App\Services;

use App\Enums\ContentStatus;
use App\Models\Category;
use App\Models\ImportedContent;
use App\Models\News;
use App\Models\Source;
use App\Support\ContentSummary;
use App\Support\FescContentCleaner;
use Illuminate\Support\Str;

class ImportContentService
{
    public function __construct(private FescContentCleaner $cleaner) {}

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
        $rawText = isset($payload['raw_text'])
            ? $this->cleaner->cleanText((string) $payload['raw_text'])
            : null;
        $rawHtml = isset($payload['raw_html'])
            ? $this->cleaner->cleanHtml((string) $payload['raw_html'])
            : null;

        return ImportedContent::query()->create([
            'source_id' => $source->id,
            'external_id' => $payload['external_id'] ?? null,
            'origin_url' => $originUrl,
            'origin_published_at' => $payload['origin_published_at'] ?? null,
            'extracted_at' => now(),
            'content_hash' => $payload['content_hash'] ?? hash('sha256', $originUrl.'|'.(string) $rawText),
            'title' => $payload['title'] ?? null,
            'raw_text' => $rawText,
            'raw_html' => $rawHtml,
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
        $body = $attributes['body'] ?? $this->cleaner->cleanText((string) $imported->raw_text);
        $summary = $attributes['summary'] ?? ContentSummary::make((string) $body);

        return News::query()->create([
            'imported_content_id' => $imported->id,
            'source_id' => $imported->source_id,
            'category_id' => $attributes['category_id'] ?? $this->categoryIdFromImport($imported),
            'title' => $title,
            'slug' => $attributes['slug'] ?? Str::slug($title).'-'.$imported->id,
            'summary' => $summary,
            'body' => $body,
            'featured_image_path' => $featured,
            'gallery' => $gallery,
            'origin_url' => $imported->origin_url,
            'origin_published_at' => $imported->origin_published_at,
            'status' => ContentStatus::Draft,
            'published_at' => null,
            'processed_payload' => [
                'presentation' => 'original',
                'original_title' => $title,
                'original_summary' => $summary,
                'original_body' => $body,
            ],
        ]);
    }

    /**
     * Repara HTML/texto ya guardado donde Joomla dejó el mensaje de email cloaking.
     */
    public function scrubStoredCloakArtifacts(): int
    {
        $updated = 0;

        ImportedContent::query()
            ->where(function ($query): void {
                $query->where('raw_html', 'like', '%joomla-hidden-mail%')
                    ->orWhere('raw_text', 'like', '%protegida contra los robots%')
                    ->orWhere('raw_text', 'like', '%protected from spambots%');
            })
            ->orderBy('id')
            ->each(function (ImportedContent $imported) use (&$updated): void {
                $rawHtml = $this->cleaner->cleanHtml((string) $imported->raw_html);
                $rawText = filled($imported->raw_html)
                    ? $this->cleaner->htmlToText((string) $imported->raw_html)
                    : $this->cleaner->cleanText((string) $imported->raw_text);

                if ($rawHtml === (string) $imported->raw_html && $rawText === (string) $imported->raw_text) {
                    return;
                }

                $imported->forceFill([
                    'raw_html' => $rawHtml !== '' ? $rawHtml : $imported->raw_html,
                    'raw_text' => $rawText,
                ])->save();

                $updated++;
            });

        News::query()
            ->with('importedContent')
            ->where(function ($query): void {
                $query->where('body', 'like', '%protegida contra los robots%')
                    ->orWhere('body', 'like', '%protected from spambots%')
                    ->orWhere('summary', 'like', '%protegida contra los robots%');
            })
            ->orderBy('id')
            ->each(function (News $news) use (&$updated): void {
                $importedText = $news->importedContent?->raw_text;
                $body = filled($importedText) && (
                    str_contains((string) $news->body, 'protegida contra los robots')
                    || str_contains((string) $news->body, 'protected from spambots')
                )
                    ? (string) $importedText
                    : $this->cleaner->cleanText((string) $news->body);

                $summary = $this->cleaner->cleanText((string) $news->summary);
                if (
                    str_contains($summary, 'protegida contra los robots')
                    || str_contains($summary, 'protected from spambots')
                ) {
                    $summary = ContentSummary::make($body);
                }

                $payload = is_array($news->processed_payload) ? $news->processed_payload : [];

                if (isset($payload['original_body']) && is_string($payload['original_body'])) {
                    $payload['original_body'] = filled($importedText)
                        ? (string) $importedText
                        : $this->cleaner->cleanText($payload['original_body']);
                }

                if (isset($payload['original_summary']) && is_string($payload['original_summary'])) {
                    $payload['original_summary'] = $this->cleaner->cleanText($payload['original_summary']);
                }

                if (
                    $body === (string) $news->body
                    && $summary === (string) $news->summary
                    && $payload === $news->processed_payload
                ) {
                    return;
                }

                $news->forceFill([
                    'body' => $body,
                    'summary' => $summary,
                    'processed_payload' => $payload,
                ])->save();

                $updated++;
            });

        $updated += $this->reformatContactEmailsInStoredBodies();

        return $updated;
    }

    /**
     * Separa " / correo@dominio" en un párrafo propio para lectura y mailto.
     * También reconstruye párrafos desde el HTML importado cuando aún está disponible.
     */
    private function reformatContactEmailsInStoredBodies(): int
    {
        $updated = 0;

        ImportedContent::query()
            ->where(function ($query): void {
                $query->whereNotNull('raw_html')
                    ->where('raw_html', '!=', '')
                    ->orWhere('raw_text', 'like', '%/%@%')
                    ->orWhere('raw_text', 'like', '%En la FESC%');
            })
            ->orderBy('id')
            ->each(function (ImportedContent $imported) use (&$updated): void {
                $rawHtml = filled($imported->raw_html)
                    ? $this->cleaner->cleanHtml((string) $imported->raw_html)
                    : (string) $imported->raw_html;
                $rawText = filled($rawHtml)
                    ? $this->cleaner->htmlToText($rawHtml)
                    : $this->cleaner->cleanText((string) $imported->raw_text);

                if (
                    $rawHtml === (string) $imported->raw_html
                    && $rawText === (string) $imported->raw_text
                ) {
                    return;
                }

                $imported->forceFill([
                    'raw_html' => $rawHtml !== '' ? $rawHtml : $imported->raw_html,
                    'raw_text' => $rawText,
                ])->save();

                $updated++;
            });

        News::query()
            ->with('importedContent')
            ->where(function ($query): void {
                $query->where('body', 'like', '%/%@%')
                    ->orWhere('body', 'like', '%En la FESC%');
            })
            ->orderBy('id')
            ->each(function (News $news) use (&$updated): void {
                $importedText = $news->importedContent?->raw_text;
                $body = filled($importedText) && $news->admin_edited_at === null
                    ? (string) $importedText
                    : $this->cleaner->cleanText((string) $news->body);

                if ($body === (string) $news->body) {
                    return;
                }

                $payload = is_array($news->processed_payload) ? $news->processed_payload : [];
                if (isset($payload['original_body']) && is_string($payload['original_body'])) {
                    $payload['original_body'] = filled($importedText)
                        ? (string) $importedText
                        : $this->cleaner->cleanText($payload['original_body']);
                }

                $news->forceFill([
                    'body' => $body,
                    'processed_payload' => $payload,
                ])->save();

                $updated++;
            });

        return $updated;
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
