#!/usr/bin/env bash
set -e

# Pindah ke direktori kerja
cd /var/www/html

# Jika file .env tidak ada, buat dari .env.example
if [ ! -f ".env" ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env

    # Generate APP_KEY setelah .env dibuat
    echo "Generating application key..."
    php artisan key:generate
fi

# Jalankan migrasi database untuk memastikan tabel sudah ada
# Opsi --force diperlukan karena ini lingkungan non-interaktif
echo "Running database migrations..."
php artisan migrate --force

# Optimasi untuk produksi dengan membuat cache
echo "Caching configuration and routes for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Perbaiki izin untuk folder storage dan bootstrap/cache
echo "Fixing permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Jalankan perintah utama dari Dockerfile (yaitu "php-fpm")
echo "Handing over to main container command..."
exec docker-php-entrypoint "$@"
