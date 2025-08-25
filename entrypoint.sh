#!/bin/sh
set -e

# Salin file environment jika .env belum ada
if [ ! -f ".env" ]; then
    cp .env.docker .env
fi

# Instal dependensi Composer
# Opsi --no-scripts mencegah error jika APP_KEY belum ada
composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Buat kunci aplikasi (APP_KEY) jika belum ada
php artisan key:generate

# Jalankan migrasi database
php artisan migrate --force

# Bersihkan dan cache konfigurasi untuk produksi
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Setup completed. Starting PHP-FPM..."

# Jalankan perintah asli yang dikirim ke container (yaitu, php-fpm)
exec "$@"
