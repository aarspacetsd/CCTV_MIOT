FROM nginx:alpine

# Install fcgi untuk healthcheck yang lebih andal
RUN apk add --no-cache fcgi

# Salin file konfigurasi Nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html

# PERBAIKAN: Salin folder public dari proyek Anda
# Ini termasuk file CSS/JS hasil build dan link storage
COPY public ./public

EXPOSE 80
