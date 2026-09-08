<?php

namespace Tests\Feature;

use App\Models\User;
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

    public function test_editor_cannot_view_or_create_users(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.users.index'))
            ->assertForbidden();

        $this->actingAs($editor)
            ->get(route('admin.users.create'))
            ->assertForbidden();

        $this->actingAs($editor)
            ->post(route('admin.users.store'), [
                'name' => 'Usuario no autorizado',
                'email' => 'noautorizado@fesc.edu.co',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'editor',
                'is_active' => '1',
            ])
            ->assertForbidden();
    }

    public function test_administrator_can_create_an_editor(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Editor Campus',
                'email' => 'editor.campus@fesc.edu.co',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'editor',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHasNoErrors();

        $created = User::query()->where('email', 'editor.campus@fesc.edu.co')->first();

        $this->assertNotNull($created);
        $this->assertTrue($created->hasRole('editor'));
        $this->assertTrue($created->is_active);
        $this->assertNotNull($created->email_verified_at);
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

    public function test_administrator_can_update_a_user_role(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $editor), [
                'name' => $editor->name,
                'email' => $editor->email,
                'role' => 'admin',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertTrue($editor->fresh()->hasRole('admin'));
        $this->assertFalse($editor->fresh()->hasRole('editor'));
    }

    public function test_editor_cannot_update_or_delete_users(): void
    {
        $editor = User::factory()->editor()->create();
        $other = User::factory()->editor()->create([
            'email' => 'otro@fesc.edu.co',
        ]);

        $this->actingAs($editor)
            ->patch(route('admin.users.update', $other), [
                'name' => 'Hack',
                'email' => $other->email,
                'role' => 'admin',
                'is_active' => '1',
            ])
            ->assertForbidden();

        $this->actingAs($editor)
            ->delete(route('admin.users.destroy', $other))
            ->assertForbidden();
    }
}
