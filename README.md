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

Configurar la base de datos y las credenciales iniciales (`ADMIN_*`) en `.env` y luego:

```bash
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

## Despliegue en Vercel

La aplicación corre en un contenedor FrankenPHP. Hace falta una base de datos MySQL/MariaDB externa (SQLite no persiste en Vercel).

### Deploy automático

Cada push a `main` dispara el workflow [Deploy Production](.github/workflows/deploy-production.yml) hacia el proyecto Vercel `nexo-nfc`.

Configura estos **secrets** en el repositorio de GitHub (`Settings → Secrets and variables → Actions`):

| Secret | Valor |
| --- | --- |
| `VERCEL_TOKEN` | Token de [Vercel → Account Settings → Tokens](https://vercel.com/account/tokens) |
| `VERCEL_ORG_ID` | `team_ED4JT637mALZVOb0g0fDFaMf` |
| `VERCEL_PROJECT_ID` | `prj_OV2IzObgwiNVyV7q9widk5bsBcp7` |

Opcional (recomendado): instalar la [app de Vercel en GitHub](https://github.com/apps/vercel) sobre la organización `NexoNFC` y vincular el repo al proyecto; así Vercel también puede crear previews por PR.

### Variables de entorno en Vercel

1. En el proyecto Vercel, definir: `APP_KEY`, `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`, credenciales `DB_*` (MySQL/MariaDB) y, para los administradores iniciales, `ADMIN_EMAIL`, `ADMIN_PASSWORD`, `ADMIN_NAME` (y opcionalmente `ADMIN_ERICK_*` / `ADMIN_SANTIAGO_*`).
2. Al arrancar el contenedor se ejecutan `php artisan migrate --force` y, de forma explícita, los seeders de roles y usuarios (`RolesAndPermissionsSeeder`, `AdminUserSeeder`). Son idempotentes: crean cuentas faltantes y no reinician contraseñas de usuarios ya existentes. Después se siembran fuentes, categorías y catálogo demo.

Archivos de despliegue: `vercel.json`, `Dockerfile.vercel`, `Caddyfile`, `docker/entrypoint.sh`.

Dominio actual del proyecto: [nexo-nfc-phi.vercel.app](https://nexo-nfc-phi.vercel.app).

## Acceso administrativo

- Login: `/admin/login` (`/login` redirige ahí)
- Panel: `/admin` (requiere autenticación y rol `admin`)
- Autorización con Spatie Permission (un solo rol: administrador)
- El público consulta el sitio sin iniciar sesión

Usuarios de prueba (después de `migrate --seed`; contraseñas definidas en `.env`, por defecto `password`):

| Nombre | Correo por defecto |
| --- | --- |
| Administrador FESC | `admin@fesc.edu.co` |
| Erick | `est_es.perez@fesc.edu.co` |
| Santiago | `est_s_rueda@fesc.edu.co` |

## Documentación

- [Modelo de negocio](docs/modelo-de-negocio.md): propósito, NFC, actores, entidades y reglas de dominio.
- [Convenciones de desarrollo](docs/convenciones-de-desarrollo.md): stack, arquitectura, Git, frontend y calidad.
- [Contextualizador institucional y físico](docs/contexto-institucional-y-fisico.md): identidad oficial FESC y sede Cúcuta / NFC.
- [Sistema de diseño](docs/sistema-de-diseno.md): tokens, tipografía y componentes.
- [Referencias de diseño](docs/referencias-diseno.md): análisis del portal FESC y patrones de producto.

## Rutas principales

| Ruta | Descripción |
|------|-------------|
| `/` | Home pública |
| `/contenidos/{slug}` | Detalle de contenido |
| `/nfc/{code}` | Experiencia por punto NFC |
| `/login` | Acceso administrativo |
| `/admin` | Dashboard (protegido) |
| `/admin/news` | Noticias (según permisos) |
| `/admin/categories` | Categorías (según permisos) |
| `/admin/nfc` | Puntos NFC (según permisos) |
| `/admin/statistics` | Estadísticas (según permisos) |
| `/admin/users` | Usuarios (solo admin) |

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

Autenticación, autorización y dominio de catálogo/NFC persistente. El público solo ve noticias publicadas. Desde `/admin/news` el administrador trae piezas del portal FESC (carrusel «Proyectamos Nuestra Institución», News Bienestar, Comunicados, Novedades SIG y News Extension), las revisa en original o transcritas con IA y decide si las publica. Instagram y el scheduler todavía no forman parte de este flujo.

## Comandos importantes

```bash
php artisan serve
php artisan migrate
php artisan catalog:ingest --source=fesc
php artisan test
npm run dev
npm run build
```

`catalog:ingest` consulta el portal FESC (portada y listados institucionales), importa piezas recientes de forma idempotente y crea noticias en estado `borrador`. No publica automáticamente. En administración, `/admin/news` ofrece la misma acción. En pruebas no se llama al portal: se usan fixtures.
