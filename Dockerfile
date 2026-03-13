FROM php:8.2-fpm

ENV TZ=Asia/Jakarta

# Install system dependencies
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

# Install Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install JS dependencies & build assets
COPY package.json package-lock.json* yarn.lock* ./
RUN npm install --no-audit --no-fund

COPY vite.config.* ./
COPY resources ./resources
COPY public ./public

RUN npm run build

# Fix permissions
RUN usermod -u 1000 www-data && groupmod -g 1000 www-
