# ==========================
# Stage 1: Build Frontend
# ==========================
FROM node:20 AS build-frontend

WORKDIR /app

# Copy package files and install dependencies
COPY package*.json ./
RUN npm install

# Copy project files
COPY . .

# Build frontend assets
RUN npm run build

# ==========================
# Stage 2: Setup PHP + Laravel
# ==========================
FROM php:8.2-fpm

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath xml zip \
    && apt-get clean

# Copy Laravel app from frontend build stage
COPY --from=build-frontend /app /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose Railway port
EXPOSE 8000

# Start Laravel using environment port
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
