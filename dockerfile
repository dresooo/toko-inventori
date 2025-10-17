# ==========================
# Stage 1: Build Frontend
# ==========================
FROM node:20 AS build-frontend

WORKDIR /app

# Copy package.json & package-lock.json
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy seluruh frontend
COPY . .

# Build assets Vite/Tailwind
RUN npm run build

# ==========================
# Stage 2: PHP + Laravel
# ==========================
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip \
    curl \
    npm \
    && docker-php-ext-install zip pdo pdo_mysql

# Copy composer.json & composer.lock
COPY composer.json composer.lock ./

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy Laravel app
COPY . .

# Copy built frontend assets
COPY --from=build-frontend /app/dist ./public/dist

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 for php-fpm
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
