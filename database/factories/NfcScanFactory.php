<?php

namespace Database\Factories;

use App\Models\NfcPoint;
use App\Models\NfcScan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NfcScan>
 */
class NfcScanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nfc_point_id' => NfcPoint::factory(),
            'news_id' => null,
            'scanned_at' => now(),
        ];
    }
}
