#!/usr/bin/env bash
set -e

php artisan config:clear
php artisan cache:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

apache2-foreground
