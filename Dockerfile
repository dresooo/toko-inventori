# ====================================
# Stage 1: Build Frontend dengan Node
# ====================================
FROM node:20-alpine AS build-frontend
WORKDIR /app
# Copy package files
COPY package*.json ./
# Install dependencies
RUN npm ci --legacy-peer-deps
# Copy source code
COPY . .
# Build frontend assets
RUN npm run build

# ====================================
# Stage 2: PHP Runtime (Production)
# ====================================
FROM php:8.2-cli-alpine
WORKDIR /var/www/html

# Install dependencies minimal yang diperlukan
RUN apk add --no-cache \
    bash \
    mysql-client \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql \
    && rm -rf /tmp/* /var/cache/apk/*

# Install Composer dari image resmi
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies (production only)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --optimize-autoloader \
    --prefer-dist

# Copy aplikasi Laravel
COPY . .

# Copy built assets dari stage 1
COPY --from=build-frontend /app/public/build ./public/build

# Set permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 8000

# Health check (optional)
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s \
  CMD php artisan || exit 1

# Start command
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]