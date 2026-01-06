FROM php:8.4-fpm

LABEL "language"="php"
LABEL "framework"="laravel"

WORKDIR /var/www

# 1. Install Dependencies System
# Kita tambahkan libpq-dev agar client postgresql berjalan lancar
RUN apt-get update && apt-get install -y \
    curl gettext git grep libicu-dev nginx pkg-config unzip postgresql-client libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Install Node.js (Versi 22.x)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && apt-get install -y nodejs && rm -rf /var/lib/apt/lists/*

# 3. Install PHP Extensions
# PENTING: Ada 'pdo_pgsql' untuk PostgreSQL dan 'opcache' untuk performa production
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions @composer bcmath gd intl pdo_pgsql zip opcache

# 4. Copy Application Files
COPY --chown=www-data:www-data . /var/www

# 5. Setup Permissions
# Buat folder storage dan bootstrap cache, lalu beri permission write
RUN mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/framework/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 6. Build Assets & Install Vendor
# Jalankan composer install dan npm build
RUN composer install --no-dev --optimize-autoloader \
    && npm install \
    && npm run build

# 7. Setup Nginx Configuration
# - client_max_body_size: dinaikkan jadi 20M biar bisa upload foto agak besar
# - cache headers: ditambahkan untuk file static (js/css/img)
RUN cat <<'EOF' > /etc/nginx/sites-enabled/default
server {
    listen 8080;
    root /var/www/public;
    index index.php;
    server_tokens off;
    client_max_body_size 20M;

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    # Cache assets browser biar loading lebih ngebut
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires max;
        log_not_found off;
    }
}
EOF

# 8. Setup Entrypoint Script
# Script ini akan dijalankan setiap kali container start
RUN cat <<'EOF' > /entrypoint.sh
#!/bin/bash
set -e

echo "🚀 Starting deployment tasks..."

# A. Fix Storage Link (Wajib buat Filament/Uploads)
# Menghapus link lama jika ada, lalu buat baru
rm -f /var/www/public/storage
php artisan storage:link

# B. Cache & Optimization (Wajib buat Production)
echo "🔥 Optimizing Laravel..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# C. Optimize Filament (Supaya Admin Panel cepat)
echo "⚡ Optimizing Filament..."
php artisan filament:optimize

# D. Database Migration
# Pastikan DB PostgreSQL sudah ready sebelum migrate
echo "Migrating database..."
php artisan migrate --force

# E. Start Services
echo "✅ Starting Nginx & PHP-FPM..."
php-fpm -D
nginx -g "daemon off;"
EOF

RUN chmod +x /entrypoint.sh

EXPOSE 8080

CMD ["/entrypoint.sh"]