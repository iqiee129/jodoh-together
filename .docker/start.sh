#!/bin/bash
set -e

APP_PORT=${PORT:-10000}

cat > /etc/apache2/ports.conf <<EOF
Listen ${APP_PORT}
EOF

cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:${APP_PORT}>
    ServerName localhost
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

php artisan optimize:clear || true
php artisan storage:link || true

if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan migrate --force
fi

php artisan config:cache || true
php artisan view:cache || true

apache2-foreground