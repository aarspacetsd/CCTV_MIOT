# ---------- Stage 1: build assets ----------
FROM node:20-alpine AS assets
WORKDIR /app

# Salin file yang dibutuhkan untuk Vite build (hindari bawa vendor/node_modules)
COPY package.json package-lock.json* yarn.lock* pnpm-lock.yaml* ./
# Kalau kamu pakai npm: 
RUN npm ci --no-audit --no-fund

# Salin source yang diperlukan Vite
# (resources/, public/ untuk base files, + file config Vite)
COPY resources ./resources
COPY public ./public
COPY vite.config.* ./
# Tambahkan jika kamu punya file konfigurasi tambahan
# COPY postcss.config.* tailwind.config.* ./

# Build Vite -> output ke public/build
RUN npm run build

# ---------- Stage 2: app + PHP (produksi) ----------
FROM webdevops/php-nginx:8.3 AS app
ENV TZ=Asia/Jakarta \
    WEB_DOCUMENT_ROOT=/app/public

WORKDIR /app

# Copy seluruh source (kecuali yang di-ignore .dockerignore)
COPY . /app

# Pastikan folder writable
RUN mkdir -p /app/storage /app/bootstrap/cache \
 && chown -R application:application /app

# Pakai user non-root
USER application

# Install dependency PHP (tanpa dev)
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Copy hasil build Vite dari stage assets
COPY --from=assets /app/public/build /app/public/build

# (Opsional) buat storage link; kalau gagal (misal di read-only FS), biarkan lanjut
RUN php artisan storage:link || true

# Jangan cache config/route di build-time (butuh .env runtime). Cache-nya jalankan di post-deploy Coolify.
EXPOSE 80
