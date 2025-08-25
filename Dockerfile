FROM php:8.2-fpm as base

# Install system dependencies
RUN apt-get update && apt-get install -y \
  libpng-dev \
  libjpeg-dev \
  libfreetype6-dev \
  libzip-dev \
  libonig-dev \
  libicu-dev \
  libxml2-dev \
  && rm -rf /var/lib/apt/lists/*

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
  intl

FROM base as development

# Install development tools
RUN apt-get update && apt-get install -y \
  git \
  curl \
  vim \
  unzip \
  netcat-traditional \
  && rm -rf /var/lib/apt/lists/*

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-interaction --prefer-dist

# Copy application
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
