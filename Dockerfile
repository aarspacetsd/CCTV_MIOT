# Dockerfile (root repo)
FROM webdevops/php-nginx:8.3

# Timezone & ext yang umum (opsional)
ENV TZ=Asia/Jakarta

WORKDIR /app
COPY . /app

# Pastikan folder writable
RUN mkdir -p /app/storage /app/bootstrap/cache \
 && chown -R application:application /app

# Dokumen root ke public/
ENV WEB_DOCUMENT_ROOT=/app/public

# Non-root for security
USER application

# Composer setup (gunakan composer di image ini)
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Optional: Build asset jika pakai Vite
# Hapus jika kamu deploy asset via CI/CD
# Ganti 'npm ci' ke 'npm install' kalau belum lockfile
# RUN npm ci && npm run build

# Cache Laravel (tanpa migrate di tahap build!)
RUN php artisan key:generate --force || true \
 && php artisan storage:link || true \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

EXPOSE 80
