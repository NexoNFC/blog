<?php

namespace Database\Seeders;

use App\Enums\NfcPointStatus;
use App\Models\NfcPoint;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach (DemoCatalog::nfcPoints() as $point) {
            NfcPoint::query()->firstOrCreate(
                ['code' => $point['code']],
                [
                    'identifier' => $point['identifier'],
                    'name' => $point['name'],
                    'location' => $point['location'],
                    'description' => $point['description'],
                    'status' => NfcPointStatus::from($point['status']),
                    'kind' => $point['kind'] ?? 'espacio',
                    'image_path' => $point['image'] ?? null,
                    'news_id' => null,
                ],
            );
        }

        $this->call(NfcTourSeeder::class);
    }
}
