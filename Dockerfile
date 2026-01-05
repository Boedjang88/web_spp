# Gunakan PHP 8.4
FROM php:8.4-fpm

# Install library sistem (Termasuk libzip-dev yang WAJIB buat Excel)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    nodejs \
    npm

# Bersihkan cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Ekstensi PHP (Sekarang ada ZIP dan INTL buat Excel)
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Ambil Composer terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set folder kerja
WORKDIR /var/www/html

# Copy semua file
COPY . .

# Install Dependencies PHP
RUN composer install --no-dev --optimize-autoloader

# Install Dependencies JS & Build
RUN npm install && npm run build

# Permission storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose Port
EXPOSE 8080

# Jalankan
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080