<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_user_without_admin_role_cannot_view_or_create_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.users.create'))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('admin.users.store'), [
                'name' => 'Usuario no autorizado',
                'email' => 'noautorizado@fesc.edu.co',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'admin',
                'is_active' => '1',
            ])
            ->assertForbidden();
    }

    public function test_administrator_cannot_assign_an_editor_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.create'))
            ->post(route('admin.users.store'), [
                'name' => 'Editor Campus',
                'email' => 'editor.campus@fesc.edu.co',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'editor',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.create'))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', [
            'email' => 'editor.campus@fesc.edu.co',
        ]);
    }

    public function test_administrator_can_create_another_administrator(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Administrador Dos',
                'email' => 'admin2@fesc.edu.co',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'admin',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.index'));

        $created = User::query()->where('email', 'admin2@fesc.edu.co')->first();

        $this->assertNotNull($created);
        $this->assertTrue($created->hasRole('admin'));
    }

    public function test_administrator_can_update_another_administrator(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->admin()->create([
            'email' => 'otro@fesc.edu.co',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $other), [
                'name' => 'Administrador actualizado',
                'email' => $other->email,
                'role' => 'admin',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame('Administrador actualizado', $other->fresh()->name);
        $this->assertTrue($other->fresh()->hasRole('admin'));
    }

    public function test_user_without_admin_role_cannot_update_or_delete_users(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->admin()->create([
            'email' => 'otro@fesc.edu.co',
        ]);

        $this->actingAs($user)
            ->patch(route('admin.users.update', $other), [
                'name' => 'Hack',
                'email' => $other->email,
                'role' => 'admin',
                'is_active' => '1',
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->delete(route('admin.users.destroy', $other))
            ->assertForbidden();
    }

    public function test_seeder_creates_erick_and_santiago_as_administrators(): void
    {
        $this->seed(AdminUserSeeder::class);

        $erick = User::query()->where('email', 'est_es.perez@fesc.edu.co')->first();
        $santiago = User::query()->where('email', 'est_s_rueda@fesc.edu.co')->first();

        $this->assertNotNull($erick);
        $this->assertNotNull($santiago);
        $this->assertTrue($erick->hasRole('admin'));
        $this->assertTrue($santiago->hasRole('admin'));
        $this->assertTrue($erick->is_active);
        $this->assertTrue($santiago->is_active);
    }
}
