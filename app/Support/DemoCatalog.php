<?php

namespace App\Support;

class DemoCatalog
{
    /**
     * Contenido de demostración usado por el seeder hasta que exista extracción automática.
     *
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
                'image' => 'images/campus/estudiantes-fuente.jpg',
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
                'image' => 'images/campus/estudiantes-cancha.jpg',
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
                'image' => 'images/campus/estudiantes-fachada.jpg',
            ],
            [
                'slug' => 'evento-oficial-fesc',
                'title' => 'FESC anuncia nuevo evento académico',
                'summary' => 'Conoce los detalles del evento en el sitio oficial de la institución.',
                'body' => 'La institución invita a la comunidad a participar del próximo evento académico. En esta plataforma encuentras el resumen; los detalles oficiales están en el portal FESC.',
                'type' => 'externo',
                'status' => 'publicado',
                'published_at' => '2026-03-08',
                'external_url' => 'https://www.fesc.edu.co/portal/',
                'image' => 'images/campus/edificio-avenida-5.jpg',
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
     * Puntos NFC validados del campus. Un punto, una noticia activa.
     *
     * @return list<array<string, mixed>>
     */
    public static function nfcPoints(): array
    {
        return [
            [
                'code' => 'bloque-a',
                'identifier' => 'NFC-001',
                'name' => 'Bloque A',
                'location' => 'Bloque A',
                'description' => 'Información institucional asociada al Bloque A.',
                'status' => 'activo',
                'kind' => 'bloque',
                'image' => 'images/campus/edificio-01.jpg',
                'content_slugs' => ['comunicado-calendario'],
            ],
            [
                'code' => 'bloque-b',
                'identifier' => 'NFC-002',
                'name' => 'Bloque B',
                'location' => 'Bloque B',
                'description' => 'Información institucional asociada al Bloque B.',
                'status' => 'activo',
                'kind' => 'bloque',
                'image' => 'images/campus/edificio-03.jpg',
                'content_slugs' => ['comunicado-calendario'],
            ],
            [
                'code' => 'bloque-c',
                'identifier' => 'NFC-003',
                'name' => 'Bloque C',
                'location' => 'Bloque C',
                'description' => 'Información institucional asociada al Bloque C.',
                'status' => 'activo',
                'kind' => 'bloque',
                'image' => 'images/campus/edificio-05.jpg',
                'content_slugs' => ['evento-oficial-fesc'],
            ],
            [
                'code' => 'biblioteca',
                'identifier' => 'NFC-004',
                'name' => 'Biblioteca Moisés San Juan López',
                'location' => 'Biblioteca Moisés San Juan López',
                'description' => 'Servicios, noticias y eventos relacionados con la biblioteca.',
                'status' => 'activo',
                'kind' => 'espacio',
                'image' => 'images/campus/fuente.jpg',
                'content_slugs' => ['servicios-biblioteca'],
            ],
            [
                'code' => 'auditorio-avenida-5',
                'identifier' => 'NFC-005',
                'name' => 'Auditorio Avenida 5',
                'location' => 'Auditorio Avenida 5',
                'description' => 'Información de actividades y eventos del auditorio.',
                'status' => 'activo',
                'kind' => 'espacio',
                'image' => 'images/campus/edificio-06.jpg',
                'content_slugs' => ['evento-lectura-cultura'],
            ],
            [
                'code' => 'entrada-avenida-4',
                'identifier' => 'NFC-006',
                'name' => 'Entrada Avenida 4',
                'location' => 'Acceso Avenida 4',
                'description' => 'Bienvenida e información general al ingresar por Avenida 4.',
                'status' => 'activo',
                'kind' => 'acceso',
                'image' => 'images/campus/fachada.jpg',
                'content_slugs' => ['comunicado-calendario'],
            ],
            [
                'code' => 'entrada-avenida-5',
                'identifier' => 'NFC-007',
                'name' => 'Entrada Avenida 5',
                'location' => 'Acceso Avenida 5',
                'description' => 'Orientación e información institucional al ingresar por Avenida 5.',
                'status' => 'activo',
                'kind' => 'acceso',
                'image' => 'images/campus/edificio-avenida-5.jpg',
                'content_slugs' => ['evento-oficial-fesc'],
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
                'icon' => 'atom',
                'title' => 'Encuentra un punto NFC',
                'description' => 'Están en entradas, bloques, biblioteca y auditorio.',
            ],
            [
                'icon' => 'blender-phone',
                'title' => 'Acerca tu teléfono',
                'description' => 'Sin apps extra: el chip abre una URL del punto.',
            ],
            [
                'icon' => 'book',
                'title' => 'Descubre el contenido',
                'description' => 'Noticias, eventos y mensajes de ese lugar.',
            ],
            [
                'icon' => 'arrow-up-right-from-square',
                'title' => 'Explora más información',
                'description' => 'Sigue navegando o visita el sitio oficial FESC.',
            ],
        ];
    }
}
