<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\NewsView;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NewsView>
 */
class NewsViewFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'news_id' => News::factory()->published(),
            'viewed_at' => now(),
        ];
    }
}
