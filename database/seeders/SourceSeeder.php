<?php

namespace Database\Seeders;

use App\Enums\ContentSourceKey;
use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    public function run(): void
    {
        Source::query()->updateOrCreate(
            ['key' => ContentSourceKey::Fesc->value],
            [
                'name' => 'Portal oficial FESC',
                'base_url' => 'https://www.fesc.edu.co/portal/',
                'is_active' => true,
            ],
        );

        Source::query()->updateOrCreate(
            ['key' => ContentSourceKey::Instagram->value],
            [
                'name' => 'Instagram FESC',
                'base_url' => 'https://www.instagram.com/fesc.edusuperior/',
                'is_active' => true,
            ],
        );
    }
}
