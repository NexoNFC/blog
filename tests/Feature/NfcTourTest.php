<?php

namespace Tests\Feature;

use App\Models\NfcPoint;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NfcTourTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_visitor_can_open_enabled_tour_for_an_nfc_point(): void
    {
        $point = NfcPoint::factory()->withTour('images/tours/BloqueB.jpeg')->create([
            'code' => 'bloque-b',
            'name' => 'Bloque B',
        ]);

        $this->get(route('nfc.tour', $point))
            ->assertOk()
            ->assertSee('Vista 360°')
            ->assertSee('Bloque B')
            ->assertSee('nfcTourViewer', false)
            ->assertSee('Tarjeta NFC')
            ->assertDontSee('Otros recorridos');
    }

    public function test_tour_without_panorama_redirects_to_nfc_point_page(): void
    {
        $point = NfcPoint::factory()->create([
            'code' => 'bloque-a',
            'tour_enabled' => true,
            'panorama_path' => null,
        ]);

        $this->get(route('nfc.tour', $point))
            ->assertRedirect(route('nfc.show', $point));
    }

    public function test_administrator_can_manage_tour_and_nfc_marker(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $point = NfcPoint::factory()->create([
            'code' => 'biblioteca',
            'name' => 'Biblioteca',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.nfc.tour.edit', $point))
            ->assertOk()
            ->assertSee('Vista 360°')
            ->assertSee('Marcador NFC')
            ->assertSee('Moneda colocada en el panorama', false)
            ->assertSee('arrastra y suelta el panorama aquí')
            ->assertDontSee('Nuevo hotspot')
            ->assertDontSee('Punto destino')
            ->assertDontSee('Eliminar marcador al guardar')
            ->assertDontSee('Quitar panorama actual');

        $this->actingAs($admin)
            ->from(route('admin.nfc.tour.edit', $point))
            ->patch(route('admin.nfc.tour.update', $point), [
                'tour_enabled' => '1',
                'tour_description' => 'Busca el punto rojo para encontrar la tarjeta NFC.',
                'panorama' => UploadedFile::fake()->image('biblioteca-360.jpg', 1600, 800),
                'nfc_marker_theta' => '1.2500',
                'nfc_marker_phi' => '0.1500',
            ])
            ->assertRedirect(route('admin.nfc.tour.edit', $point));

        $point->refresh();
        $this->assertTrue($point->tour_enabled);
        $this->assertNotNull($point->panorama_path);
        $this->assertEqualsWithDelta(1.25, $point->nfc_marker_theta, 0.0001);
        $this->assertEqualsWithDelta(0.15, $point->nfc_marker_phi, 0.0001);
        Storage::disk('public')->assertExists($point->panorama_path);

        $this->actingAs($admin)
            ->from(route('admin.nfc.tour.edit', $point))
            ->patch(route('admin.nfc.tour.update', $point), [
                'tour_enabled' => '1',
                'tour_description' => 'Busca el punto rojo para encontrar la tarjeta NFC.',
                'nfc_marker_theta' => '',
                'nfc_marker_phi' => '',
            ])
            ->assertRedirect(route('admin.nfc.tour.edit', $point));

        $point->refresh();
        $this->assertNull($point->nfc_marker_theta);
        $this->assertNull($point->nfc_marker_phi);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('nfc.tour', $point), false);
    }

    public function test_failed_panorama_upload_shows_spanish_toast_alert(): void
    {
        $admin = User::factory()->admin()->create();
        $point = NfcPoint::factory()->create([
            'code' => 'cafeteria',
            'name' => 'Cafetería',
        ]);

        $temp = UploadedFile::fake()->create('panorama.jpg', 100, 'image/jpeg');
        $file = UploadedFile::createFromBase(
            new \Symfony\Component\HttpFoundation\File\UploadedFile(
                $temp->getPathname(),
                $temp->getClientOriginalName(),
                $temp->getClientMimeType(),
                \UPLOAD_ERR_INI_SIZE,
                true,
            ),
        );

        $this->actingAs($admin)
            ->from(route('admin.nfc.tour.edit', $point))
            ->followingRedirects()
            ->patch(route('admin.nfc.tour.update', $point), [
                'tour_enabled' => '0',
                'panorama' => $file,
            ])
            ->assertOk()
            ->assertSee('data-alert-host', false)
            ->assertSee('No se pudo guardar el recorrido')
            ->assertSee('La imagen panorámica supera el tamaño máximo permitido')
            ->assertDontSee('The panorama failed to upload.');
    }
}
