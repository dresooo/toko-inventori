# Build stage untuk frontend
FROM node:18-alpine AS build-frontend
WORKDIR /app/frontend
COPY frontend/package*.json ./
RUN npm ci
COPY frontend/ .
RUN npm run build

# PHP Runtime stage
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Copy built frontend files
COPY --from=build-frontend /app/frontend/dist ./frontend/dist

# Copy backend files
COPY backend/ ./backend/
COPY index.html ./

# Install dependencies dan PHP extensions
RUN apk add --no-cache \
    bash \
    mysql-client \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql \
    && rm -rf /tmp/* /var/cache/apk/*

# Install nginx
RUN apk add --no-cache nginx

# Copy nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Create nginx runtime directories
RUN mkdir -p /run/nginx

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Create start script inline
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'php-fpm -D' >> /start.sh && \
    echo 'nginx -g "daemon off;"' >> /start.sh && \
    chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]