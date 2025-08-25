FROM php:8.2-fpm-alpine

RUN apk add --no-cache bash git curl icu-dev zlib-dev libpng-dev oniguruma-dev \
  && docker-php-ext-install intl pdo_mysql mbstring exif pcntl bcmath gd

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN php artisan storage:link && php artisan config:cache && php artisan route:cache

CMD ["php-fpm"]
