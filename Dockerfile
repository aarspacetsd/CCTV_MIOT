# Dockerfile
# File ini berisi instruksi untuk membangun image Docker untuk aplikasi Laravel Anda.

# Gunakan base image PHP 8.2 dengan FPM
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Instalasi dependensi yang dibutuhkan oleh Laravel
RUN apt-get update && apt-get install -y \
  build-essential \
  libpng-dev \
  libjpeg62-turbo-dev \
  libfreetype6-dev \
  locales \
  zip \
  jpegoptim optipng pngquant gifsicle \
  vim \
  unzip \
  git \
  curl \
  libzip-dev \
  libonig-dev \
  graphviz \
  libicu-dev \
  libxml2-dev

# Bersihkan cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalasi ekstensi PHP yang umum digunakan
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Instal Composer (dependency manager untuk PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin file aplikasi ke dalam container
COPY . /var/www/html

# Setel izin yang benar untuk direktori storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 untuk PHP-FPM
EXPOSE 9000

# Perintah untuk menjalankan PHP-FPM
CMD ["php-fpm"]
