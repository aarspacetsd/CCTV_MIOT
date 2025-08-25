#!/bin/sh
set -e

# Membuat file .env dari environment variables yang disuntikkan oleh Docker
echo "Creating .env file from environment variables..."
printenv | grep -E '^(APP_|DB_|BROADCAST_|CACHE_|FILESYSTEM_|QUEUE_|SESSION_|MEMCACHED_|REDIS_|MAIL_|AWS_|PUSHER_|VITE_)' > .env

# HAPUS BARIS INI: composer install --no-interaction --prefer-dist --optimize-autoloader
# Dependensi sudah diinstal saat proses build Docker.

# Generate kunci aplikasi HANYA jika belum ada
if ! grep -q "APP_KEY=base64:.*" .env; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Bersihkan cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Jalankan migrasi database
php artisan migrate --force

# Setel izin yang benar (ini tetap penting)
chown -R www-data:www-data storage bootstrap/cache

echo "Setup completed. Starting PHP-FPM..."

# Jalankan proses utama (PHP-FPM)
exec "$@"
