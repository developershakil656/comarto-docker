FROM php:8.4-fpm

# 1. Install system dependencies (WITH image libs)
RUN apt-get update && apt-get install -y \
    zip unzip curl git \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libgmp-dev \
    libzip-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install \
        pdo_mysql \
        gd \
        bcmath \
        gmp \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/backend

# 2. Get Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Copy application files
COPY app/ .

# 4. Install PHP dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --ignore-platform-reqs

# 5. Setup Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# 6. Permissions
RUN chown -R www-data:www-data \
    /var/www/backend/storage \
    /var/www/backend/bootstrap/cache

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
