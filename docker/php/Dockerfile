FROM php:8.2-fpm

# Hindari prompt interaktif saat apt
ARG DEBIAN_FRONTEND=noninteractive

# System deps (tanpa libonig-dev), Node.js 20 dari NodeSource
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
  ca-certificates curl gnupg git unzip libzip-dev \
  libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
  libicu-dev libxml2-dev libssl-dev libpq-dev zlib1g-dev \
  libcurl4-openssl-dev netcat-openbsd \
  && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
  && apt-get update \
  && apt-get install -y --no-install-recommends nodejs \
  # Siapkan ekstensi PHP
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
  # Bersih-bersih layer apt
  && apt-get purge -y --auto-remove \
  && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Konfigurasi PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Workdir
WORKDIR /var/www/html

# Salin project
COPY . .

# Install dependency PHP
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-progress

# Install dependency Node & build aset (pakai npm ci bila ada package-lock.json)
# Fallback ke npm install kalau ci gagal (mis. tidak ada lockfile)
RUN npm ci --no-audit --no-fund || npm install --no-audit --no-fund \
  && npm run build

# Entrypoint
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
