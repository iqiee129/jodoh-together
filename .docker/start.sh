#!/bin/bash
set -e

php artisan optimize:clear || true
php artisan storage:link || true

if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan migrate --force
fi

php artisan config:cache || true
php artisan view:cache || true

apache2-foreground