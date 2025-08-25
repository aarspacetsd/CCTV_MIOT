# -------- base PHP-FPM 8.2 (Debian Bookworm) --------
FROM php:8.2-fpm

ENV DEBIAN_FRONTEND=noninteractive
WORKDIR /var/www/html

# OS deps (aman untuk non-interaktif) + locales
RUN set -eux; \
  apt-get update; \
  apt-get install -y --no-install-recommends \
  ca-certificates gnupg locales \
  build-essential \
  git curl unzip zip vim \
  libicu-dev \
  libxml2-dev \
  libzip-dev \
  libpng-dev \
  libjpeg62-turbo-dev \
  libfreetype6-dev \
  libwebp-dev \
  graphviz \
  netcat-openbsd \
  ; \
  sed -i '/en_US.UTF-8/s/^# //g' /etc/locale.gen && locale-gen; \
  rm -rf /var/lib/apt/lists/*

ENV LANG=en_US.UTF-8 \
  LANGUAGE=en_US:en \
  LC_ALL=en_US.UTF-8

# Configure & install PHP extensions
# - zip: butuh libzip-dev
# - intl: butuh libicu-dev
# - gd: butuh libjpeg62-turbo-dev, libfreetype6-dev, libpng-dev, libwebp-dev
RUN set -eux; \
  docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp; \
  docker-php-ext-install -j"$(nproc)" \
  pdo_mysql \
  mbstring \
  exif \
  pcntl \
  bcmath \
  gd \
  zip \
  intl \
  opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# (opsional) Optimasi layer: install vendor dulu biar cache efektif
COPY composer.json composer.lock ./
RUN set -eux; \
  composer install --no-interaction --no-scripts --no-dev --prefer-dist --no-progress

# Salin source code aplikasi
COPY . .

# Dump autoload
RUN composer dump-autoload --optimize

# Izin folder Laravel
RUN set -eux; \
  chown -R www-data:www-data storage bootstrap/cache; \
  chmod -R 775 storage bootstrap/cache

# Entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
