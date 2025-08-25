#!/bin/sh
set -e

# --- PERUBAHAN DI SINI ---
# Salin dari .env.example (yang ada di repo Anda) jika .env belum ada
if [ ! -f ".env" ]; then
    echo "Creating .env file from .env.example"
    cp .env.example .env
fi

# Instal dependensi Composer
# Opsi --no-scripts mencegah error jika APP_KEY belum ada
composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Buat kunci aplikasi (APP_KEY)
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
