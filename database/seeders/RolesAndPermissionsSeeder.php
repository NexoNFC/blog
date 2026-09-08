<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    public const PERMISSIONS = [
        'users.view',
        'users.create',
        'users.update',
        'users.delete',
        'users.manage-roles',
        'news.view',
        'news.create',
        'news.update',
        'news.delete',
        'news.publish',
        'categories.view',
        'categories.create',
        'categories.update',
        'categories.delete',
        'nfc.view',
        'nfc.create',
        'nfc.update',
        'nfc.delete',
        'nfc.manage-content',
        'statistics.view',
        'statistics.view-scans',
        'statistics.view-content',
        'settings.view',
        'settings.update',
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions(self::PERMISSIONS);

        $editor = Role::query()
            ->where('name', 'editor')
            ->where('guard_name', 'web')
            ->first();

        if ($editor !== null) {
            $editor->users()->detach();
            $editor->syncPermissions([]);
            $editor->delete();
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
