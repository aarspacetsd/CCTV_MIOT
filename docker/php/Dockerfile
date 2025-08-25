FROM php:8.2-fpm

# System deps
RUN apt-get update && apt-get install -y \
  git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libicu-dev \
  libxml2-dev libssl-dev libpq-dev zlib1g-dev libcurl4-openssl-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
  && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP ini - Path lengkap dari root context
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Workdir
WORKDIR /var/www/html

# Salin semua file dari root repositori ke dalam image
COPY . .

# Entrypoint - Salin ke /usr/local/bin agar ada di PATH
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Jalankan script entrypoint kustom kita. Ini akan berjalan sebagai root.
ENTRYPOINT ["entrypoint.sh"]

# CMD asli dari base image (php-fpm) akan dijalankan oleh entrypoint.sh
CMD ["php-fpm"]
