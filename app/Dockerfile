# app/Dockerfile
# Gunakan base image resmi PHP 8.1 FPM Alpine
FROM php:8.1-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install dependensi sistem yang dibutuhkan oleh PHP extensions
# Menambahkan beberapa library yang umum dibutuhkan seperti libxml2 dan oniguruma
RUN apk update && apk add --no-cache \
  build-base \
  git \
  curl \
  unzip \
  zip \
  libzip-dev \
  libpng-dev \
  libjpeg-turbo-dev \
  freetype-dev \
  libxml2-dev \
  oniguruma-dev

# Konfigurasi dan install ekstensi PHP
# Menggunakan -j$(nproc) untuk mempercepat proses kompilasi
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
  pdo pdo_mysql \
  mbstring \
  exif \
  pcntl \
  bcmath \
  gd \
  zip

# Install Composer (dependency manager untuk PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Hapus cache apk
RUN rm -rf /var/cache/apk/*

# Salin file composer.json dan composer.lock
COPY composer.json composer.lock ./

# Install dependensi vendor tanpa script dev
RUN composer install --no-interaction --no-plugins --no-dev --prefer-dist --no-autoloader --no-scripts

# Salin seluruh kode aplikasi
COPY . .

# Generate autoloader yang dioptimalkan
RUN composer dump-autoload --optimize

# Generate application key Laravel
RUN php artisan key:generate

# Atur kepemilikan file ke user www-data
RUN chown -R www-data:www-data \
  /var/www/html/storage \
  /var/www/html/bootstrap/cache

# Expose port 9000 untuk PHP-FPM
EXPOSE 9000

# Perintah default untuk menjalankan container
CMD ["php-fpm"]
