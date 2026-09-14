#!/bin/sh
set -eu

cd /app

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    db_file="${DB_DATABASE:-/app/database/database.sqlite}"
    mkdir -p "$(dirname "$db_file")"
    if [ ! -f "$db_file" ]; then
        touch "$db_file"
    fi
fi

# Solo migraciones pendientes (no borra tablas ni datos existentes).
php artisan migrate --force --no-interaction

# Roles y usuarios administrativos: siempre al desplegar (idempotente).
# No reinicia contraseñas de cuentas ya existentes.
php artisan db:seed --class=Database\\Seeders\\RolesAndPermissionsSeeder --force --no-interaction
php artisan db:seed --class=Database\\Seeders\\AdminUserSeeder --force --no-interaction

# Fuentes, categorías y catálogo base (idempotente).
# Si falla, el panel sigue accesible con los usuarios ya sembrados.
if ! php artisan db:seed --class=Database\\Seeders\\SourceSeeder --force --no-interaction; then
    echo "Advertencia: no se pudo sembrar SourceSeeder." >&2
fi

if ! php artisan db:seed --class=Database\\Seeders\\CategorySeeder --force --no-interaction; then
    echo "Advertencia: no se pudo sembrar CategorySeeder." >&2
fi

if ! php artisan db:seed --class=Database\\Seeders\\CatalogSeeder --force --no-interaction; then
    echo "Advertencia: no se pudo sembrar CatalogSeeder." >&2
fi

exec frankenphp run --config /etc/frankenphp/Caddyfile
