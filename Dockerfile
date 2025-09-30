# --- STAGE 1: Base PHP ---
# Menggunakan base image resmi PHP 8.2 dengan FPM dan Alpine Linux (ringan)
FROM php:8.2-fpm-alpine AS base

# Menginstal library sistem yang umum dibutuhkan Laravel
RUN apk add --no-cache \
  curl \
  zip \
  unzip \
  libpng-dev \
  libjpeg-turbo-dev \
  freetype-dev \
  oniguruma-dev \
  libxml2-dev \
  supervisor

# Menginstal ekstensi PHP yang umum
RUN docker-php-ext-install \
  pdo_mysql \
  mbstring \
  exif \
  pcntl \
  bcmath \
  gd \
  xml

# Menginstal Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# --- STAGE 2: Build Dependensi Composer ---
FROM base AS vendor

COPY database/ database/
COPY composer.json composer.lock ./
# Menginstal dependensi produksi saja
RUN composer install --no-interaction --no-dev --optimize-autoloader

# --- STAGE 3: Build Aset Frontend ---
FROM node:18-alpine AS frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build

# --- STAGE 4: Final Image ---
FROM base AS final

WORKDIR /app

# Salin semua file aplikasi dan dependensi yang sudah di-build dari stage sebelumnya
COPY . .
COPY --from=vendor /app/vendor/ ./vendor/
COPY --from=frontend /app/public/build/ ./public/build/

# Atur kepemilikan file agar web server bisa menulis ke folder storage dan bootstrap/cache
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
RUN chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 8000

# Perintah `Start Command` di Coolify akan menangani ini,
# biasanya dengan menjalankan `php-fpm`
# CMD ["php-fpm"]
