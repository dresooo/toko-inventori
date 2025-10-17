# ==========================
# Stage 1: Build Frontend
# ==========================
FROM node:20 AS build-frontend

WORKDIR /app

# Copy package.json & package-lock.json
COPY package*.json ./

# Install Node dependencies
RUN npm install

# Copy semua file project
COPY . .

# Build assets Vite/Tailwind
RUN npm run build

# ==========================
# Stage 2: Setup PHP + Laravel
# ==========================
FROM php:8.2-fpm

WORKDIR /var/www/html

# Install dependencies sistem
RUN apt-get update && apt-get install -y \
    git unzip libonig-dev libxml2-dev libzip-dev zip curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath xml zip \
    && apt-get clean

# Copy hasil build frontend dari stage 1
COPY --from=build-frontend /app/public/build /var/www/html/public/build

# Copy seluruh project Laravel
COPY --from=build-frontend /app /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port Laravel
EXPOSE 8000

# Jalankan Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000
