FROM php:8.4-fpm

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip curl git libpng-dev libgmp-dev libzip-dev \
    && docker-php-ext-install pdo_mysql gd bcmath gmp zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/admin-backend

# 2. Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Copy ALL application files first
# This ensures composer.lock and app/Helpers exist before install
COPY app/ .

# 4. Install dependencies in the correct directory
# The composer.json file is at the root level after copying
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --ignore-platform-reqs

# 5. Setup Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# 6. Permissions for Laravel
RUN chown -R www-data:www-data /var/www/admin-backend/storage /var/www/admin-backend/bootstrap/cache

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]