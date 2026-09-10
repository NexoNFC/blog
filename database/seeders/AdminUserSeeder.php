<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = (string) env('ADMIN_PASSWORD', 'password');

        $this->upsertAdministrator(
            email: (string) env('ADMIN_EMAIL', 'admin@fesc.edu.co'),
            name: (string) env('ADMIN_NAME', 'Administrador FESC'),
            password: $password,
        );

        $erickEmail = (string) env('ADMIN_ERICK_EMAIL', 'est_es.perez@fesc.edu.co');
        $santiagoEmail = (string) env('ADMIN_SANTIAGO_EMAIL', 'est_s_rueda@fesc.edu.co');

        $this->migrateEmail('erick@fesc.edu.co', $erickEmail);
        $this->migrateEmail('santiago@fesc.edu.co', $santiagoEmail);

        $this->upsertAdministrator(
            email: $erickEmail,
            name: (string) env('ADMIN_ERICK_NAME', 'Erick'),
            password: (string) env('ADMIN_ERICK_PASSWORD', $password),
        );

        $this->upsertAdministrator(
            email: $santiagoEmail,
            name: (string) env('ADMIN_SANTIAGO_NAME', 'Santiago'),
            password: (string) env('ADMIN_SANTIAGO_PASSWORD', $password),
        );

        $legacyEditor = User::query()->where('email', env('EDITOR_EMAIL', 'editor@fesc.edu.co'))->first();

        if ($legacyEditor !== null) {
            $legacyEditor->syncRoles([]);
            $legacyEditor->is_active = false;
            $legacyEditor->save();
        }
    }

    private function migrateEmail(string $from, string $to): void
    {
        if ($from === $to) {
            return;
        }

        $previous = User::query()->where('email', $from)->first();
        $target = User::query()->where('email', $to)->first();

        if ($previous === null) {
            return;
        }

        if ($target === null) {
            $previous->email = $to;
            $previous->save();

            return;
        }

        $previous->syncRoles([]);
        $previous->is_active = false;
        $previous->save();
    }

    private function upsertAdministrator(string $email, string $name, string $password): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        if (! $user->wasRecentlyCreated) {
            $user->forceFill([
                'name' => $name,
                'is_active' => true,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        }

        $user->syncRoles(['admin']);
    }
}
