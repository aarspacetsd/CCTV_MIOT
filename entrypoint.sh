#!/bin/sh
set -e

# Membuat file .env dari environment variables yang disuntikkan oleh Docker
echo "Creating .env file from environment variables..."
printenv | grep -E '^(APP_|DB_|BROADCAST_|CACHE_|FILESYSTEM_|QUEUE_|SESSION_|MEMCACHED_|REDIS_|MAIL_|AWS_|PUSHER_|VITE_)' > .env

# --- TAMBAHAN: Menunggu Database Siap ---
# Ambil host dan port dari environment variables
DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
DB_PORT=$(grep DB_PORT .env | cut -d '=' -f2)

echo "Waiting for database at $DB_HOST:$DB_PORT..."
# Loop sampai koneksi ke host dan port database berhasil
while ! nc -z $DB_HOST $DB_PORT; do
  sleep 1 # tunggu 1 detik sebelum mencoba lagi
done
echo "Database is ready."
# --- AKHIR TAMBAHAN ---

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
echo "Running database migrations..."
php artisan migrate --force

# Setel izin yang benar (ini tetap penting)
chown -R www-data:www-data storage bootstrap/cache

echo "Setup completed. Starting PHP-FPM..."

# Jalankan proses utama (misalnya: php-fpm)
exec "$@"
