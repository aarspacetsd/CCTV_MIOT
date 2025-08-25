# app/Dockerfile
# Gunakan PHP 8.2 FPM berbasis Alpine
FROM php:8.2-fpm-alpine

# Direktori kerja
WORKDIR /var/www/html

# ====== Sistem deps (runtime + build) ======
# - .build-deps: hanya untuk kompilasi ekstensi, nanti dihapus
# - runtime libs: tetap dipakai saat container jalan
RUN apk update && \
  apk add --no-cache \
  git curl unzip zip \
  libzip libpng libjpeg-turbo freetype libxml2 \
  && apk add --no-cache --virtual .build-deps \
  $PHPIZE_DEPS \
  build-base \
  libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev libxml2-dev oniguruma-dev

# ====== Ekstensi PHP ======
# GD perlu dikonfigurasi agar dukung freetype & jpeg
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
  docker-php-ext-install -j"$(nproc)" \
  pdo pdo_mysql \
  mbstring \
  exif \
  pcntl \
  bcmath \
  gd \
  zip \
  dom \
  xml \
  opcache

# Bersihkan build deps agar image kecil
RUN apk del .build-deps && rm -rf /var/cache/apk/*

# ====== Composer ======
# Lebih ringan: pakai binary dari image composer resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_NO_INTERACTION=1

# Salin file composer terlebih dulu (untuk caching layer)
COPY composer.json composer.lock ./

# Install dependensi tanpa dev & tanpa scripts (hindari artisan jalan saat build)
RUN composer install \
  --no-dev --prefer-dist --optimize-autoloader --no-scripts \
  && rm -rf /root/.composer/cache

# Salin seluruh kode aplikasi
COPY . .

# (Opsional) jalankan composer dump-autoload setelah source tersalin
RUN composer dump-autoload --optimize

# Set permission folder penting Laravel
RUN chown -R www-data:www-data storage bootstrap/cache && \
  chmod -R 775 storage bootstrap/cache

# Jangan generate APP_KEY saat build (lakukan saat runtime atau melalui entrypoint)
# APP_KEY sebaiknya diinject lewat environment (APP_KEY=base64:...)

# Expose port PHP-FPM
EXPOSE 9000

# Jalankan PHP-FPM
CMD ["php-fpm"]
