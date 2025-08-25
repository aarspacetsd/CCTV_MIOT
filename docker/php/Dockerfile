FROM php:8.2-fpm

# Set environment variables untuk non-interactive installation
ENV DEBIAN_FRONTEND=noninteractive
ENV APT_KEY_DONT_WARN_ON_DANGEROUS_USAGE=1

# System deps - Install dependensi dasar dengan retry logic
RUN set -eux; \
  apt-get update --fix-missing; \
  apt-get install -y --no-install-recommends --fix-broken \
  git \
  unzip \
  libzip-dev \
  libpng-dev \
  libjpeg-dev \
  libfreetype6-dev \
  libonig-dev \
  libicu-dev \
  libxml2-dev \
  libssl-dev \
  libpq-dev \
  zlib1g-dev \
  libcurl4-openssl-dev \
  netcat-traditional \
  ca-certificates \
  curl \
  gnupg \
  lsb-release; \
  apt-get autoremove -y; \
  apt-get clean; \
  rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
  pdo_mysql \
  mbstring \
  exif \
  pcntl \
  bcmath \
  gd \
  zip \
  intl \
  opcache

# Install Node.js using NodeSource repository (lebih reliable)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
  && apt-get install -y nodejs \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Workdir
WORKDIR /var/www/html

# Salin composer files dulu untuk cache layer
COPY composer.json composer.lock ./

# Install dependensi PHP
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-progress --no-scripts

# Salin package.json untuk cache layer
COPY package*.json ./

# Install dependensi Node.js
RUN npm ci --only=production

# Salin semua file proyek
COPY . .

# Finalize composer install
RUN composer dump-autoload --optimize

# Build aset Vite
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 755 /var/www/html \
  && chmod -R 775 /var/www/html/storage \
  && chmod -R 775 /var/www/html/bootstrap/cache

# Entrypoint
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
