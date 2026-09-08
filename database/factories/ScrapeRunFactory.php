<?php

namespace Database\Factories;

use App\Enums\ScrapeRunStatus;
use App\Models\ScrapeRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScrapeRun>
 */
class ScrapeRunFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => ScrapeRunStatus::Pending,
            'trigger' => 'scheduled',
            'started_at' => null,
            'finished_at' => null,
            'contents_found' => 0,
            'contents_new' => 0,
            'contents_processed' => 0,
            'news_created' => 0,
            'error_message' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => ScrapeRunStatus::Completed,
            'started_at' => now()->subHour(),
            'finished_at' => now(),
        ]);
    }
}
