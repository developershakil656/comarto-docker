#!/bin/sh
set -e

# --- 1. Permissions Check ---
mkdir -p /var/www/admin-backend/storage/framework/cache/data
mkdir -p /var/www/admin-backend/storage/framework/sessions
mkdir -p /var/www/admin-backend/storage/framework/views
mkdir -p /var/www/admin-backend/storage/logs

chown -R www-data:www-data /var/www/admin-backend/storage
chown -R www-data:www-data /var/www/admin-backend/bootstrap/cache

# --- 2. THE FIX: Clear Local Cache ---
echo "Clearing admin-backend bootstrap caches..."
rm -f /var/www/admin-backend/bootstrap/cache/*.php

# --- 3. Locate and run ---
if [ -f "/var/www/admin-backend/artisan" ]; then
    cd /var/www/admin-backend
    echo "Found Admin-Backend application"
else
    echo "ERROR: Cannot locate Admin-Backend artisan file"
    exit 1
fi

# --- 4. Handle Passport Keys ---
if [ ! -f "./storage/oauth-private.key" ]; then
    echo "Generating Passport keys for Admin-Backend..."
    php artisan passport:keys --force
    chmod 600 ./storage/*.key
fi

# --- 5. Optimization ---
php artisan storage:link --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# --- 6. Start Laravel Scheduler in the background ---
echo "Starting Laravel Scheduler..."
php artisan schedule:work > /dev/null 2>&1 &

echo "Admin-Backend is ready. Starting php-fpm..."
exec "$@"