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

# Create nginx config inline
RUN mkdir -p /run/nginx && \
    cat > /etc/nginx/nginx.conf <<'EOF'
worker_processes auto;
error_log /dev/stdout warn;
pid /var/run/nginx.pid;

events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    access_log /dev/stdout;
    sendfile on;
    keepalive_timeout 65;
    gzip on;

    server {
        listen 8080;
        server_name _;
        root /var/www/html;
        index index.html index.php;

        location / {
            try_files $uri $uri/ /index.html;
        }

        location /frontend/ {
            alias /var/www/html/frontend/;
            try_files $uri $uri/ =404;
        }

        location /backend/ {
            try_files $uri $uri/ /backend/index.php?$query_string;
        }

        location ~ \.php$ {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }

        location ~ /\. {
            deny all;
        }
    }
}
EOF

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Create start script inline
RUN cat > /start.sh <<'EOF'
#!/bin/sh
php-fpm -D
nginx -g "daemon off;"
EOF

RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]