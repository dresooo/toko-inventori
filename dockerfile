# ==========================
# Stage 1: Build Frontend
# ==========================
FROM node:20.19.0-alpine AS build-frontend

WORKDIR /app

# Copy package files dulu biar caching npm
COPY package*.json ./

# Install dependencies
RUN npm ci --legacy-peer-deps

# Copy source
COPY . .

# Build frontend
RUN npm run build

# ==========================
# Stage 2: Laravel + PHP
# ==========================
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    curl \
    bash \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath xml zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Laravel project
COPY . /var/www/html

# Copy built frontend assets
COPY --from=build-frontend /app/public/build /var/www/html/public/build

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Clear cache & config (Railway cache safe)
RUN php artisan config:clear || true \
    && php artisan cache:clear || true \
    && php artisan view:clear || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 8000

# Start Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
