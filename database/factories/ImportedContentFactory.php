<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\ImportedContent;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ImportedContent>
 */
class ImportedContentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $url = fake()->unique()->url();

        return [
            'source_id' => Source::factory(),
            'external_id' => fake()->unique()->uuid(),
            'origin_url' => $url,
            'origin_published_at' => now()->subDays(2),
            'extracted_at' => now(),
            'content_hash' => hash('sha256', $url),
            'title' => fake()->sentence(6),
            'raw_text' => fake()->paragraphs(2, true),
            'raw_html' => null,
            'media' => [],
            'metadata' => [],
            'status' => ContentStatus::Imported,
            'processing_error' => null,
        ];
    }
}
