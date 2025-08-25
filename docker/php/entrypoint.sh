#!/usr/bin/env bash
set -e

# Pastikan folder penting ada (ketika src kosong atau baru)
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache

# Permission untuk Laravel
# Perintah ini sekarang akan berhasil karena skrip dijalankan sebagai root
chown -R www-data:www-data /var/www/html
chmod -R ug+rwx storage bootstrap/cache

# Jalankan CMD asli dari image php-fpm (misalnya, "php-fpm").
# Skrip 'docker-php-entrypoint' akan secara otomatis menurunkan hak akses
# dan menjalankan proses sebagai user 'www-data'.
exec docker-php-entrypoint "$@"
