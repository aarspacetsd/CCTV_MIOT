# app/Dockerfile
# PENTING: Versi PHP diubah dari 8.1 ke 8.2 agar sesuai dengan composer.json
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install dependensi sistem yang dibutuhkan oleh PHP extensions
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
# MENAMBAHKAN ekstensi umum yang dibutuhkan Laravel: ctype, fileinfo, dom, xml, tokenizer
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
  pdo pdo_mysql \
  mbstring \
  exif \
  pcntl \
  bcmath \
  gd \
  zip \
  ctype \
  fileinfo \
  dom \
  xml \
  tokenizer

# Install Composer (dependency manager untuk PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Hapus cache apk
RUN rm -rf /var/cache/apk/*

# Salin file composer.json dan composer.lock
COPY composer.json composer.lock ./

# Install dependensi vendor untuk produksi dan optimalkan autoloader
# Menggunakan --optimize-autoloader lebih efisien
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader

# Salin seluruh kode aplikasi (termasuk file .env Anda)
COPY . .

# PERINGATAN KEAMANAN: File .env Anda yang berisi kredensial sensitif
# ikut disalin ke dalam image Docker. Ini tidak disarankan untuk produksi.
# Praktik terbaik adalah menggunakan .env.example dan mengisi variabel saat runtime.

# Generate application key Laravel (akan menulis ke file .env yang sudah disalin)
RUN php artisan key:generate

# Atur kepemilikan file ke user www-data
RUN chown -R www-data:www-data \
  /var/www/html/storage \
  /var/www/html/bootstrap/cache

# Expose port 9000 untuk PHP-FPM
EXPOSE 9000

# Perintah default untuk menjalankan container
CMD ["php-fpm"]
