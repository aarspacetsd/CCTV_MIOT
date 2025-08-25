#!/bin/sh
set -e

# --- PERUBAHAN DI SINI ---
# Alih-alih menyalin file, kita akan membuat file .env secara dinamis
# dari variabel lingkungan yang disuntikkan oleh Docker Compose.
echo "Creating .env file from environment variables..."
printenv | grep -E '^(APP_|DB_|BROADCAST_|CACHE_|FILESYSTEM_|QUEUE_|SESSION_|MEMCACHED_|REDIS_|MAIL_|AWS_|PUSHER_|VITE_)' > .env

# Instal dependensi Composer
composer install --no-interaction --prefer-dist --optimize-autoloader

# Generate kunci aplikasi
php artisan key:generate

# Bersihkan cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Jalankan migrasi database
php artisan migrate --force

# Setel izin yang benar
chown -R www-data:www-data storage bootstrap/cache

echo "Setup completed. Starting PHP-FPM..."

# Jalankan proses utama (PHP-FPM)
exec php-fpm
