# Plataforma informativa FESC

Producto del PPA (Proyecto Pedagógico de Aula) de Ingeniería de Software.

Plataforma web para la difusión y consulta de información de interés para la comunidad de la Fundación de Estudios Superiores Comfanorte (FESC), con acceso tradicional por sitio web y acceso físico mediante puntos NFC ubicados en el campus.

## Objetivo

Convertir puntos físicos de la institución en puertas digitales a información relevante, administrable desde la plataforma y medible en su uso, sin que el sistema dependa exclusivamente de los chips NFC.

## Tecnologías

- PHP / Laravel
- Blade
- HTML, CSS, JavaScript
- Tailwind CSS
- MySQL / MariaDB
- Git / GitHub

## Requisitos

- PHP 8.4 o superior
- Composer
- Node.js y npm
- MySQL o MariaDB

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configurar la base de datos en `.env` y luego:

```bash
php artisan migrate
npm install
npm run build
php artisan serve
```

## Documentación

- [Modelo de negocio](docs/modelo-de-negocio.md): propósito, NFC, actores, entidades y reglas de dominio.
- [Convenciones de desarrollo](docs/convenciones-de-desarrollo.md): stack, arquitectura, Git, frontend y calidad.
- [Contextualizador institucional y físico](docs/contexto-institucional-y-fisico.md): identidad oficial FESC y sede Cúcuta / NFC.
- [Sistema de diseño](docs/sistema-de-diseno.md): tokens, tipografía y componentes.
- [Referencias de diseño](docs/referencias-diseno.md): análisis del portal FESC y patrones de producto.

## Maquetación actual (demo)

Rutas de vista estática (sin persistencia ni autenticación):

| Ruta | Descripción |
|------|-------------|
| `/` | Home pública |
| `/contenidos/{slug}` | Detalle de contenido |
| `/nfc/{code}` | Experiencia por punto NFC |
| `/admin` | Dashboard (maqueta) |
| `/admin/contenidos` | Listado de contenidos |
| `/admin/contenidos/crear` | Formulario de noticia |
| `/admin/nfc` | Listado de puntos NFC |

## Estructura general

```
app/            # Lógica de aplicación (modelos, controladores, services, etc.)
database/       # Migraciones y seeders
resources/      # Vistas Blade, CSS y JavaScript
routes/         # Definición de rutas
tests/          # Pruebas automatizadas
docs/           # Documentación del proyecto
```

## Estado actual

Maquetación Blade inicial con datos de ejemplo. Persistencia, autenticación y CRUD real se incorporarán por tareas posteriores.

## Comandos importantes

```bash
php artisan serve
php artisan migrate
php artisan test
npm run dev
npm run build
```
