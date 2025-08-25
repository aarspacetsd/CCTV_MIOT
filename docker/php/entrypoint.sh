#!/usr/bin/env bash
set -e

# Pastikan folder penting ada (ketika src kosong atau baru)
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache || true

# Permission untuk Laravel
chown -R www-data:www-data /var/www/html || true
chmod -R ug+rwx storage bootstrap/cache || true

# Endpoint health sederhana
mkdir -p public
echo "OK" > public/health

exec "$@"
