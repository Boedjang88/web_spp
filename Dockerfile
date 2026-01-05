
FROM php:8.4-fpm

LABEL "language"="php"
LABEL "framework"="laravel"

# Install system dependencies
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
    npm \
    nginx

# Clean cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files
COPY . .

# Install composer dependencies with ignore platform requirements
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-interaction

# Install npm dependencies and build
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Create .env if not exists
RUN cp .env.example .env || true

# Generate app key
RUN php artisan key:generate --force || true

EXPOSE 8080

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8080