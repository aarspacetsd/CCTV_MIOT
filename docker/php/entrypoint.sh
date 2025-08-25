#!/usr/bin/env bash
set -e

# Pindah ke direktori kerja
cd /var/www/html

# Hanya lakukan hal-hal penting untuk startup
# Jika file .env tidak ada, buat dari .env.example dan generate key
if [ ! -f ".env" ]; then
    echo "Creating .env file and generating key..."
    cp .env.example .env
    php artisan key:generate
fi

# Perbaiki izin HANYA untuk folder yang diperlukan agar Laravel tidak error
# chown akan dijalankan oleh docker-php-entrypoint
echo "Fixing permissions for storage and cache..."
chmod -R 775 storage bootstrap/cache

# Jalankan perintah utama dari Dockerfile (yaitu "php-fpm")
# Skrip 'docker-php-entrypoint' akan memulai php-fpm sebagai www-data
echo "Handing over to main container command (php-fpm)..."
exec docker-php-entrypoint "$@"
