<?php

namespace Database\Factories;

use App\Enums\ContentSourceKey;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Source>
 */
class SourceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'name' => 'Fuente de prueba',
            'base_url' => 'https://www.fesc.edu.co/portal/',
            'is_active' => true,
        ];
    }

    public function fesc(): static
    {
        return $this->state(fn (): array => [
            'key' => ContentSourceKey::Fesc->value,
            'name' => 'Portal oficial FESC',
            'base_url' => 'https://www.fesc.edu.co/portal/',
        ]);
    }

    public function instagram(): static
    {
        return $this->state(fn (): array => [
            'key' => ContentSourceKey::Instagram->value,
            'name' => 'Instagram FESC',
            'base_url' => 'https://www.instagram.com/fesc.edusuperior/',
        ]);
    }
}
