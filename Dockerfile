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

# Install dependencies dan PHP extensions
RUN apk add --no-cache \
    bash \
    nginx \
    mysql-client \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql \
    && rm -rf /tmp/* /var/cache/apk/*

# Copy files
COPY --from=build-frontend /app/frontend/dist ./frontend/dist
COPY backend/ ./backend/
COPY index.html ./

# Create nginx config
RUN mkdir -p /run/nginx /var/log/nginx && \
    echo 'worker_processes 1;' > /etc/nginx/nginx.conf && \
    echo 'error_log /dev/stdout warn;' >> /etc/nginx/nginx.conf && \
    echo 'pid /var/run/nginx.pid;' >> /etc/nginx/nginx.conf && \
    echo 'events { worker_connections 1024; }' >> /etc/nginx/nginx.conf && \
    echo 'http {' >> /etc/nginx/nginx.conf && \
    echo '  include /etc/nginx/mime.types;' >> /etc/nginx/nginx.conf && \
    echo '  default_type application/octet-stream;' >> /etc/nginx/nginx.conf && \
    echo '  access_log /dev/stdout;' >> /etc/nginx/nginx.conf && \
    echo '  sendfile on;' >> /etc/nginx/nginx.conf && \
    echo '  keepalive_timeout 65;' >> /etc/nginx/nginx.conf && \
    echo '  server {' >> /etc/nginx/nginx.conf && \
    echo '    listen 8080;' >> /etc/nginx/nginx.conf && \
    echo '    root /var/www/html;' >> /etc/nginx/nginx.conf && \
    echo '    index index.html index.php;' >> /etc/nginx/nginx.conf && \
    echo '    location / {' >> /etc/nginx/nginx.conf && \
    echo '      try_files $uri $uri/ /index.html;' >> /etc/nginx/nginx.conf && \
    echo '    }' >> /etc/nginx/nginx.conf && \
    echo '    location /backend/ {' >> /etc/nginx/nginx.conf && \
    echo '      try_files $uri $uri/ /backend/index.php?$query_string;' >> /etc/nginx/nginx.conf && \
    echo '    }' >> /etc/nginx/nginx.conf && \
    echo '    location ~ \.php$ {' >> /etc/nginx/nginx.conf && \
    echo '      fastcgi_pass 127.0.0.1:9000;' >> /etc/nginx/nginx.conf && \
    echo '      fastcgi_index index.php;' >> /etc/nginx/nginx.conf && \
    echo '      fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;' >> /etc/nginx/nginx.conf && \
    echo '      include fastcgi_params;' >> /etc/nginx/nginx.conf && \
    echo '    }' >> /etc/nginx/nginx.conf && \
    echo '  }' >> /etc/nginx/nginx.conf && \
    echo '}' >> /etc/nginx/nginx.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

# Start both services
CMD php-fpm -D && nginx -g "daemon off;"