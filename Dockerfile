# Gunakan PHP 8.4
FROM php:8.4-fpm

# Install library system (WAJIB ADA libzip-dev buat Excel)
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

# Install Ekstensi PHP (WAJIB ADA zip dan intl)
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Ambil Composer terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set folder kerja
WORKDIR /var/www/html

# Copy project
COPY . .

# Install Dependencies dengan ignore platform (biar gak rewel soal versi PHP lokal)
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Build Aset
RUN npm install && npm run build

# Permission
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose & Start
EXPOSE 8080
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080