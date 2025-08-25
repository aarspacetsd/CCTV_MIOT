FROM nginx:alpine

# TAMBAHKAN BARIS INI: Install paket untuk healthcheck yang lebih baik
RUN apk add --no-cache fcgi

# Salin file konfigurasi Nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html

# Salin folder public dari proyek Laravel Anda
COPY public ./public

EXPOSE 80
