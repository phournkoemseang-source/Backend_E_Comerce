# Stage 1: Build dependencies
FROM php:8.2-fpm-alpine AS builder

# Install build dependencies
RUN apk add --no-cache \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    curl \
    git \
    unzip \
    oniguruma-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    bcmath \
    gd \
    zip \
    mbstring \
    exif

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install production dependencies (no scripts to avoid needing .env)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Stage 2: Final Production Image
FROM php:8.2-fpm-alpine

# Install runtime dependencies only
RUN apk add --no-cache \
    libpng \
    libjpeg-turbo \
    freetype \
    zip \
    curl

# Copy PHP extensions from builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Copy application from builder
COPY --from=builder /app /var/www/html

WORKDIR /var/www/html

# Set up Laravel storage directories with proper permissions
RUN mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && chmod -R 775 storage \
    && chmod -R 775 bootstrap/cache

# Expose port (Railway provides its own HTTP proxy)
EXPOSE 8080

# Start PHP-FPM
CMD ["php-fpm"]
