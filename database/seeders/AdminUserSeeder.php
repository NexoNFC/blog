<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@fesc.edu.co')],
            [
                'name' => env('ADMIN_NAME', 'Administrador FESC'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
        $admin->syncRoles(['admin']);

        $editor = User::query()->updateOrCreate(
            ['email' => env('EDITOR_EMAIL', 'editor@fesc.edu.co')],
            [
                'name' => env('EDITOR_NAME', 'Editor FESC'),
                'password' => Hash::make(env('EDITOR_PASSWORD', 'password')),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
        $editor->syncRoles(['editor']);
    }
}
