<?php

namespace App\Support;

class DemoCatalog
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function contents(): array
    {
        return [
            [
                'slug' => 'servicios-biblioteca',
                'title' => 'Conoce los servicios disponibles en la Biblioteca',
                'summary' => 'Horarios, salas de estudio, préstamo de material y acompañamiento académico para la comunidad FESC.',
                'body' => "La Biblioteca FESC ofrece espacios de estudio individual y grupal, acceso a bases de datos y orientación para la búsqueda de información.\n\nConsulta los horarios actualizados y los servicios de préstamo disponibles para estudiantes y docentes.",
                'type' => 'institucional',
                'status' => 'publicado',
                'published_at' => '2026-03-01',
                'external_url' => null,
                'image' => 'images/landing/news-students-study.png',
            ],
            [
                'slug' => 'evento-lectura-cultura',
                'title' => 'Evento de lectura y cultura',
                'summary' => 'Encuentro cultural con lecturas abiertas y actividades para estudiantes de todas las facultades.',
                'body' => "Te invitamos al encuentro de lectura y cultura en el auditorio institucional.\n\nParticipa de las lecturas abiertas, conversatorios y actividades diseñadas para fortalecer la vida universitaria.",
                'type' => 'evento',
                'status' => 'publicado',
                'published_at' => '2026-03-10',
                'event_starts_at' => '2026-03-20 14:00',
                'event_ends_at' => '2026-03-20 17:00',
                'external_url' => null,
                'image' => 'images/landing/news-event-hall.png',
            ],
            [
                'slug' => 'comunicado-calendario',
                'title' => 'Comunicado: actualización del calendario académico',
                'summary' => 'La Dirección Académica informa ajustes en fechas de evaluaciones y matrícula.',
                'body' => "Se comunica a la comunidad educativa la actualización del calendario académico del periodo en curso.\n\nRevisa las nuevas fechas de evaluaciones, matrícula y actividades institucionales.",
                'type' => 'comunicado',
                'status' => 'publicado',
                'published_at' => '2026-03-05',
                'external_url' => null,
                'image' => 'images/landing/news-campus-walk.png',
            ],
            [
                'slug' => 'evento-oficial-fesc',
                'title' => 'FESC anuncia nuevo evento académico',
                'summary' => 'Conoce los detalles del evento en el sitio oficial de la institución.',
                'body' => 'La institución invita a la comunidad a participar del próximo evento académico. En esta plataforma encuentras el resumen; los detalles oficiales están en el portal FESC.',
                'type' => 'externo',
                'status' => 'publicado',
                'published_at' => '2026-03-08',
                'external_url' => 'https://fesc.edu.co/portal/',
                'image' => 'images/landing/campus-abstract.png',
            ],
            [
                'slug' => 'borrador-semana-universitaria',
                'title' => 'Semana Universitaria 2026 (borrador)',
                'summary' => 'Programación preliminar de actividades culturales y académicas.',
                'body' => 'Contenido en preparación para la Semana Universitaria.',
                'type' => 'noticia',
                'status' => 'borrador',
                'published_at' => null,
                'external_url' => null,
                'image' => null,
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function contentBySlug(string $slug): ?array
    {
        foreach (self::contents() as $content) {
            if ($content['slug'] === $slug) {
                return $content;
            }
        }

        return null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function nfcPoints(): array
    {
        return [
            [
                'code' => 'entrada-avenida-4',
                'identifier' => 'NFC-006',
                'name' => 'Entrada Avenida 4',
                'location' => 'Acceso Avenida 4',
                'description' => 'Bienvenida e información general al ingresar por Avenida 4.',
                'status' => 'activo',
                'content_slugs' => ['comunicado-calendario', 'evento-oficial-fesc'],
                'scans_demo' => 64,
            ],
            [
                'code' => 'entrada-avenida-5',
                'identifier' => 'NFC-007',
                'name' => 'Entrada Avenida 5',
                'location' => 'Acceso Avenida 5',
                'description' => 'Orientación e información institucional al ingresar por Avenida 5.',
                'status' => 'activo',
                'content_slugs' => ['comunicado-calendario', 'evento-oficial-fesc'],
                'scans_demo' => 91,
            ],
            [
                'code' => 'bloque-a-piso-1',
                'identifier' => 'NFC-001',
                'name' => 'Bloque A · Piso 1',
                'location' => 'Bloque A, piso 1',
                'description' => 'Contenido asociado al primer piso del Bloque A.',
                'status' => 'activo',
                'content_slugs' => ['comunicado-calendario', 'evento-lectura-cultura'],
                'scans_demo' => 42,
            ],
            [
                'code' => 'bloque-a-piso-3',
                'identifier' => 'NFC-002',
                'name' => 'Bloque A · Piso 3',
                'location' => 'Bloque A, piso 3',
                'description' => 'Contenido asociado al tercer piso del Bloque A.',
                'status' => 'activo',
                'content_slugs' => ['evento-lectura-cultura'],
                'scans_demo' => 28,
            ],
            [
                'code' => 'bloque-b-piso-2',
                'identifier' => 'NFC-003',
                'name' => 'Bloque B · Piso 2',
                'location' => 'Bloque B, piso 2',
                'description' => 'Contenido asociado al segundo piso del Bloque B.',
                'status' => 'activo',
                'content_slugs' => ['comunicado-calendario'],
                'scans_demo' => 35,
            ],
            [
                'code' => 'bloque-c-piso-4',
                'identifier' => 'NFC-004',
                'name' => 'Bloque C · Piso 4',
                'location' => 'Bloque C, piso 4',
                'description' => 'Contenido asociado al cuarto piso del Bloque C.',
                'status' => 'activo',
                'content_slugs' => ['evento-oficial-fesc'],
                'scans_demo' => 19,
            ],
            [
                'code' => 'biblioteca',
                'identifier' => 'NFC-005',
                'name' => 'Biblioteca Moisés San Juan López',
                'location' => 'Biblioteca Moisés San Juan López',
                'description' => 'Servicios, noticias y eventos relacionados con la biblioteca.',
                'status' => 'activo',
                'content_slugs' => [
                    'servicios-biblioteca',
                    'evento-lectura-cultura',
                    'comunicado-calendario',
                    'evento-oficial-fesc',
                ],
                'scans_demo' => 128,
            ],
            [
                'code' => 'auditorio-avenida-5',
                'identifier' => 'NFC-008',
                'name' => 'Auditorio Avenida 5',
                'location' => 'Auditorio Avenida 5',
                'description' => 'Información de actividades y eventos del auditorio.',
                'status' => 'activo',
                'content_slugs' => ['evento-lectura-cultura', 'evento-oficial-fesc'],
                'scans_demo' => 47,
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function nfcByCode(string $code): ?array
    {
        foreach (self::nfcPoints() as $point) {
            if ($point['code'] === $code) {
                return $point;
            }
        }

        return null;
    }

    /**
     * Ubicaciones conocidas para la landing (sin inventar espacios).
     *
     * @return list<array<string, mixed>>
     */
    public static function campusLocations(): array
    {
        return [
            [
                'name' => 'Bloque A',
                'detail' => '3 pisos · puntos NFC por nivel',
                'icon' => 'building',
                'badge' => 'Bloque',
                'href' => route('nfc.show', 'bloque-a-piso-1'),
            ],
            [
                'name' => 'Bloque B',
                'detail' => '2 pisos · puntos NFC por nivel',
                'icon' => 'building',
                'badge' => 'Bloque',
                'href' => route('nfc.show', 'bloque-b-piso-2'),
            ],
            [
                'name' => 'Bloque C',
                'detail' => '4 pisos · puntos NFC por nivel',
                'icon' => 'building',
                'badge' => 'Bloque',
                'href' => route('nfc.show', 'bloque-c-piso-4'),
            ],
            [
                'name' => 'Entrada Avenida 4',
                'detail' => 'Acceso al campus',
                'icon' => 'door',
                'badge' => 'Acceso',
                'href' => route('nfc.show', 'entrada-avenida-4'),
            ],
            [
                'name' => 'Entrada Avenida 5',
                'detail' => 'Acceso al campus',
                'icon' => 'door',
                'badge' => 'Acceso',
                'href' => route('nfc.show', 'entrada-avenida-5'),
            ],
            [
                'name' => 'Auditorio Avenida 5',
                'detail' => 'Espacio de eventos y encuentros',
                'icon' => 'stage',
                'badge' => 'Espacio',
                'href' => route('nfc.show', 'auditorio-avenida-5'),
            ],
            [
                'name' => 'Biblioteca Moisés San Juan López',
                'detail' => 'Servicios e información del lugar',
                'icon' => 'book',
                'badge' => 'Espacio',
                'href' => route('nfc.show', 'biblioteca'),
            ],
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    public static function landingSteps(): array
    {
        return [
            [
                'icon' => 'nfc',
                'title' => 'Encuentra un punto NFC',
                'description' => 'Están en entradas, bloques, biblioteca y auditorio.',
            ],
            [
                'icon' => 'phone',
                'title' => 'Acerca tu teléfono',
                'description' => 'Sin apps extra: el chip abre una URL del punto.',
            ],
            [
                'icon' => 'spark',
                'title' => 'Descubre el contenido',
                'description' => 'Noticias, eventos y mensajes de ese lugar.',
            ],
            [
                'icon' => 'compass',
                'title' => 'Explora más información',
                'description' => 'Sigue navegando o visita el sitio oficial FESC.',
            ],
        ];
    }

    /**
     * @param  list<string>  $slugs
     * @return list<array<string, mixed>>
     */
    public static function contentsBySlugs(array $slugs): array
    {
        $items = [];

        foreach ($slugs as $slug) {
            $content = self::contentBySlug($slug);

            if ($content !== null) {
                $items[] = $content;
            }
        }

        return $items;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function publishedContents(): array
    {
        return array_values(array_filter(
            self::contents(),
            fn (array $content): bool => $content['status'] === 'publicado'
        ));
    }
}
