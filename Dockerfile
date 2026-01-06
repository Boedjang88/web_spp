FROM php:8.4-fpm

LABEL "language"="php"

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libonig-dev \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

COPY . .

RUN chown -R www-data:www-data /var/www

EXPOSE 8080

CMD ["php-fpm"]