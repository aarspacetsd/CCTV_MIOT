# --- STAGE 1: Install dependensi Composer ---
    FROM composer:2 as composer_builder
    WORKDIR /app
    COPY database/ database/
    COPY composer.json composer.lock ./
    RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist --no-dev --optimize-autoloader

    # --- STAGE 2: Build aset frontend (Vite/NPM) ---
    FROM node:18 as npm_builder
    WORKDIR /app
    COPY package.json package-lock.json ./
    RUN npm install
    COPY . .
    RUN npm run build

    # --- STAGE 3: Final Production Image ---
    FROM php:8.2-fpm-alpine

    # Install dependensi sistem yang dibutuhkan Laravel
    RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        libxml2-dev

    # Install ekstensi PHP yang umum digunakan
    RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install \
        pdo_mysql \
        zip \
        gd \
        exif \
        pcntl \
        bcmath \
        sockets

    WORKDIR /var/www/html

    # Salin file dari stage sebelumnya
    COPY --from=composer_builder /app/vendor/ ./vendor/
    COPY --from=npm_builder /app/public/ ./public/
    COPY --from=npm_builder /app/public/build/ ./public/build/
    COPY --from=npm_builder /app/resources/ ./resources/
    COPY --from=npm_builder /app/storage/ ./storage/
    COPY --from=npm_builder /app/bootstrap/ ./bootstrap/
    COPY --from=npm_builder /app/config/ ./config/
    COPY --from=npm_builder /app/routes/ ./routes/
    COPY --from=npm_builder /app/app/ ./app/
    COPY --from=npm_builder /app/.env.example ./.env
    COPY --from=npm_builder /app/artisan ./artisan

    # Salin konfigurasi Nginx dan script startup
    COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
    COPY docker/start.sh /usr/local/bin/start.sh
    RUN chmod +x /usr/local/bin/start.sh

    # Atur kepemilikan file agar server web bisa menulis
    RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

    EXPOSE 80

    ENTRYPOINT ["/usr/local/bin/start.sh"]