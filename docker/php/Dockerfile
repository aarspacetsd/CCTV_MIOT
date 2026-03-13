FROM php:8.2-fpm

# Set Timezone
ENV TZ=Asia/Jakarta

# Install dependensi sistem dasar
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js (LTS Version 18) untuk menjalankan Vite/NPM
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Penyesuaian Permission (Opsional untuk lingkungan Dev)
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Pastikan perintah dijalankan sebagai root untuk instalasi awal,
# pindah ke www-data hanya jika diperlukan saat runtime
USER www-data
