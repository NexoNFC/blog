<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\News;
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

    public function test_user_without_admin_role_cannot_access_users_management(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_user_without_admin_role_cannot_archive_news(): void
    {
        $user = User::factory()->create();
        $news = News::factory()->draft()->create();

        $this->actingAs($user)
            ->delete(route('admin.news.destroy', $news))
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
        $news = News::factory()->draft()->create();

        $this->actingAs($admin)
            ->get(route('admin.news.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->delete(route('admin.news.destroy', $news))
            ->assertRedirect(route('admin.news.index'));

        $this->assertSame(ContentStatus::Archived, $news->fresh()->status);
    }

    public function test_guest_can_access_public_content(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_guest_cannot_access_admin_as_reader(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_user_without_admin_role_cannot_create_categories(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
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
