FROM nginx:alpine

# PERBAIKAN: Path lengkap dari root context
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html

# Salin folder public dari proyek Laravel Anda
COPY public ./public

EXPOSE 80
