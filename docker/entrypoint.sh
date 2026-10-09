#!/bin/sh
set -eu

cd /app

# En Vercel cada instancia fría es un contenedor nuevo. Una APP_KEY temporal
# invalida CSRF/sesión entre requests y deja el login sin mensajes de error.
if [ -z "${APP_KEY:-}" ]; then
    if [ -n "${VERCEL:-}" ] || [ -n "${VERCEL_ENV:-}" ]; then
        echo "Error: define APP_KEY en las variables de entorno del proyecto Vercel." >&2
        exit 1
    fi

    echo "Advertencia: APP_KEY no definida; generando una temporal (solo local)." >&2
    APP_KEY="$(php -r 'echo "base64:" . base64_encode(random_bytes(32));')"
    export APP_KEY
fi

# URL pública en Vercel (dominio de producción o preview).
if [ -z "${APP_URL:-}" ]; then
    if [ -n "${VERCEL_PROJECT_PRODUCTION_URL:-}" ]; then
        export APP_URL="https://${VERCEL_PROJECT_PRODUCTION_URL}"
    elif [ -n "${VERCEL_URL:-}" ]; then
        export APP_URL="https://${VERCEL_URL}"
    fi
fi

# Cookie sessions: sobreviven entre instancias sin depender del SQLite efímero.
if [ -n "${VERCEL:-}" ] || [ -n "${VERCEL_ENV:-}" ]; then
    export SESSION_DRIVER="${SESSION_DRIVER:-cookie}"
    export SESSION_SECURE_COOKIE="${SESSION_SECURE_COOKIE:-true}"
    export SESSION_SAME_SITE="${SESSION_SAME_SITE:-lax}"
fi

# Neon pooler (PgBouncer) rompe DDL de migrate; preferir host directo si llegó el pooler.
if [ -n "${DB_HOST:-}" ] && printf '%s' "$DB_HOST" | grep -q -- '-pooler.'; then
    export DB_HOST="$(printf '%s' "$DB_HOST" | sed 's/-pooler\././')"
fi
if [ -n "${DB_URL:-}" ] && printf '%s' "$DB_URL" | grep -q -- '-pooler.'; then
    export DB_URL="$(printf '%s' "$DB_URL" | sed 's/-pooler\././')"
fi

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    db_file="${DB_DATABASE:-/app/database/database.sqlite}"
    mkdir -p "$(dirname "$db_file")"
    if [ ! -f "$db_file" ]; then
        touch "$db_file"
    fi
fi

# Solo migraciones pendientes (no borra tablas ni datos existentes).
php artisan migrate --force --no-interaction

# Seeders en cada arranque/deploy: solo crean lo que falta (firstOrCreate / omitir si ya existe).
# No pisan datos ya configurados (usuarios, puntos NFC, panoramas, categorías, etc.).
php artisan db:seed --class=Database\\Seeders\\RolesAndPermissionsSeeder --force --no-interaction
php artisan db:seed --class=Database\\Seeders\\AdminUserSeeder --force --no-interaction

# Si falla, el panel sigue accesible con los usuarios ya sembrados.
if ! php artisan db:seed --class=Database\\Seeders\\SourceSeeder --force --no-interaction; then
    echo "Advertencia: no se pudo sembrar SourceSeeder." >&2
fi

if ! php artisan db:seed --class=Database\\Seeders\\CategorySeeder --force --no-interaction; then
    echo "Advertencia: no se pudo sembrar CategorySeeder." >&2
fi

# CatalogSeeder crea puntos NFC nuevos; NfcTourSeeder solo asigna panorama si el punto aún no tiene.
if ! php artisan db:seed --class=Database\\Seeders\\CatalogSeeder --force --no-interaction; then
    echo "Advertencia: no se pudo sembrar CatalogSeeder / NfcTourSeeder." >&2
fi

exec frankenphp run --config /etc/frankenphp/Caddyfile
