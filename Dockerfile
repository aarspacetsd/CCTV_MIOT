# Dockerfile (root repo)
FROM webdevops/php-nginx:8.3

# Timezone (opsional)
ENV TZ=Asia/Jakarta

WORKDIR /app

# Copy composer files dulu supaya cache install dependencies lebih efektif
COPY composer.json composer.lock ./
# Install deps dulu (lebih cepat berkat cache layer)
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-autoloader

# Baru copy source code
COPY . /app

# Pastikan folder writable
RUN mkdir -p /app/storage /app/bootstrap/cache \
 && chown -R application:application /app

# Document root ke public/
ENV WEB_DOCUMENT_ROOT=/app/public

# Non-root user
USER application

# Optimize autoload setelah source dicopy
RUN composer dump-autoload --optimize

# ❌ JANGAN jalankan perintah artisan di build-stage (pindahkan ke Post-deploy Coolify)
# Jika butuh build aset Vite dan image ini ada Node, baris ini bisa diaktifkan:
# RUN npm ci && npm run build

EXPOSE 80
