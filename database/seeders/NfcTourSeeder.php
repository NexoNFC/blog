<?php

namespace Database\Seeders;

use App\Models\NfcPoint;
use App\Support\TourPanoramaBuilder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Throwable;

class NfcTourSeeder extends Seeder
{
    /**
     * Panorámicas equirectangular 2:1 (JPG) en public/images/tours.
     *
     * @return array<string, array{path: string, theta: float, phi: float, description: string}>
     */
    public static function tours(): array
    {
        return [
            'entrada-avenida-5' => [
                'path' => 'images/tours/EntradaAvenida5.jpg',
                'theta' => 1.05,
                'phi' => -0.08,
                'description' => 'Gira la vista sobre la Entrada Avenida 5 y busca el punto rojo: ahí está la tarjeta NFC.',
            ],
            'entrada-avenida-4' => [
                'path' => 'images/tours/SalidaFesc.jpeg',
                'theta' => -1.15,
                'phi' => -0.05,
                'description' => 'Recorre el acceso por Avenida 4 y localiza el marcador rojo de la tarjeta NFC.',
            ],
            'bloque-a' => [
                'path' => 'images/tours/BloqueB.jpeg',
                'theta' => 0.95,
                'phi' => 0.06,
                'description' => 'Explora el Bloque A en 360° y encuentra el punto rojo de la tarjeta NFC.',
            ],
            'bloque-b' => [
                'path' => 'images/tours/BloqueB.jpeg',
                'theta' => -0.85,
                'phi' => 0.04,
                'description' => 'Gira la vista del Bloque B y busca el punto rojo: ahí está la tarjeta NFC.',
            ],
            'bloque-c' => [
                'path' => 'images/tours/BloqueC.jpg',
                'theta' => 1.25,
                'phi' => 0.1,
                'description' => 'Recorre el Bloque C y localiza el marcador rojo de la tarjeta NFC.',
            ],
            'biblioteca' => [
                'path' => 'images/tours/DireccionDeSoftware.jpeg',
                'theta' => -1.05,
                'phi' => -0.02,
                'description' => 'Gira la vista en la biblioteca y encuentra el punto rojo de la tarjeta NFC.',
            ],
            'auditorio-avenida-5' => [
                'path' => 'images/tours/AuditorioAvenida5.jpg',
                'theta' => 0.9,
                'phi' => 0.05,
                'description' => 'Explora el Auditorio Avenida 5 y busca el punto rojo de la tarjeta NFC.',
            ],
            'cafeteria' => [
                'path' => 'images/tours/Cafeteria.jpeg',
                'theta' => 0.75,
                'phi' => -0.04,
                'description' => 'Recorre la cafetería en 360° y localiza el marcador rojo de la tarjeta NFC.',
            ],
        ];
    }

    public function run(): void
    {
        $this->ensurePanoramaAssets();

        foreach (self::tours() as $code => $tour) {
            $absolute = public_path($tour['path']);

            if (! File::isFile($absolute)) {
                $this->command?->warn("Panorama ausente para {$code}: {$tour['path']}");

                continue;
            }

            $point = NfcPoint::query()->where('code', $code)->first();

            if ($point === null) {
                $this->command?->warn("Punto NFC no encontrado: {$code}");

                continue;
            }

            $updates = [];

            // Panorama + tour público solo si aún no están configurados.
            if (blank($point->panorama_path)) {
                $updates['panorama_path'] = $tour['path'];
                $updates['tour_enabled'] = true;
                $updates['tour_description'] = $tour['description'];
            }

            // Punto rojo NFC solo si aún no hay marcador (no pisa el del admin).
            if ($point->nfc_marker_theta === null || $point->nfc_marker_phi === null) {
                $updates['nfc_marker_theta'] = $tour['theta'];
                $updates['nfc_marker_phi'] = $tour['phi'];
            }

            if ($updates === []) {
                $this->command?->line("Tour y marcador ya configurados, se omite: {$code}");

                continue;
            }

            $point->forceFill($updates)->save();

            $parts = [];
            if (array_key_exists('panorama_path', $updates)) {
                $parts[] = 'panorama';
            }
            if (array_key_exists('nfc_marker_theta', $updates)) {
                $parts[] = 'punto rojo NFC';
            }

            $this->command?->info('Tour actualizado ('.implode(' + ', $parts)."): {$code}");
        }
    }

    private function ensurePanoramaAssets(): void
    {
        $sources = storage_path('app/tour-sources');
        $hasSources = File::isDirectory($sources)
            && collect(TourPanoramaBuilder::demoManifest())
                ->keys()
                ->contains(fn (string $name): bool => File::isFile($sources.DIRECTORY_SEPARATOR.$name));

        if (! $hasSources) {
            return;
        }

        $missing = collect(TourPanoramaBuilder::demoManifest())
            ->contains(fn (string $path): bool => ! File::isFile(public_path($path)));

        // Si hay fuentes y falta alguna panorámica pública, generarlas.
        // También regenera si el usuario corre tours:build-panoramas a mano.
        if (! $missing) {
            return;
        }

        try {
            Artisan::call('tours:build-panoramas', [], $this->command?->getOutput());
        } catch (Throwable $e) {
            $this->command?->warn('No se pudieron generar panorámicas automáticamente: '.$e->getMessage());
        }
    }
}
