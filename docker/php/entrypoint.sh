#!/usr/bin/env bash
set -e

# Pindah ke direktori kerja
cd /var/www/html

# Hanya lakukan hal-hal penting untuk startup
# Jika file .env tidak ada, buat dari .env.example dan generate key
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cp .env.example .env

    # PERBAIKAN: Pastikan baris APP_KEY ada sebelum men-generate
    echo "Ensuring APP_KEY is set..."
    grep -q "APP_KEY=" .env || echo "APP_KEY=" >> .env
    php artisan key:generate
fi

# PERBAIKAN: Tambahkan 'chown' untuk memastikan www-data memiliki kepemilikan folder
echo "Fixing ownership and permissions for storage and cache..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Jalankan perintah utama dari Dockerfile (yaitu "php-fpm")
# Skrip 'docker-php-entrypoint' akan memulai php-fpm sebagai www-data
echo "Handing over to main container command (php-fpm)..."
exec docker-php-entrypoint "$@"
