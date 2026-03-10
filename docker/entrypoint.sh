#!/bin/sh

# Set permissions for the mounted volume
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear existing caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
rm -rf bootstrap/cache/*.php

# Run migrations
php artisan migrate --force

# Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start supervisor
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf