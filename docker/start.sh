#!/bin/bash
php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan storage:link
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
php-fpm -D
nginx -g "daemon off;"
