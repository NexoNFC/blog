<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Category;
use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'imported_content_id' => null,
            'source_id' => null,
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => fake()->paragraph(),
            'body' => fake()->paragraphs(3, true),
            'featured_image_path' => 'images/campus/fachada.jpg',
            'gallery' => [],
            'origin_url' => null,
            'origin_published_at' => null,
            'published_at' => null,
            'status' => ContentStatus::Draft,
            'processed_payload' => null,
            'admin_edited_at' => null,
            'event_starts_at' => null,
            'event_ends_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => ContentStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'status' => ContentStatus::Archived,
            'published_at' => now()->subMonth(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'status' => ContentStatus::Draft,
            'published_at' => null,
        ]);
    }
}
