# ==========================
# Stage 1: Build Frontend (Node 20)
# ==========================
FROM node:20 AS build-frontend

# Set working directory
WORKDIR /app

# Copy package.json & package-lock.json
COPY package*.json ./

# Install Node dependencies
RUN npm install

# Copy all project files
COPY . .

# Build assets (Tailwind + Vite)
RUN npm run build

# ==========================
# Stage 2: Setup PHP + Laravel
# ==========================
FROM php:8.2-fpm

# Set working directory
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

# Copy built frontend assets from previous stage
COPY --from=build-frontend /app/public/build /var/www/html/public/build

# Copy the rest of the Laravel app
COPY --from=build-frontend /app /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Fix permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose Laravel port
EXPOSE 8000

# Start Laravel server
CMD php artisan serve --host=0.0.0.0 --port=8000
