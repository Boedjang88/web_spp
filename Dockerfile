FROM php:8.4-fpm

LABEL "language"="php"

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    nginx \
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

# NGINX configuration
RUN mkdir -p /etc/nginx/sites-enabled && cat <<'EOF' > /etc/nginx/sites-enabled/default
server {
    listen 8080;
    root /var/www;
    index index.php index.html;

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

    error_log /dev/stderr;
    access_log /dev/stdout;
}
EOF

COPY . .

RUN chown -R www-data:www-data /var/www

EXPOSE 8080

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]