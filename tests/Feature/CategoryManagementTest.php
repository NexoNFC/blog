<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_access_category_management(): void
    {
        $this->get(route('admin.categories.index'))->assertRedirect(route('login'));
        $this->get(route('admin.categories.create'))->assertRedirect(route('login'));
    }

    public function test_user_without_admin_role_cannot_create_categories(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.categories.create'))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('admin.categories.store'), [
                'name' => 'Bienestar',
            ])
            ->assertForbidden();
    }

    public function test_administrator_can_create_a_category(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.categories.create'))
            ->assertOk()
            ->assertSee('Registrar categoría');

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Bienestar universitario',
            ])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('categories', [
            'name' => 'Bienestar universitario',
            'slug' => 'bienestar-universitario',
        ]);
    }

    public function test_administrator_cannot_create_a_duplicate_category_name(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['name' => 'Evento', 'slug' => 'evento']);

        $this->actingAs($admin)
            ->from(route('admin.categories.create'))
            ->post(route('admin.categories.store'), [
                'name' => 'Evento',
            ])
            ->assertRedirect(route('admin.categories.create'))
            ->assertSessionHasErrors('name');
    }

    public function test_administrator_can_update_a_category_name_without_changing_the_slug(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create([
            'name' => 'Evento',
            'slug' => 'evento',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.categories.update', $category), [
                'name' => 'Eventos de campus',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $category->refresh();

        $this->assertSame('Eventos de campus', $category->name);
        $this->assertSame('evento', $category->slug);
    }

    public function test_administrator_can_delete_an_unused_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_administrator_cannot_delete_a_category_assigned_to_news(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        News::factory()->create(['category_id' => $category->id]);

        $this->actingAs($admin)
            ->from(route('admin.categories.index'))
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('alert.type', 'danger');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }
}
