<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Institucional', 'slug' => 'institucional'],
            ['name' => 'Evento', 'slug' => 'evento'],
            ['name' => 'Comunicado', 'slug' => 'comunicado'],
            ['name' => 'Noticia', 'slug' => 'noticia'],
            ['name' => 'Externo', 'slug' => 'externo'],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']],
            );
        }
    }
}
