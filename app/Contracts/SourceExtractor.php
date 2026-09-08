<?php

namespace App\Contracts;

use App\Enums\ContentSourceKey;
use App\Models\Source;

interface SourceExtractor
{
    public function sourceKey(): ContentSourceKey;

    /**
     * @return list<array{
     *     origin_url: string,
     *     external_id?: string|null,
     *     title?: string|null,
     *     raw_text?: string|null,
     *     raw_html?: string|null,
     *     media?: array<int, array<string, mixed>>,
     *     metadata?: array<string, mixed>,
     *     origin_published_at?: mixed,
     *     content_hash?: string|null
     * }>
     */
    public function extract(Source $source): array;
}
