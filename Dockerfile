# Gunakan PHP 8.4 sesuai permintaan (Option A)
FROM php:8.4-fpm

# Install library sistem yang dibutuhkan Laravel & Postgres
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nodejs \
    npm

# Bersihkan cache biar ringan
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Ekstensi PHP yang wajib (Postgres, GD, dll)
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Ambil Composer terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set folder kerja
WORKDIR /var/www/html

# Copy semua file project ke dalam server
COPY . .

# Install Dependencies PHP (Laravel)
RUN composer install --no-dev --optimize-autoloader

# Install Dependencies JS & Build Aset (Tailwind/Vite)
RUN npm install && npm run build

# Beri hak akses ke folder storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Buka Port 8080 (Standar Zeabur)
EXPOSE 8080

# Perintah Jalankan Server (Migrasi dulu baru jalan)
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080