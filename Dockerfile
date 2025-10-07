# ---------- Stage 1: Build assets ----------
FROM node:20-alpine AS assets
WORKDIR /app

# Salin & install deps JS (pakai cache layer yang efisien)
COPY package.json package-lock.json* yarn.lock* pnpm-lock.yaml* ./
# npm ci kalau ada lockfile, fallback ke npm install kalau tidak ada
RUN if [ -f package-lock.json ]; then \
      npm ci --no-audit --no-fund ; \
    else \
      npm install --no-audit --no-fund ; \
    fi

# Salin source untuk Vite
COPY resources ./resources
COPY public ./public
COPY vite.config.* ./
# COPY postcss.config.* tailwind.config.* ./

# Build Vite -> output default ke public/build
RUN npm run build


# ---------- Stage 2: PHP + Nginx (Production) ----------
FROM webdevops/php-nginx:8.3 AS app

ENV TZ=Asia/Jakarta \
    WEB_DOCUMENT_ROOT=/app/public

WORKDIR /app

# 1) Composer install pakai cache layer
#    (copy file composer dulu supaya layer vendor bisa di-cache)
COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# 2) Copy sisa source kode (vendor akan tetap dari layer sebelumnya)
COPY . /app

# 3) Copy hasil build Vite dari stage assets
COPY --from=assets /app/public/build /app/public/build

# 4) Pastikan SEMUA folder runtime ADA & writable
#    (jangan mengandalkan bind mount/CI — ini dibuat langsung di image)
RUN set -eux; \
    mkdir -p \
      /app/bootstrap/cache \
      /app/storage/app/public \
      /app/storage/framework/cache/data \
      /app/storage/framework/sessions \
      /app/storage/framework/views; \
    chown -R application:application /app; \
    chmod -R ug+rwX /app/storage /app/bootstrap/cache

# 5) Pakai user non-root
USER application

# 6) (Opsional) bikin storage symlink; kalau gagal, lanjut saja
RUN php artisan storage:link || true

# NOTE:
# - JANGAN jalankan config:cache/route:cache di build-time; lakukan di post-deploy (runtime).
# - Post-deploy 1-liner yang kemarin sudah aman & idempotent.

EXPOSE 80

# (Opsional) healthcheck sederhana
HEALTHCHECK --interval=30s --timeout=5s --retries=3 CMD curl -fsS http://127.0.0.1/health || exit 1
