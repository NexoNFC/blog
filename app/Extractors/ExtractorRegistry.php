<?php

namespace App\Extractors;

use App\Contracts\SourceExtractor;
use App\Enums\ContentSourceKey;

class ExtractorRegistry
{
    public function __construct(
        private FescPortalExtractor $fescPortalExtractor,
    ) {}

    public function for(string $sourceKey): ?SourceExtractor
    {
        return match ($sourceKey) {
            ContentSourceKey::Fesc->value => $this->fescPortalExtractor,
            default => null,
        };
    }
}
