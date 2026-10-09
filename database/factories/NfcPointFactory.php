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
            'panorama_path' => null,
            'tour_enabled' => false,
            'tour_description' => null,
            'nfc_marker_theta' => null,
            'nfc_marker_phi' => null,
            'news_id' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'status' => NfcPointStatus::Inactive,
        ]);
    }

    public function withTour(?string $panoramaPath = 'images/tours/EntradaAvenida5.jpg'): static
    {
        return $this->state(fn (): array => [
            'panorama_path' => $panoramaPath,
            'tour_enabled' => true,
            'tour_description' => 'Gira la vista y busca el punto rojo: ahí está la tarjeta NFC.',
            'nfc_marker_theta' => 0.5,
            'nfc_marker_phi' => 0.1,
        ]);
    }
}
