# Stage 1: Build dependencies dengan Composer
FROM composer:2 as vendor
WORKDIR /app
COPY database/ database/
COPY composer.json composer.json
COPY composer.lock composer.lock
RUN composer install \
  --ignore-platform-reqs \
  --no-interaction \
  --no-plugins \
  --no-scripts \
  --prefer-dist

# Stage 2: Build aset frontend dengan Node.js (jika ada)
FROM node:18-alpine as frontend
WORKDIR /app
COPY package.json package.json
COPY package-lock.json package-lock.json
COPY vite.config.js vite.config.js
# COPY tailwind.config.js tailwind.config.js
# COPY postcss.config.js postcss.config.js
COPY resources/ resources/
RUN npm install && npm run build

# Stage 3: Final production image
FROM php:8.2-fpm-alpine

# Instal ekstensi PHP yang umum digunakan Laravel
RUN apk add --no-cache \
  libpng-dev \
  jpeg-dev \
  freetype-dev \
  libzip-dev \
  oniguruma-dev \
  libxml2-dev \
  zip \
  unzip \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install \
  gd \
  pdo_mysql \
  mbstring \
  exif \
  pcntl \
  bcmath \
  zip \
  opcache

WORKDIR /var/www/html

# Copy file-file yang sudah di-build dari stage sebelumnya
COPY --from=vendor /app/vendor/ /var/www/html/vendor/
COPY --from=frontend /app/public/build/ /var/www/html/public/build/

# Copy sisa kode aplikasi
COPY . .

# Atur kepemilikan file agar bisa ditulis oleh web server
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port untuk PHP-FPM
EXPOSE 9000

# Command untuk menjalankan PHP-FPM
CMD ["php-fpm"]
