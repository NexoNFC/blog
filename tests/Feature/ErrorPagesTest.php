<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    public function test_not_found_page_uses_branded_error_view(): void
    {
        $this->get('/ruta-inexistente-ppa')
            ->assertNotFound()
            ->assertSee('404', false)
            ->assertSee('Página no encontrada')
            ->assertSee('Vaya, no encontramos esa página')
            ->assertSee('Información en campus')
            ->assertSee('Inicio');
    }

    public function test_forbidden_page_uses_branded_error_view(): void
    {
        Route::get('/__test-forbidden', fn () => abort(403));

        $this->get('/__test-forbidden')
            ->assertForbidden()
            ->assertSee('403', false)
            ->assertSee('Acceso denegado')
            ->assertSee('No tienes permiso para ver esta página');
    }

    public function test_server_error_page_uses_branded_error_view(): void
    {
        Route::get('/__test-server-error', fn () => abort(500));

        $this->get('/__test-server-error')
            ->assertStatus(500)
            ->assertSee('500', false)
            ->assertSee('Error interno del servidor');
    }

    public function test_service_unavailable_page_uses_branded_error_view(): void
    {
        Route::get('/__test-unavailable', fn () => abort(503));

        $this->get('/__test-unavailable')
            ->assertStatus(503)
            ->assertSee('503', false)
            ->assertSee('Plataforma en mantenimiento');
    }

    public function test_too_many_requests_page_uses_branded_error_view(): void
    {
        Route::get('/__test-throttle', fn () => abort(429));

        $this->get('/__test-throttle')
            ->assertStatus(429)
            ->assertSee('429', false)
            ->assertSee('Demasiadas solicitudes');
    }

    public function test_page_expired_uses_branded_error_view(): void
    {
        Route::get('/__test-expired', fn () => abort(419));

        $this->get('/__test-expired')
            ->assertStatus(419)
            ->assertSee('Sesión expirada')
            ->assertSee('Tu sesión ha caducado');
    }
}
