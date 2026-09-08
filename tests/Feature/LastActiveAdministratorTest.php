<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AdministratorIntegrityService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LastActiveAdministratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_the_last_active_administrator_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('alert.type', 'danger')
            ->assertSessionHas('alert.message');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
        $this->assertSame(1, app(AdministratorIntegrityService::class)->activeAdministratorCount());

        $this->get(route('admin.users.index'))
            ->assertSee('No se puede completar la operación')
            ->assertSee('último administrador activo');
    }

    public function test_the_last_active_administrator_cannot_be_deactivated(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->patch(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'admin',
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.users.edit', $admin))
            ->assertSessionHas('alert.type', 'danger');

        $this->assertTrue($admin->fresh()->is_active);
        $this->assertSame(1, app(AdministratorIntegrityService::class)->activeAdministratorCount());
    }

    public function test_the_last_active_administrator_cannot_lose_the_admin_role(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $admin))
            ->patch(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'editor',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.edit', $admin))
            ->assertSessionHasErrors('role');

        $this->assertTrue($admin->fresh()->hasRole('admin'));
        $this->assertSame(1, app(AdministratorIntegrityService::class)->activeAdministratorCount());
    }

    public function test_an_administrator_can_be_removed_when_another_active_administrator_exists(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->admin()->create([
            'email' => 'admin2@fesc.edu.co',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $other))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $other->id]);
        $this->assertSame(1, app(AdministratorIntegrityService::class)->activeAdministratorCount());
        $this->assertTrue($admin->fresh()->is_active);
        $this->assertTrue($admin->fresh()->hasRole('admin'));
    }

    public function test_inactive_administrators_do_not_count_as_the_required_active_administrator(): void
    {
        $active = User::factory()->admin()->create();
        $inactive = User::factory()->admin()->inactive()->create([
            'email' => 'inactivo@fesc.edu.co',
        ]);

        $this->actingAs($active)
            ->from(route('admin.users.edit', $active))
            ->patch(route('admin.users.update', $active), [
                'name' => $active->name,
                'email' => $active->email,
                'role' => 'editor',
                'is_active' => '1',
            ])
            ->assertSessionHasErrors('role');

        $this->assertTrue($active->fresh()->hasRole('admin'));
        $this->assertTrue($inactive->fresh()->exists);
        $this->assertSame(1, app(AdministratorIntegrityService::class)->activeAdministratorCount());
    }
}
