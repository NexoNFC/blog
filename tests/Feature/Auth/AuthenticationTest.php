<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_login_screen_is_accessible_without_authentication(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Iniciar sesión')
            ->assertSee('Correo electrónico')
            ->assertSee('Contraseña')
            ->assertSee('Recordarme')
            ->assertDontSee('Registrarse')
            ->assertDontSee('Crear cuenta')
            ->assertDontSee('términos y condiciones', false);
    }

    public function test_legacy_login_path_redirects_to_admin_login(): void
    {
        $this->get('/login')->assertRedirect('/admin/login');
    }

    public function test_admin_panel_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_administrator_can_authenticate(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_users_without_staff_role_cannot_authenticate(): void
    {
        $user = User::factory()->create();

        $this->from('/admin/login')->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_invalid_credentials_show_a_danger_alert(): void
    {
        $user = User::factory()->admin()->create();

        $this->from('/admin/login')->post('/admin/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertRedirect('/admin/login');

        $this->get('/admin/login')
            ->assertSeeInOrder([
                'Ingresa con tu cuenta de administrador FESC.',
                'No fue posible iniciar sesión',
                'El correo electrónico o la contraseña no son correctos.',
                'Correo electrónico',
                'toastHost',
                'toast-viewport',
                'data-alert-host',
            ], false)
            ->assertSee('x-teleport="body"', false)
            ->assertSee('timeout\u0022:8000', false)
            ->assertDontSee('Las credenciales no coinciden', false);

        $this->assertGuest();
    }

    public function test_expired_csrf_on_login_redirects_with_alert(): void
    {
        $request = Request::create('/admin/login', 'POST', [
            'email' => 'admin@fesc.edu.co',
            'password' => 'password',
        ]);
        $request->headers->set('Accept', 'text/html');

        $session = $this->app->make('session.store');
        $session->start();
        $request->setLaravelSession($session);

        $response = $this->app->make(ExceptionHandler::class)
            ->render($request, new HttpException(419, 'CSRF token mismatch.'));

        $this->assertTrue($response->isRedirect(route('login')));
        $this->assertSame('warning', $session->get('alert.type'));
        $this->assertSame('Sesión expirada', $session->get('alert.title'));
    }

    public function test_missing_fields_show_a_required_fields_alert(): void
    {
        $this->from('/admin/login')->post('/admin/login', [
            'email' => '',
            'password' => '',
        ])->assertRedirect('/admin/login');

        $this->get('/admin/login')
            ->assertSee('toastHost', false)
            ->assertSee('data-alert-host', false)
            ->assertSee('No fue posible continuar')
            ->assertSee('Existen campos obligatorios pendientes.');
    }

    public function test_users_can_logout_and_see_a_success_alert(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/admin/login');

        $this->assertGuest();

        $this->get('/admin/login')
            ->assertSee('toastHost', false)
            ->assertSee('data-alert-host', false)
            ->assertSee('La sesión se cerró correctamente.')
            ->assertSee('timeout\u0022:5000', false);
    }

    public function test_public_registration_routes_do_not_exist(): void
    {
        $this->get('/register')->assertNotFound();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();
    }

    public function test_readers_can_access_public_pages_without_authentication(): void
    {
        $this->get('/')->assertOk();
        $this->assertGuest();
    }
}
