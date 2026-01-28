#!/bin/sh
set -e

# --- 1. Permissions Check ---
mkdir -p /var/www/backend/storage/framework/cache/data
mkdir -p /var/www/backend/storage/framework/sessions
mkdir -p /var/www/backend/storage/framework/views
mkdir -p /var/www/backend/storage/logs

# --- 2. THE FIX: Clear Local Cache ---
echo "Clearing backend bootstrap caches..."
rm -f /var/www/backend/bootstrap/cache/*.php

# --- 3. Locate and run ---
if [ -f "/var/www/backend/artisan" ]; then
    cd /var/www/backend
    echo "Found Backend application"
else
    echo "ERROR: Cannot locate Backend artisan file"
    exit 1
fi

# --- 4. Handle Passport Keys ---
if [ ! -f "./storage/oauth-private.key" ]; then
    echo "Generating Passport keys for Backend..."
    php artisan passport:keys --force
    chmod 600 ./storage/*.key
fi

# --- 5. Optimization ---
php artisan storage:link --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Backend is ready. Starting php-fpm..."
exec "$@"