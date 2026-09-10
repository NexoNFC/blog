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

# Seeders idempotentes: crean faltantes / sincronizan roles, sin wipe.
php artisan db:seed --force --no-interaction

exec frankenphp run --config /etc/frankenphp/Caddyfile
