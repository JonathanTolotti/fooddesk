FROM php:8.5-fpm

ARG UID=1000
ARG GID=1000

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    zip \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Configure GD
RUN docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd xml zip

# Redis
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Ajusta UID/GID do www-data
RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data

WORKDIR /var/www

COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

USER www-data

ENTRYPOINT ["/entrypoint.sh"]
