FROM php:8.2-fpm

# System deps - Tambahkan Node.js dan NPM untuk build aset frontend
RUN apt-get update && apt-get install -y ca-certificates curl gnupg \
  && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
  && apt-get install -y \
  git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libicu-dev \
  libxml2-dev libssl-dev libpq-dev zlib1g-dev libcurl4-openssl-dev netcat \
  nodejs \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
  && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Workdir
WORKDIR /var/www/html

# Salin semua file proyek
COPY . .

# Install dependensi PHP
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-progress

# Install dependensi Node.js dan build aset Vite
RUN npm install
RUN npm run build

# Entrypoint
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
