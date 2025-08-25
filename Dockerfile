# Dockerfile
# File ini berisi instruksi untuk membangun image Docker untuk aplikasi Laravel Anda.

# Gunakan base image PHP 8.2 dengan FPM
FROM php:8.2-fpm

# --- PERBAIKAN UTAMA: MENJALANKAN DALAM MODE NON-INTERAKTIF ---
# Ini adalah kunci untuk mencegah error 'exit code 100' di lingkungan build otomatis
ENV DEBIAN_FRONTEND=noninteractive

# Set working directory
WORKDIR /var/www/html

# Membuat proses instalasi lebih kuat dan non-interaktif
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
  build-essential \
  libpng-dev \
  libjpeg-dev \
  libfreetype6-dev \
  locales \
  zip \
  vim \
  unzip \
  git \
  curl \
  libzip-dev \
  libonig-dev \
  graphviz \
  libicu-dev \
  libxml2-dev \
  netcat \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

# Konfigurasi dan generate locale untuk menghindari warning
RUN sed -i '/en_US.UTF-8/s/^# //g' /etc/locale.gen && locale-gen
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

# Instalasi ekstensi PHP yang umum digunakan
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Instal Composer (dependency manager untuk PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin hanya file composer terlebih dahulu untuk memanfaatkan cache Docker
COPY composer.json composer.lock ./

# Jalankan composer install untuk mengunduh vendor dependencies
RUN composer install --no-interaction --no-scripts --no-autoloader --prefer-dist

# Salin sisa file aplikasi ke dalam container
COPY . .

# Generate autoloader yang dioptimalkan
RUN composer dump-autoload --optimize

# Salin skrip start-up dan buat agar bisa dieksekusi
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Setel izin yang benar untuk direktori storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 untuk PHP-FPM
EXPOSE 9000

# Gunakan skrip entrypoint untuk menjalankan container
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
