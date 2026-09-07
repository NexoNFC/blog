<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_access_admin(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_editor_can_access_dashboard(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_editor_cannot_access_users_management(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_editor_cannot_delete_news(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->delete(route('admin.news.destroy', 'noticia-demo'))
            ->assertForbidden();
    }

    public function test_admin_can_manage_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.users.create'))
            ->assertOk();
    }

    public function test_admin_can_manage_news(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.news.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->delete(route('admin.news.destroy', 'noticia-demo'))
            ->assertRedirect(route('admin.news.index'));
    }

    public function test_guest_can_access_public_content(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_guest_cannot_access_admin_as_reader(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_create_categories(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->post(route('admin.categories.store'))
            ->assertForbidden();
    }

    public function test_authenticated_user_without_admin_role_cannot_access_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
