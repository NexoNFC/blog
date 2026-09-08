<?php

namespace Database\Seeders;

use App\Enums\ContentSourceKey;
use App\Enums\ContentStatus;
use App\Enums\NfcPointStatus;
use App\Models\Category;
use App\Models\News;
use App\Models\NfcPoint;
use App\Models\Source;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $fesc = Source::query()->where('key', ContentSourceKey::Fesc->value)->firstOrFail();
        $categories = Category::query()->get()->keyBy('slug');

        foreach (DemoCatalog::contents() as $item) {
            $category = $categories->get($item['type']);
            $status = ContentStatus::from($item['status']);

            News::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'source_id' => $item['type'] === 'externo' ? $fesc->id : null,
                    'category_id' => $category?->id,
                    'title' => $item['title'],
                    'summary' => $item['summary'],
                    'body' => $item['body'],
                    'featured_image_path' => $item['image'] ?? null,
                    'origin_url' => $item['external_url'] ?? null,
                    'published_at' => $item['published_at'] ?? null,
                    'status' => $status,
                    'event_starts_at' => $item['event_starts_at'] ?? null,
                    'event_ends_at' => $item['event_ends_at'] ?? null,
                ],
            );
        }

        $news = News::query()->get()->keyBy('slug');

        foreach (DemoCatalog::nfcPoints() as $point) {
            $slug = $point['content_slugs'][0] ?? null;
            $associated = $slug !== null ? $news->get($slug) : null;

            NfcPoint::query()->updateOrCreate(
                ['code' => $point['code']],
                [
                    'identifier' => $point['identifier'],
                    'name' => $point['name'],
                    'location' => $point['location'],
                    'description' => $point['description'],
                    'status' => NfcPointStatus::from($point['status']),
                    'kind' => $point['kind'] ?? 'espacio',
                    'image_path' => $point['image'] ?? null,
                    'news_id' => $associated?->status === ContentStatus::Published ? $associated->id : null,
                ],
            );
        }
    }
}
