FROM php:8.2-fpm

ENV DEBIAN_FRONTEND=noninteractive
WORKDIR /var/www/html

# OS deps lengkap untuk build ekstensi
RUN set -eux; \
  apt-get update; \
  apt-get install -y --no-install-recommends \
  ca-certificates gnupg locales \
  build-essential pkg-config \
  git curl unzip zip \
  libicu-dev \
  libxml2-dev \
  libzip-dev zlib1g-dev \
  libpng-dev \
  libjpeg62-turbo-dev \
  libfreetype6-dev \
  libwebp-dev \
  graphviz \
  netcat-openbsd \
  ; \
  sed -i '/en_US.UTF-8/s/^# //g' /etc/locale.gen && locale-gen; \
  rm -rf /var/lib/apt/lists/*

ENV LANG=en_US.UTF-8 LANGUAGE=en_US:en LC_ALL=en_US.UTF-8

# Konfigurasi & install ekstensi (batasi paralelisme ke -j1)
RUN set -eux; \
  docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp; \
  docker-php-ext-install -j1 pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# (opsional) optimasi layer vendor
COPY composer.json composer.lock ./
RUN set -eux; composer install --no-interaction --no-scripts --no-dev --prefer-dist --no-progress || true

COPY . .

RUN composer dump-autoload --optimize

RUN set -eux; \
  chown -R www-data:www-data storage bootstrap/cache; \
  chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
