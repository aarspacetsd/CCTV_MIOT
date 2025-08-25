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

# PHP ini - PERBAIKAN: Path lengkap dari root context
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Workdir
WORKDIR /var/www/html

# Salin semua file dari root repositori ke dalam image
COPY . .

# Entrypoint - PERBAIKAN: Path lengkap dari root context
COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Atur kepemilikan file setelah menyalin kode
RUN chown -R www-data:www-data /var/www/html

USER www-data

EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
