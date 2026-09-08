<?php

namespace Database\Factories;

use App\Enums\NfcPointStatus;
use App\Models\NfcPoint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<NfcPoint>
 */
class NfcPointFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'identifier' => 'NFC-'.fake()->unique()->numerify('###'),
            'code' => Str::slug($name),
            'name' => ucfirst($name),
            'location' => 'Campus FESC',
            'description' => fake()->sentence(),
            'status' => NfcPointStatus::Active,
            'kind' => 'bloque',
            'image_path' => 'images/campus/fachada.jpg',
            'news_id' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => NfcPointStatus::Inactive,
        ]);
    }
}
